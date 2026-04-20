<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Sale;

class CustomerController extends Controller
{
    /* ===============================
       CUSTOMER LIST (ADMIN PAGE)
    =============================== */
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /* ===============================
       AJAX LIVE SEARCH (SALES PAGE)
       For large data (1000+)
    =============================== */
    public function ajaxSearch(Request $request)
    {
        $search = trim($request->search);

        // 🔒 Small protection (performance)
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('gst_no', 'like', "%{$search}%");
        })
            ->latest()
            ->limit(20)
            ->get([
                'id',
                'name',
                'mobile',
                'email',
                'gst_no',
                'address'
            ]);

        return response()->json($customers);
    }

    /* ===============================
       CREATE CUSTOMER PAGE
    =============================== */
    public function create()
    {
        return view('customers.create');
    }

    /* ===============================
       STORE CUSTOMER (NORMAL FORM)
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:customers,mobile',
            'email'  => 'nullable|email|unique:customers,email',
            'gst_no' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Customer::create([
            'name'    => trim($request->name),
            'mobile'  => trim($request->mobile),
            'email'   => trim($request->email),
            'gst_no'  => trim($request->gst_no),
            'address' => trim($request->address),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer added successfully');
    }

    /* ===============================
       EDIT CUSTOMER
    =============================== */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    /* ===============================
       UPDATE CUSTOMER
    =============================== */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:customers,mobile,' . $customer->id,
            'email'  => 'nullable|email|unique:customers,email,' . $customer->id,
            'gst_no' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update([
            'name'    => trim($request->name),
            'mobile'  => trim($request->mobile),
            'email'   => trim($request->email),
            'gst_no'  => trim($request->gst_no),
            'address' => trim($request->address),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    /* ===============================
       DELETE CUSTOMER
    =============================== */
    public function destroy($id)
    {
        Customer::destroy($id);
        return back()->with('success', 'Customer deleted successfully');
    }

    /* ===============================
       BULK DELETE CUSTOMERS
    =============================== */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'customer_ids' => 'required|array|min:1',
                'customer_ids.*' => 'required|integer|exists:customers,id'
            ]);

            $count = count($request->customer_ids);
            Customer::whereIn('id', $request->customer_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$count} customer(s)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting customers: ' . $e->getMessage()
            ], 400);
        }
    }

    /* ===============================
       AJAX STORE (FROM SALES MODAL)
    =============================== */
    public function storeAjax(Request $request)
    {
        try {
            $request->validate([
                'name'    => 'required|string|max:255',
                'mobile'  => 'required|string|max:20',
                'email'   => 'nullable|email',
                'address' => 'nullable|string',
            ]);

            // 🔍 Prevent duplicate by mobile
            $existing = Customer::where('mobile', $request->mobile)->first();
            if ($existing) {
                return response()->json([
                    'success'  => true,
                    'customer' => $existing,
                    'message'  => 'Existing customer selected'
                ]);
            }

            $customer = Customer::create([
                'name'    => trim($request->name),
                'mobile'  => trim($request->mobile),
                'email'   => trim($request->email),
                'address' => trim($request->address),
            ]);

            return response()->json([
                'success'  => true,
                'customer' => $customer,
                'message'  => 'Customer created successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /* ===============================
       CUSTOMER SALES (VIEW BUTTON)
    =============================== */
    public function sales(Customer $customer)
    {
        $sales = $customer->sales()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customers.sales', compact('customer', 'sales'));
    }



    public function payments(Customer $customer)
    {
        // Get all invoices for this customer with pagination
        $invoices = Sale::where('customer_id', $customer->id)
            ->orderBy('sale_date', 'desc')
            ->paginate(10, ['*'], 'invoices_page');

        // Get all transactions for this customer with eager loading
        $transactions = Payment::with('sale')
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'transactions_page');

        // Calculate invoice statistics
        $invoiceStats = [
            'total' => Sale::where('customer_id', $customer->id)->count(),
            'paid' => Sale::where('customer_id', $customer->id)->where('payment_status', 'paid')->count(),
            'partial' => Sale::where('customer_id', $customer->id)->whereIn('payment_status', ['unpaid', 'partial'])->count(),
        ];

        // Calculate advance balance
        $advanceBalance = $customer->open_balance < 0 ? abs((float)$customer->open_balance) : 0;

        // Calculate total money received from customer
        $totalReceived = Payment::where('customer_id', $customer->id)
            ->where('status', 'paid')
            ->whereNotIn('remarks', ['INVOICE_DUE']) // Exclude due entries
            ->sum('amount');

        return view('customers.payments', compact(
            'customer',
            'invoices',
            'transactions',
            'invoiceStats',
            'advanceBalance',
            'totalReceived'
        ));
    }
  public function getDetails($id)
{
    try {
        $customer = Customer::findOrFail($id);
        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'mobile' => $customer->mobile,
                'email' => $customer->email,
                'address' => $customer->address
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ], 404);
    }
}
}
