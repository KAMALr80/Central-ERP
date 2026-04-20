<?php
// app/Http/Controllers/Sales/SalesController.php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerWallet;
use App\Models\Payment;
use App\Models\EmiPlan;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Services\GoogleMapsService;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    protected $googleMaps;
    protected $shipmentService;

    public function __construct(GoogleMapsService $googleMaps, ShipmentService $shipmentService)
    {
        $this->googleMaps = $googleMaps;
        $this->shipmentService = $shipmentService;
    }

    const PAYMENT_REMARKS = [
        'INVOICE' => 'Direct Invoice Payment',
        'EMI_DOWN' => 'EMI Down Payment',
        'ADVANCE_USED' => 'Wallet Used for Invoice',
        'EXCESS_TO_ADVANCE' => 'Excess to Wallet',
        'ADVANCE_ONLY' => 'Pure Advance Payment',
        'WALLET_ADD' => 'Wallet Add',
        'INVOICE_DUE' => 'Marked as Due'
    ];

    public function sendInvoice(Request $request)
    {
        try {
            $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'recipient_email' => 'required|email',
                'email_subject' => 'required|string|max:255',
                'email_body' => 'nullable|string'
            ]);

            $sale = Sale::with(['customer', 'items.product'])->findOrFail($request->sale_id);
            $pdf = Pdf::loadView('sales.invoice-pdf', [
                'sale' => $sale,
                'amountInWords' => $this->numberToWords($sale->grand_total)
            ]);

            $subject = $request->email_subject;
            $body = $request->email_body ?? "Dear {$sale->customer->name},\n\nPlease find attached the invoice for your recent purchase.\n\nThank you for your business!";

            Mail::raw($body, function ($message) use ($request, $pdf, $subject, $sale) {
                $message->to($request->recipient_email)
                        ->subject($subject)
                        ->attachData($pdf->output(), "Invoice_{$sale->invoice_no}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });

            Log::info("Single email sent successfully", [
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'recipient' => $request->recipient_email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Email validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Single email failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkSendInvoice(Request $request)
    {
        try {
            $request->validate([
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'exists:sales,id',
                'recipient_email' => 'required|email',
                'email_subject_prefix' => 'required|string|max:255',
                'email_body_template' => 'nullable|string'
            ]);

            $invoiceIds = $request->invoice_ids;
            $results = [
                'total' => count($invoiceIds),
                'success' => 0,
                'failed' => 0,
                'failed_invoices' => []
            ];

            $template = $request->email_body_template ?? "Dear {customer_name},\n\nPlease find attached the invoice #{invoice_no} for your recent purchase.\n\nInvoice Details:\nDate: {invoice_date}\nAmount: ₹{amount}\nDue: ₹{due}\n\nThank you for your business!";

            foreach ($invoiceIds as $saleId) {
                try {
                    $sale = Sale::with(['customer', 'items.product'])->find($saleId);
                    if (!$sale) {
                        $results['failed']++;
                        $results['failed_invoices'][] = "ID: {$saleId} - Not found";
                        continue;
                    }

                    $totalPaid = $sale->payments()
                        ->where('status', 'paid')
                        ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
                        ->sum('amount');
                    $dueAmount = $sale->grand_total - $totalPaid;

                    $pdf = Pdf::loadView('sales.invoice-pdf', [
                        'sale' => $sale,
                        'amountInWords' => $this->numberToWords($sale->grand_total)
                    ]);

                    $subject = $request->email_subject_prefix . " - Invoice #{$sale->invoice_no}";
                    $body = str_replace(
                        ['{customer_name}', '{invoice_no}', '{invoice_date}', '{amount}', '{due}'],
                        [
                            $sale->customer->name ?? 'Customer',
                            $sale->invoice_no,
                            $sale->sale_date->format('d M Y'),
                            number_format($sale->grand_total, 2),
                            number_format($dueAmount, 2)
                        ],
                        $template
                    );

                    Mail::raw($body, function ($message) use ($request, $pdf, $subject, $sale) {
                        $message->to($request->recipient_email)
                                ->subject($subject)
                                ->attachData($pdf->output(), "Invoice_{$sale->invoice_no}.pdf", [
                                    'mime' => 'application/pdf',
                                ]);
                    });

                    $results['success']++;
                    Log::info("Bulk email sent for invoice #{$sale->invoice_no}");

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['failed_invoices'][] = "Invoice #{$sale->invoice_no}: " . $e->getMessage();
                    Log::error("Bulk email failed for sale ID {$saleId}: " . $e->getMessage());
                }
            }

            $message = "Emails sent: {$results['success']} successful, {$results['failed']} failed";
            return response()->json([
                'success' => $results['success'] > 0,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Bulk email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk emails: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendDueReminder(Request $request)
    {
        try {
            $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'recipient_email' => 'required|email'
            ]);

            $sale = Sale::with('customer')->findOrFail($request->sale_id);

            $totalPaid = $sale->payments()
                ->where('status', 'paid')
                ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
                ->sum('amount');
            $dueAmount = $sale->grand_total - $totalPaid;

            $pdf = Pdf::loadView('sales.invoice-pdf', [
                'sale' => $sale,
                'amountInWords' => $this->numberToWords($sale->grand_total)
            ]);

            $subject = "Payment Reminder: Invoice #{$sale->invoice_no} - Due: ₹" . number_format($dueAmount, 2);
            $body = "Dear {$sale->customer->name},\n\n"
                  . "This is a friendly reminder that you have an outstanding invoice.\n\n"
                  . "Invoice #: {$sale->invoice_no}\n"
                  . "Date: {$sale->sale_date->format('d M Y')}\n"
                  . "Total Amount: ₹" . number_format((float)$sale->grand_total, 2) . "\n"
                  . "Due Amount: ₹" . number_format((float)$dueAmount, 2) . "\n\n"
                  . "Please make the payment at your earliest convenience.\n\n"
                  . "Thank you for your business!";

            Mail::raw($body, function ($message) use ($request, $pdf, $subject, $sale) {
                $message->to($request->recipient_email)
                        ->subject($subject)
                        ->attachData($pdf->output(), "Invoice_{$sale->invoice_no}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });

            return response()->json([
                'success' => true,
                'message' => "Reminder sent for Invoice #{$sale->invoice_no}"
            ]);

        } catch (\Exception $e) {
            Log::error('Due reminder failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkSendDueReminders(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'exists:sales,id',
                'recipient_email' => 'required|email'
            ]);

            $customer = Customer::find($request->customer_id);
            $invoiceIds = $request->invoice_ids;

            $results = [
                'total' => count($invoiceIds),
                'success' => 0,
                'failed' => 0,
                'total_due' => 0
            ];

            $invoiceList = [];

            foreach ($invoiceIds as $saleId) {
                try {
                    $sale = Sale::with('customer')->find($saleId);
                    if (!$sale) {
                        $results['failed']++;
                        continue;
                    }

                    $totalPaid = $sale->payments()
                        ->where('status', 'paid')
                        ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
                        ->sum('amount');
                    $dueAmount = $sale->grand_total - $totalPaid;

                    $results['total_due'] += $dueAmount;
                    $invoiceList[] = [
                        'no' => $sale->invoice_no,
                        'date' => $sale->sale_date->format('d M Y'),
                        'total' => $sale->grand_total,
                        'due' => $dueAmount
                    ];

                    $pdf = Pdf::loadView('sales.invoice-pdf', [
                        'sale' => $sale,
                        'amountInWords' => $this->numberToWords($sale->grand_total)
                    ]);

                    $subject = "Payment Reminder: Invoice #{$sale->invoice_no} - Due: ₹" . number_format($dueAmount, 2);
                    $body = "Dear {$customer->name},\n\n"
                          . "This is a friendly reminder that you have an outstanding invoice.\n\n"
                          . "Invoice #: {$sale->invoice_no}\n"
                          . "Date: {$sale->sale_date->format('d M Y')}\n"
                          . "Total Amount: ₹" . number_format($sale->grand_total, 2) . "\n"
                          . "Due Amount: ₹" . number_format($dueAmount, 2) . "\n\n"
                          . "Please make the payment at your earliest convenience.\n\n"
                          . "Thank you for your business!";

                    Mail::raw($body, function ($message) use ($request, $pdf, $subject, $sale) {
                        $message->to($request->recipient_email)
                                ->subject($subject)
                                ->attachData($pdf->output(), "Invoice_{$sale->invoice_no}.pdf", [
                                    'mime' => 'application/pdf',
                                ]);
                    });

                    $results['success']++;

                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Bulk due reminder failed for sale ID {$saleId}: " . $e->getMessage());
                }
            }

            $message = "Reminders sent: {$results['success']} successful, {$results['failed']} failed";
            return response()->json([
                'success' => $results['success'] > 0,
                'message' => $message,
                'results' => $results,
                'invoice_list' => $invoiceList
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk due reminders failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    private function numberToWords($num)
    {
        $ones = [
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
            60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        ];

        $num = (int)$num;

        if ($num < 20) return $ones[$num];
        if ($num < 100) return $ones[floor($num/10)*10] . ($num%10 > 0 ? ' ' . $ones[$num%10] : '');
        if ($num < 1000) return $ones[floor($num/100)] . ' Hundred' . ($num%100 > 0 ? ' ' . $this->numberToWords($num%100) : '');
        if ($num < 100000) return $this->numberToWords(floor($num/1000)) . ' Thousand' . ($num%1000 > 0 ? ' ' . $this->numberToWords($num%1000) : '');
        if ($num < 10000000) return $this->numberToWords(floor($num/100000)) . ' Lakh' . ($num%100000 > 0 ? ' ' . $this->numberToWords($num%100000) : '');
        return $this->numberToWords(floor($num/10000000)) . ' Crore' . ($num%10000000 > 0 ? ' ' . $this->numberToWords($num%10000000) : '');
    }

    public function datatable()
    {
        $sales = Sale::with('customer')->select('sales.*');

        return DataTables::of($sales)
            ->addColumn('customer_name', function ($sale) {
                return $sale->customer->name ?? 'N/A';
            })
            ->addColumn('customer_mobile', function ($sale) {
                return $sale->customer->mobile ?? 'N/A';
            })
            ->addColumn('status_badge', function ($sale) {
                $badgeClass = match ($sale->payment_status) {
                    'paid' => 'success',
                    'partial' => 'warning',
                    'emi' => 'info',
                    default => 'danger'
                };
                return "<span class='badge bg-{$badgeClass}'>{$sale->payment_status}</span>";
            })
            ->addColumn('actions', function ($sale) {
                return view('sales.partials.actions', compact('sale'))->render();
            })
            ->addColumn('amount_formatted', function ($sale) {
                return '₹' . number_format($sale->grand_total, 2);
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function stats()
    {
        $totalInvoices = Sale::count();
        $totalRevenue = Sale::sum('grand_total');
        $totalPaid = Payment::where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');
        $totalDue = $totalRevenue - $totalPaid;

        $recentSales = Sale::with('customer')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'customer' => $sale->customer->name ?? 'N/A',
                    'amount' => $sale->grand_total,
                    'status' => $sale->payment_status,
                    'date' => $sale->sale_date->format('d M Y'),
                    'requires_shipping' => $sale->requires_shipping,
                    'shipment_count' => $sale->shipments->count()
                ];
            });

        return response()->json([
            'total_invoices' => $totalInvoices,
            'total_revenue' => $totalRevenue,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'recent_sales' => $recentSales,
            'collection_rate' => $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100, 2) : 0,
            'total_shipments' => Shipment::count(),
            'pending_shipments' => Shipment::where('status', 'pending')->count()
        ]);
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:customers,mobile',
            'email'  => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'gst_no' => 'nullable|string|max:50'
        ]);

        try {
            $customer = Customer::create([
                'name'    => $request->name,
                'mobile'  => $request->mobile,
                'email'   => $request->email,
                'address' => $request->address,
                'gst_no'  => $request->gst_no,
                'open_balance' => 0,
                'wallet_balance' => 0
            ]);

            Log::info("New customer created via AJAX: ID={$customer->id}, Name={$customer->name}");

            return response()->json([
                'success'  => true,
                'customer' => $customer,
                'message'  => 'Customer created successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('AJAX Customer Create Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Sale::with('customer', 'shipments');

        if ($request->status && $request->status != 'all') {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('requires_shipping') && $request->requires_shipping !== '') {
            $query->where('requires_shipping', $request->requires_shipping);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_no', 'LIKE', "%{$request->search}%")
                    ->orWhereHas('customer', function ($cq) use ($request) {
                        $cq->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('mobile', 'LIKE', "%{$request->search}%");
                    });
            });
        }

        if ($request->from_date) {
            $query->whereDate('sale_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('sale_date', '<=', $request->to_date);
        }

        $sales = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Sale::count(),
            'paid' => Sale::where('payment_status', 'paid')->count(),
            'partial' => Sale::where('payment_status', 'partial')->count(),
            'unpaid' => Sale::where('payment_status', 'unpaid')->count(),
            'emi' => Sale::where('payment_status', 'emi')->count(),
            'shipping_required' => Sale::where('requires_shipping', true)->count(),
            'shipped' => Sale::whereHas('shipments')->count()
        ];

        return view('sales.index', compact('sales', 'stats', 'request'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products  = Product::where('quantity', '>', 0)->orderBy('name')->get();
        $invoice_token = uniqid() . '_' . time();

        return view('sales.create', compact('customers', 'products', 'invoice_token'));
    }

public function store(Request $request)
{
    Log::info('🚀 SALES STORE METHOD CALLED', $request->all());

    $validator = Validator::make($request->all(), [
        'customer_id'        => 'required|exists:customers,id',
        'invoice_token'      => 'required|string|unique:sales,invoice_token',
        'items.product_id'   => 'required|array|min:1',
        'items.quantity'     => 'required|array|min:1',
        'items.price'        => 'required|array|min:1',
        'discount'           => 'nullable|numeric|min:0',
        'tax'                => 'nullable|numeric|min:0|max:100',
        'requires_shipping'  => 'nullable|boolean',
        'shipping_address'   => 'nullable|string|max:500',
        'city'               => 'nullable|string|max:100',
        'state'              => 'nullable|string|max:100',
        'pincode'            => 'nullable|string|max:20',
        'receiver_name'      => 'nullable|string|max:255',
        'receiver_phone'     => 'nullable|string|max:20',
        'delivery_instructions' => 'nullable|string|max:500',
        'destination_latitude'  => 'nullable|numeric|between:-90,90',
        'destination_longitude' => 'nullable|numeric|between:-180,180',
        'place_id' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        Log::error('❌ VALIDATION FAILED', $validator->errors()->toArray());
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        $sale = null;

        DB::transaction(function () use ($request, &$sale) {
            // Calculate totals
            $subTotal = $this->calculateSubTotal($request->items);
            $discount = (float) ($request->discount ?? 0);
            $tax = (float) ($request->tax ?? 0);
            $taxAmount = ($subTotal - $discount) * $tax / 100;
            $grandTotal = ($subTotal - $discount) + $taxAmount;

            // Get customer
            $customer = Customer::lockForUpdate()->findOrFail($request->customer_id);

            // ✅ FIX: Generate unique invoice number with lock
            $lastSale = Sale::lockForUpdate()->orderBy('id', 'desc')->first();
            $lastId = $lastSale ? $lastSale->id : 0;
            $invoiceNo = 'INV-' . date('Y') . '-' . str_pad(($lastId + 1), 6, '0', STR_PAD_LEFT);

            // Prepare shipping data
            $shippingData = [];

            if ($request->boolean('requires_shipping')) {
                $city = $request->city;
                $state = $request->state;
                $pincode = $request->pincode;

                if ($request->destination_latitude && $request->destination_longitude &&
                    (empty($city) || empty($state))) {
                    try {
                        $geocoded = $this->googleMaps->reverseGeocode(
                            $request->destination_latitude,
                            $request->destination_longitude
                        );
                        if ($geocoded && isset($geocoded['components'])) {
                            $city = $city ?? ($geocoded['components']['city'] ?? null);
                            $state = $state ?? ($geocoded['components']['state'] ?? null);
                            $pincode = $pincode ?? ($geocoded['components']['postal_code'] ?? null);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Reverse geocoding failed: ' . $e->getMessage());
                    }
                }

                $shippingData = [
                    'requires_shipping' => true,
                    'shipping_address' => $request->shipping_address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'delivery_instructions' => $request->delivery_instructions,
                    'destination_latitude' => $request->destination_latitude,
                    'destination_longitude' => $request->destination_longitude,
                    'place_id' => $request->place_id,
                    'shipping_status' => 'pending',
                ];
            } else {
                $shippingData = [
                    'requires_shipping' => false,
                    'shipping_address' => null,
                    'city' => null,
                    'state' => null,
                    'pincode' => null,
                    'receiver_name' => null,
                    'receiver_phone' => null,
                    'delivery_instructions' => null,
                    'destination_latitude' => null,
                    'destination_longitude' => null,
                    'place_id' => null,
                    'shipping_status' => null,
                ];
            }

            // Create sale
            $sale = Sale::create(array_merge([
                'customer_id'    => $customer->id,
                'invoice_no'     => $invoiceNo,
                'invoice_token'  => $request->invoice_token,
                'sale_date'      => now(),
                'sub_total'      => $subTotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'tax_amount'     => $taxAmount,
                'grand_total'    => $grandTotal,
                'payment_status' => 'unpaid',
                'paid_amount'    => 0
            ], $shippingData));

            // Create sale items (rest of your code...)
            foreach ($request->items['product_id'] as $i => $productId) {
                $qty = (int) $request->items['quantity'][$i];
                $price = (float) $request->items['price'][$i];

                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->quantity < $qty) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->quantity}");
                }

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'total'      => $qty * $price,
                    'mrp'        => $product->mrp ?? $price
                ]);

                $product->decrement('quantity', $qty);
            }

            // Create shipment if needed
            if ($request->boolean('requires_shipping') && !empty($request->shipping_address)) {
                try {
                    $shipment = $this->createShipmentFromSale($sale, $request);
                    if ($shipment) {
                        $sale->shipping_status = 'shipment_created';
                        $sale->save();
                    }
                } catch (\Exception $e) {
                    Log::error("❌ Shipment creation failed: " . $e->getMessage());
                }
            }

            Log::info("✅ Sale created successfully", [
                'id' => $sale->id,
                'invoice' => $sale->invoice_no
            ]);
        });

        // Success - redirect to invoice view
        return redirect()->route('sales.index', $sale->id)
            ->with('success', 'Sale created successfully. Invoice #' . $sale->invoice_no);

    } catch (\Exception $e) {
        Log::error('❌ Sale Store Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating sale: ' . $e->getMessage());
    }
}
private function createShipmentFromSale($sale, $request)
{
    try {
        // ✅ DEBUG: Log input data
        Log::info('🔍 [DEBUG] Starting createShipmentFromSale', [
            'sale_id' => $sale->id,
            'sale_invoice' => $sale->invoice_no,
            'requires_shipping' => $request->boolean('requires_shipping'),
            'shipping_address' => $request->shipping_address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'has_coordinates' => !is_null($request->destination_latitude)
        ]);

        $receiverName = $request->receiver_name ?? $sale->customer->name;
        $receiverPhone = $request->receiver_phone ?? $sale->customer->mobile;

        $shipment = new Shipment();

        // ✅ DEBUG: Check if generateShipmentNumber exists
        if (!method_exists($shipment, 'generateShipmentNumber')) {
            Log::error('❌ generateShipmentNumber method not found in Shipment model');
            throw new \Exception('generateShipmentNumber method not found');
        }

        if (!method_exists($shipment, 'generateTrackingNumber')) {
            Log::error('❌ generateTrackingNumber method not found in Shipment model');
            throw new \Exception('generateTrackingNumber method not found');
        }

        $shipment->shipment_number = $shipment->generateShipmentNumber();
        $shipment->tracking_number = $shipment->generateTrackingNumber();
        $shipment->sale_id = $sale->id;
        $shipment->customer_id = $sale->customer_id;
        $shipment->receiver_name = $receiverName;
        $shipment->receiver_phone = $receiverPhone;
        $shipment->shipping_address = $request->shipping_address;
        $shipment->city = $request->city;
        $shipment->state = $request->state;
        $shipment->pincode = $request->pincode;
        $shipment->country = 'India';
        $shipment->declared_value = $sale->grand_total;
        $shipment->quantity = $sale->items->sum('quantity');
        $shipment->weight = $sale->items->sum(function($item) {
            return ($item->product->weight ?? 0.5) * $item->quantity;
        });
        $shipment->shipping_method = 'standard';
        $shipment->payment_mode = $sale->payment_status === 'paid' ? 'prepaid' : 'cod';

        if ($request->destination_latitude && $request->destination_longitude) {
            $shipment->destination_latitude = $request->destination_latitude;
            $shipment->destination_longitude = $request->destination_longitude;
            $shipment->place_id = $request->place_id;
        }

        $shipment->delivery_instructions = $request->delivery_instructions;
        $shipment->estimated_delivery_date = now()->addDays(3);
        $shipment->status = 'pending';
        $shipment->created_by = auth()->id() ?? 1;

        // ✅ DEBUG: Log before save
        Log::info('📦 [DEBUG] Saving shipment with data', [
            'shipment_number' => $shipment->shipment_number,
            'tracking_number' => $shipment->tracking_number,
            'sale_id' => $shipment->sale_id,
            'customer_id' => $shipment->customer_id,
            'receiver_name' => $shipment->receiver_name,
            'city' => $shipment->city,
            'status' => $shipment->status
        ]);

        $shipment->save();

        // ✅ DEBUG: Verify save was successful
        Log::info('✅ [DEBUG] Shipment saved successfully', [
            'shipment_id' => $shipment->id,
            'shipment_number' => $shipment->shipment_number,
            'sale_id' => $shipment->sale_id
        ]);

        // Add tracking
        $tracking = $shipment->trackings()->create([
            'status' => 'pending',
            'location' => $request->city ?? 'Warehouse',
            'remarks' => 'Shipment created from invoice #' . $sale->invoice_no,
            'latitude' => $request->destination_latitude,
            'longitude' => $request->destination_longitude,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => 'India',
            'tracked_at' => now(),
            'event_type' => 'shipment_created',
            'is_public' => true
        ]);

        // ✅ DEBUG: Verify tracking was created
        Log::info('✅ [DEBUG] Tracking created', [
            'tracking_id' => $tracking->id,
            'shipment_id' => $shipment->id
        ]);

        Log::info("📦 Shipment auto-created from sale", [
            'sale_id' => $sale->id,
            'shipment_id' => $shipment->id,
            'shipment_number' => $shipment->shipment_number,
            'tracking_number' => $shipment->tracking_number
        ]);

        return $shipment;

    } catch (\Exception $e) {
        Log::error("❌ Failed to auto-create shipment: " . $e->getMessage(), [
            'sale_id' => $sale->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return null;
    }
}

    private function calculateSubTotal($items): float
    {
        $subTotal = 0;
        if (isset($items['product_id']) && is_array($items['product_id'])) {
            foreach ($items['product_id'] as $i => $productId) {
                $qty = isset($items['quantity'][$i]) ? (int) $items['quantity'][$i] : 0;
                $price = isset($items['price'][$i]) ? (float) $items['price'][$i] : 0;
                $subTotal += ($qty * $price);
            }
        }
        return $subTotal;
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'customer',
            'items.product',
            'payments',
            'shipments' => function($q) {
                $q->with(['trackings' => function($tq) {
                    $tq->latest()->limit(1);
                }]);
            }
        ]);

        $emiData = $this->getEmiData($sale);
        $paymentSummary = $this->calculatePaymentSummary($sale);

        $shipmentStatus = null;
        if ($sale->shipments->count() > 0) {
            $latestShipment = $sale->shipments->first();
            $shipmentStatus = [
                'exists' => true,
                'count' => $sale->shipments->count(),
                'latest_id' => $latestShipment->id,
                'latest_number' => $latestShipment->shipment_number,
                'latest_status' => $latestShipment->status,
                'tracking_number' => $latestShipment->tracking_number,
                'estimated_delivery' => $latestShipment->estimated_delivery_date?->format('d M Y'),
                'last_tracking' => $latestShipment->trackings->first()
            ];
        } else {
            $shipmentStatus = ['exists' => false];
        }

        return view('sales.show', compact('sale', 'emiData', 'paymentSummary', 'shipmentStatus'));
    }

    public function view(Sale $sale)
    {
        return $this->show($sale);
    }

    public function edit(Sale $sale)
    {
        if (in_array($sale->payment_status, ['paid', 'emi'])) {
            return redirect()->route('sales.show', $sale)->with('error', 'Cannot edit a paid or EMI invoice');
        }

        if ($sale->shipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
            return redirect()->route('sales.show', $sale)->with('error', 'Cannot edit invoice while shipment is in transit');
        }

        $sale->load('items.product');
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        if (in_array($sale->payment_status, ['paid', 'emi'])) {
            return redirect()->route('sales.show', $sale)->with('error', 'Cannot update a paid or EMI invoice');
        }

        if ($sale->shipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
            return redirect()->route('sales.show', $sale)->with('error', 'Cannot update invoice while shipment is in transit');
        }

        $request->validate([
            'items.product_id' => 'required|array|min:1',
            'items.quantity'   => 'required|array|min:1',
            'items.price'      => 'required|array|min:1',
            'discount'         => 'nullable|numeric|min:0',
            'tax'              => 'nullable|numeric|min:0|max:100'
        ]);

        try {
            DB::transaction(function () use ($request, $sale) {
                $subTotal = $this->calculateSubTotal($request->items);
                $discount = (float) ($request->discount ?? 0);
                $tax = (float) ($request->tax ?? 0);
                $taxAmount = $subTotal * $tax / 100;
                $grandTotal = $subTotal - $discount + $taxAmount;

                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }

                $sale->items()->delete();

                foreach ($request->items['product_id'] as $i => $productId) {
                    $qty = (int) $request->items['quantity'][$i];
                    $price = (float) $request->items['price'][$i];

                    $product = Product::lockForUpdate()->findOrFail($productId);

                    if ($product->quantity < $qty) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $productId,
                        'quantity'   => $qty,
                        'price'      => $price,
                        'total'      => $qty * $price,
                        'mrp'        => $product->mrp ?? $price
                    ]);

                    $product->decrement('quantity', $qty);
                }

                $sale->update([
                    'sub_total'   => $subTotal,
                    'discount'    => $discount,
                    'tax'         => $tax,
                    'tax_amount'  => $taxAmount,
                    'grand_total' => $grandTotal,
                ]);

                if ($sale->requires_shipping) {
                    foreach ($sale->shipments as $shipment) {
                        if ($shipment->status === 'pending') {
                            $shipment->declared_value = $grandTotal;
                            $shipment->save();
                        }
                    }
                }

                $this->recalculateInvoiceStatus($sale->id);
                Log::info("Sale updated successfully: ID={$sale->id}");
            });

            return redirect()->route('sales.show', $sale)->with('success', 'Invoice updated successfully');

        } catch (\Exception $e) {
            Log::error('Sale Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'sale_id' => $sale->id
            ]);
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        if ($sale->shipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
            return redirect()->route('sales.index')->with('error', 'Cannot delete invoice while shipment is in transit. Cancel shipment first.');
        }

        try {
            DB::transaction(function () use ($sale) {
                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }

                if ($sale->shipments()->exists()) {
                    $sale->shipments()->each(function(Shipment $shipment) {
                        if ($shipment->status === 'pending') {
                            $shipment->trackings()->delete();
                            $shipment->delete();
                        }
                    });
                }

                $sale->items()->delete();
                $sale->delete();
            });

            return redirect()->route('sales.index')->with('success', 'Sale deleted successfully');

        } catch (\Exception $e) {
            Log::error('Sale delete error: ' . $e->getMessage());
            return redirect()->route('sales.index')->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'items.product']);
        $totalPaid = $sale->payments()
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');
        $remaining = $sale->grand_total - $totalPaid;
        return view('sales.print', compact('sale', 'totalPaid', 'remaining'));
    }

  public function invoice(Sale $sale)
{
    $sale->load(['customer', 'items.product']);

    // Check if intl extension is available
    if (extension_loaded('intl')) {
        try {
            $formatter = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);
            $amountInWords = ucfirst($formatter->format((float)$sale->grand_total)) . ' Rupees Only';
        } catch (\Exception $e) {
            $amountInWords = $this->numberToWords($sale->grand_total) . ' Rupees Only';
        }
    } else {
        // Use your custom numberToWords method as fallback
        $amountInWords = $this->numberToWords($sale->grand_total) . ' Rupees Only';
    }

    $totalPaid = $sale->payments()
        ->where('status', 'paid')
        ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
        ->sum('amount');

    $pdf = Pdf::loadView('sales.invoice-pdf', [
        'sale' => $sale,
        'amountInWords' => $amountInWords,
        'totalPaid' => $totalPaid,
        'remaining' => $sale->grand_total - $totalPaid
    ]);

    return $pdf->stream('Invoice-' . $sale->invoice_no . '.pdf');
}

    public function deleteImpact($id)
    {
        try {
            $sale = Sale::with(['payments', 'customer', 'shipments'])->findOrFail($id);

            if ($sale->shipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
                return response()->json([
                    'success' => false,
                    'can_delete' => false,
                    'message' => 'Cannot delete invoice while shipment is in transit. Cancel shipment first.',
                    'shipment_status' => $sale->shipments->first()?->status
                ]);
            }

            $totalPaid = $sale->payments->sum('amount');
            $walletUsed = $sale->payments->where('remarks', 'ADVANCE_USED')->sum('amount');
            $directPayments = $sale->payments->whereIn('remarks', ['INVOICE', 'EMI_DOWN'])->sum('amount');
            $advancePayments = $sale->payments->whereIn('remarks', ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])->sum('amount');

            $affectedInvoices = $this->findAffectedInvoices($sale);
            $walletImpact = $advancePayments - $walletUsed;
            $openImpact = $directPayments + $walletUsed;

            return response()->json([
                'success' => true,
                'invoice_no' => $sale->invoice_no,
                'grand_total' => $sale->grand_total,
                'total_paid' => $totalPaid,
                'wallet_used' => $walletUsed,
                'direct_payments' => $directPayments,
                'advance_payments' => $advancePayments,
                'payment_count' => $sale->payments->count(),
                'affected_count' => count($affectedInvoices),
                'affected_invoices' => $affectedInvoices,
                'wallet_impact' => $walletImpact,
                'open_impact' => $openImpact,
                'has_shipment' => $sale->shipments->count() > 0,
                'shipment_status' => $sale->shipments->first()?->status,
                'warning' => $totalPaid > 0 ? "⚠️ This will affect:\n• Wallet: " . ($walletImpact > 0 ? "+" : "") . "₹{$walletImpact}\n• Open Balance: +₹{$openImpact}" : '✅ No payments to convert',
                'can_delete' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Impact Error: ' . $e->getMessage(), [
                'sale_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing impact: ' . $e->getMessage()
            ], 500);
        }
    }

    private function findAffectedInvoices($sale)
    {
        Log::info("========== 🔍 FIND AFFECTED INVOICES ==========");
        Log::info("Deleting invoice ID: {$sale->id}, Invoice #: {$sale->invoice_no}");

        $affectedIds = [];

        foreach ($sale->payments as $payment) {
            Log::info("Checking payment ID: {$payment->id}, Remarks: {$payment->remarks}");

            if ($payment->remarks === 'ADVANCE_USED' && $payment->source_wallet_id) {
                $originalCreditWalletId = $payment->source_wallet_id;
                Log::info("  → This payment uses credit wallet ID: {$originalCreditWalletId}");

                $otherUsers = Payment::where('source_wallet_id', $originalCreditWalletId)
                    ->where('sale_id', '!=', $sale->id)
                    ->where('remarks', 'ADVANCE_USED')
                    ->pluck('sale_id')
                    ->toArray();

                Log::info("  → Other invoices using this credit: " . json_encode($otherUsers));
                $affectedIds = array_merge($affectedIds, $otherUsers);
            }
            elseif (in_array($payment->remarks, ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD']) && $payment->wallet_id) {
                Log::info("  → This payment created wallet ID: {$payment->wallet_id}");

                $usersOfThisWallet = Payment::where('source_wallet_id', $payment->wallet_id)
                    ->where('sale_id', '!=', $sale->id)
                    ->where('remarks', 'ADVANCE_USED')
                    ->pluck('sale_id')
                    ->toArray();

                Log::info("  → Invoices using this wallet: " . json_encode($usersOfThisWallet));
                $affectedIds = array_merge($affectedIds, $usersOfThisWallet);
            }
        }

        $uniqueIds = array_unique($affectedIds);
        Log::info("✅ Final affected invoices: " . json_encode($uniqueIds));
        Log::info("========== 🔍 FIND AFFECTED INVOICES END ==========");

        return $uniqueIds;
    }

    public function deleteWithPayments($saleId)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::with(['items', 'payments', 'customer'])->lockForUpdate()->findOrFail($saleId);
            $customer = $sale->customer;

            if (!$customer) {
                throw new \Exception('Customer not found for this sale');
            }

            if ($sale->shipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete invoice while shipment is in transit. Cancel shipment first.'
                ], 400);
            }

            Log::info("========== 🗑️ DELETE WITH PAYMENTS STARTED ==========");
            Log::info("Deleting invoice #{$sale->invoice_no} (ID: {$saleId})");
            Log::info("Customer: {$customer->name} (ID: {$customer->id})");

            $currentWalletBalance = $this->getCurrentWalletBalance($customer->id);
            $currentOpenBalance = $customer->open_balance;

            Log::info("Current balances:");
            Log::info("  • Wallet balance: ₹{$currentWalletBalance}");
            Log::info("  • Open balance: ₹{$currentOpenBalance}");

            $walletAdjustment = 0;
            $openBalanceAdjustment = 0;
            $affectedInvoiceIds = [];
            $processedWallets = [];
            $totalCashToWallet = 0;

            Log::info("🔍 STEP 2: Finding affected invoices...");

            foreach ($sale->payments as $payment) {
                if ($payment->remarks === 'ADVANCE_USED' && $payment->source_wallet_id) {
                    $otherUsers = Payment::where('source_wallet_id', $payment->source_wallet_id)
                        ->where('sale_id', '!=', $saleId)
                        ->where('remarks', 'ADVANCE_USED')
                        ->pluck('sale_id')
                        ->toArray();

                    $affectedInvoiceIds = array_merge($affectedInvoiceIds, $otherUsers);
                    Log::info("  • Source wallet ID {$payment->source_wallet_id} used in invoices: " . json_encode($otherUsers));
                }

                if (in_array($payment->remarks, ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD']) && $payment->wallet_id) {
                    $users = Payment::where('source_wallet_id', $payment->wallet_id)
                        ->where('remarks', 'ADVANCE_USED')
                        ->pluck('sale_id')
                        ->toArray();

                    $affectedInvoiceIds = array_merge($affectedInvoiceIds, $users);
                    Log::info("  • Credit wallet ID {$payment->wallet_id} used in invoices: " . json_encode($users));
                }
            }

            $affectedInvoiceIds = array_unique($affectedInvoiceIds);
            Log::info("Total affected invoices: " . count($affectedInvoiceIds));

            Log::info("🔍 STEP 3: Collecting cash from affected invoices...");

            foreach ($affectedInvoiceIds as $invId) {
                $inv = Sale::with('payments')->find($invId);
                if ($inv) {
                    foreach ($inv->payments as $payment) {
                        if (in_array($payment->remarks, ['INVOICE', 'EMI_DOWN'])) {
                            $totalCashToWallet += $payment->amount;
                            Log::info("  • Invoice #{$inv->invoice_no}: Cash ₹{$payment->amount}");
                        }
                    }
                }
            }

            Log::info("Total cash to add to wallet: ₹{$totalCashToWallet}");

            Log::info("🔍 STEP 4: Processing main invoice payments...");

            foreach ($sale->payments as $payment) {
                Log::info("-----------------------------------");
                Log::info("Processing payment ID: {$payment->id}");
                Log::info("  • Remarks: {$payment->remarks}");
                Log::info("  • Amount: ₹{$payment->amount}");

                if (in_array($payment->remarks, ['INVOICE', 'EMI_DOWN'])) {
                    $openBalanceAdjustment += $payment->amount;
                    Log::info("  → Type: CASH PAYMENT");
                    Log::info("  → Effect: +₹{$payment->amount} to open balance");
                }
                elseif (in_array($payment->remarks, ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])) {
                    if ($payment->wallet_id && !in_array($payment->wallet_id, $processedWallets)) {
                        $walletAdjustment -= $payment->amount;
                        $processedWallets[] = $payment->wallet_id;

                        Log::info("  → Type: WALLET CREDIT");
                        Log::info("  → Effect: -₹{$payment->amount} to wallet balance");

                        CustomerWallet::where('id', $payment->wallet_id)->delete();
                        Log::info("  ✅ Deleted wallet credit ID: {$payment->wallet_id}");
                    }
                }
                elseif ($payment->remarks === 'ADVANCE_USED') {
                    if ($payment->wallet_id && !in_array($payment->wallet_id, $processedWallets)) {
                        $walletAdjustment += $payment->amount;
                        $processedWallets[] = $payment->wallet_id;

                        Log::info("  → Type: WALLET DEBIT");
                        Log::info("  → Effect: +₹{$payment->amount} to wallet balance");

                        CustomerWallet::where('id', $payment->wallet_id)->delete();
                        Log::info("  ✅ Deleted wallet debit ID: {$payment->wallet_id}");
                    }

                    $openBalanceAdjustment += $payment->amount;
                    Log::info("  → Also: +₹{$payment->amount} to open balance");
                }

                $payment->delete();
                Log::info("  ✅ Deleted payment record");
            }

            if (!empty($affectedInvoiceIds)) {
                Log::info("🔍 STEP 5: Processing affected invoices...");

                foreach ($affectedInvoiceIds as $affectedId) {
                    $affectedSale = Sale::with('payments')->find($affectedId);
                    if ($affectedSale) {
                        Log::info("-----------------------------------");
                        Log::info("Processing affected invoice #{$affectedSale->invoice_no}");

                        foreach ($affectedSale->payments as $payment) {
                            Log::info("  Deleting payment ID: {$payment->id} (₹{$payment->amount})");

                            if ($payment->remarks === 'ADVANCE_USED' && $payment->wallet_id) {
                                if (!in_array($payment->wallet_id, $processedWallets)) {
                                    $walletAdjustment += $payment->amount;
                                    $processedWallets[] = $payment->wallet_id;
                                    CustomerWallet::where('id', $payment->wallet_id)->delete();
                                    Log::info("    → Wallet debit deleted: +₹{$payment->amount}");
                                }
                            }

                            if (in_array($payment->remarks, ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD']) && $payment->wallet_id) {
                                if (!in_array($payment->wallet_id, $processedWallets)) {
                                    $walletAdjustment -= $payment->amount;
                                    $processedWallets[] = $payment->wallet_id;
                                    CustomerWallet::where('id', $payment->wallet_id)->delete();
                                    Log::info("    → Wallet credit deleted: -₹{$payment->amount}");
                                }
                            }

                            $payment->delete();
                        }

                        $affectedSale->payment_status = 'unpaid';
                        $affectedSale->paid_amount = 0;
                        $affectedSale->save();

                        Log::info("  ✅ Invoice #{$affectedSale->invoice_no} marked as DUE");

                        $openBalanceAdjustment += $affectedSale->grand_total;
                        Log::info("  → Added ₹{$affectedSale->grand_total} to open balance");
                    }
                }
            }

            if ($totalCashToWallet > 0) {
                Log::info("🔍 STEP 6: Adding ₹{$totalCashToWallet} cash to wallet...");

                $currentBalance = $this->getCurrentWalletBalance($customer->id);
                $newBalance = $currentBalance + $totalCashToWallet;

                $wallet = CustomerWallet::create([
                    'customer_id' => $customer->id,
                    'type' => 'credit',
                    'amount' => $totalCashToWallet,
                    'balance' => $newBalance,
                    'reference' => 'Cash recovered from affected invoices'
                ]);

                $walletAdjustment = $totalCashToWallet;

                Log::info("  ✅ Created wallet credit ID: {$wallet->id} for ₹{$totalCashToWallet}");
            }

            Log::info("🔍 STEP 7: Restoring stock...");

            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('quantity', $item->quantity);
                    Log::info("  ✅ Restored {$item->quantity} units");
                }
                $item->delete();
            }

            if ($sale->emiPlan) {
                $sale->emiPlan->delete();
                Log::info("  ✅ EMI plan deleted");
            }

            if ($sale->shipments()->exists()) {
                Log::info("🔍 Deleting associated shipments...");
                foreach ($sale->shipments as $shipment) {
                    $shipment->trackings()->delete();
                    $shipment->delete();
                    Log::info("  ✅ Deleted shipment #{$shipment->shipment_number}");
                }
            }

            $sale->delete();
            Log::info("  ✅ Main sale deleted");

            $newWalletBalance = max(0, $currentWalletBalance + $walletAdjustment);
            $newOpenBalance = max(0, $currentOpenBalance + $openBalanceAdjustment);

            $customer->wallet_balance = $newWalletBalance;
            $customer->open_balance = $newOpenBalance;
            $customer->save();

            Log::info("Final balances:");
            Log::info("  • Wallet: ₹{$newWalletBalance}");
            Log::info("  • Open: ₹{$newOpenBalance}");

            DB::commit();

            $message = "✅ Invoice #{$sale->invoice_no} deleted successfully!\n";
            if (!empty($affectedInvoiceIds)) {
                $message .= "📄 " . count($affectedInvoiceIds) . " affected invoice(s) marked as DUE\n";
            }
            $message .= "💰 Cash added to wallet: ₹" . number_format($totalCashToWallet, 2) . "\n";
            $message .= "💼 New wallet balance: ₹" . number_format($newWalletBalance, 2) . "\n";
            $message .= "📊 New open balance: ₹" . number_format($newOpenBalance, 2);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'deleted_invoice' => $sale->invoice_no,
                    'affected_invoices' => $affectedInvoiceIds,
                    'affected_count' => count($affectedInvoiceIds),
                    'cash_to_wallet' => $totalCashToWallet,
                    'new_wallet_balance' => $newWalletBalance,
                    'new_open_balance' => $newOpenBalance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ DELETE ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCurrentWalletBalance($customerId): float
    {
        $lastWallet = CustomerWallet::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastWallet ? (float) $lastWallet->balance : 0.00;
    }

    private function recalculateInvoiceStatus($saleId): void
    {
        $sale = Sale::find($saleId);
        if (!$sale) return;

        $totalPaid = Payment::where('sale_id', $saleId)
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');

        $remaining = $sale->grand_total - $totalPaid;

        $newStatus = match (true) {
            $remaining <= 0.01 => 'paid',
            $totalPaid > 0 => 'partial',
            default => 'unpaid'
        };

        $sale->update([
            'payment_status' => $newStatus,
            'paid_amount' => $totalPaid
        ]);

        Log::info("Invoice #{$sale->invoice_no} status updated to {$newStatus} (Paid: ₹{$totalPaid})");
    }

    private function recalculateAllCustomerInvoices($customerId): void
    {
        $invoices = Sale::where('customer_id', $customerId)->get();
        foreach ($invoices as $invoice) {
            $this->recalculateInvoiceStatus($invoice->id);
        }
    }

    private function getEmiData($sale): ?array
    {
        $emi = EmiPlan::where('sale_id', $sale->id)
            ->where('status', 'running')
            ->first();

        if (!$emi) return null;

        $total = $emi->emi_amount * $emi->months;
        $paid = Payment::where('sale_id', $sale->id)
            ->where('method', 'emi')
            ->sum('amount');
        $remaining = max($total - $paid, 0);
        $nextPay = min($emi->emi_amount, $remaining);

        return [
            'emi' => $emi,
            'total' => $total,
            'paid' => $paid,
            'remaining' => $remaining,
            'nextPay' => $nextPay,
            'completed' => $remaining <= 0,
            'progress' => $total > 0 ? round(($paid / $total) * 100, 2) : 0
        ];
    }

    private function calculatePaymentSummary($sale): array
    {
        $payments = $sale->payments;

        return [
            'total_paid' => $payments->where('status', 'paid')->sum('amount'),
            'cash_paid' => $payments->where('status', 'paid')->whereIn('remarks', ['INVOICE', 'EMI_DOWN'])->sum('amount'),
            'wallet_used' => $payments->where('status', 'paid')->where('remarks', 'ADVANCE_USED')->sum('amount'),
            'wallet_added' => $payments->where('status', 'paid')->whereIn('remarks', ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])->sum('amount'),
            'payment_count' => $payments->count(),
            'due' => $sale->grand_total - $payments->where('status', 'paid')->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])->sum('amount')
        ];
    }
}
