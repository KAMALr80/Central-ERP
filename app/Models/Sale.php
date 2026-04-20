<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Sale extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'customer_id',
        'invoice_no',
        'invoice_token',
        'sale_date',
        'sub_total',
        'discount',
        'tax',
        'tax_amount',
        'grand_total',
        'payment_status',
        'paid_amount',

        // Shipping Fields
        'requires_shipping',
        'shipping_address',
        'city',
        'state',
        'pincode',
        'receiver_name',
        'receiver_phone',
        'delivery_instructions',

        // ✅ NEW SHIPPING FIELDS
        'shipping_status',
        'shipped_at',
        'delivered_at',

        // ✅ LOCATION FIELDS
        'destination_latitude',
        'destination_longitude',
        'place_id',
        'location_verified',

        // ✅ PREFERENCE FIELDS
        'preferred_delivery_date',
        'preferred_delivery_time',
        'allow_partial_delivery',

        // ✅ SHIPPING CHARGES
        'shipping_charge',
        'cod_charge',
        'insurance_charge',
        'packing_charge',
        'total_shipping_charge',

        // Audit
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        // Dates
        'sale_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'preferred_delivery_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',

        // Decimals
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'cod_charge' => 'decimal:2',
        'insurance_charge' => 'decimal:2',
        'packing_charge' => 'decimal:2',
        'total_shipping_charge' => 'decimal:2',

        // Coordinates
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',

        // Booleans
        'requires_shipping' => 'boolean',
        'location_verified' => 'boolean',
        'allow_partial_delivery' => 'boolean',

        // JSON
        'preferred_delivery_time' => 'string',
        'shipping_status' => 'string',
        'place_id' => 'string',
    ];

    /* ==================== EXISTING RELATIONSHIPS ==================== */

    /**
     * Get the customer for this sale
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get the items for this sale
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the payments for this sale
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment for this sale
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Get the EMI plan for this sale
     */
    public function emiPlan()
    {
        return $this->hasOne(EmiPlan::class);
    }

    /**
     * Get the user who created this sale
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this sale
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ==================== SHIPMENT RELATIONSHIPS ==================== */

    /**
     * Get all shipments for this sale
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'sale_id');
    }

    /**
     * Get the latest shipment for this sale
     */
    public function latestShipment()
    {
        return $this->hasOne(Shipment::class, 'sale_id')->latestOfMany();
    }

    /**
     * Get pending shipments for this sale
     */
    public function pendingShipments()
    {
        return $this->hasMany(Shipment::class, 'sale_id')->where('status', 'pending');
    }

    /**
     * Get active shipments (not delivered) for this sale
     */
    public function activeShipments()
    {
        return $this->hasMany(Shipment::class, 'sale_id')
                    ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery']);
    }

    /**
     * Get delivered shipments for this sale
     */
    public function deliveredShipments()
    {
        return $this->hasMany(Shipment::class, 'sale_id')->where('status', 'delivered');
    }

    /**
     * Get the first shipment (useful for tracking)
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'sale_id')->latestOfMany();
    }

    /* ==================== ACCESSORS - FORMATTED VALUES ==================== */

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->sale_date
            ? $this->sale_date->format('d-m-Y')
            : null;
    }

    /**
     * Get full shipping address with all components
     */
    public function getFullShippingAddressAttribute()
    {
        if (!$this->requires_shipping) {
            return $this->customer ? $this->customer->address : 'No address provided';
        }

        $address = $this->shipping_address ?? '';

        if ($this->city) {
            $address .= ($address ? ', ' : '') . $this->city;
        }
        if ($this->state) {
            $address .= ($address ? ', ' : '') . $this->state;
        }
        if ($this->pincode) {
            $address .= ($address ? ' - ' : '') . $this->pincode;
        }

        return $address ?: 'No address provided';
    }

    /**
     * Get final receiver name (shipping receiver or customer name)
     */
    public function getFinalReceiverNameAttribute()
    {
        if ($this->requires_shipping && $this->receiver_name) {
            return $this->receiver_name;
        }

        return $this->customer ? $this->customer->name : 'Customer';
    }

    /**
     * Get final receiver phone (shipping receiver or customer mobile)
     */
    public function getFinalReceiverPhoneAttribute()
    {
        if ($this->requires_shipping && $this->receiver_phone) {
            return $this->receiver_phone;
        }

        return $this->customer ? $this->customer->mobile : '';
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute()
    {
        if ($this->destination_latitude && $this->destination_longitude) {
            return [
                'lat' => $this->destination_latitude,
                'lng' => $this->destination_longitude
            ];
        }

        return null;
    }

    /**
     * Get Google Maps link for this location
     */
    public function getGoogleMapsLinkAttribute()
    {
        if ($this->coordinates) {
            return "https://www.google.com/maps?q={$this->destination_latitude},{$this->destination_longitude}";
        }

        return null;
    }

    /**
     * Get OpenStreetMap link for this location
     */
    public function getOpenStreetMapLinkAttribute()
    {
        if ($this->coordinates) {
            return "https://www.openstreetmap.org/?mlat={$this->destination_latitude}&mlon={$this->destination_longitude}#map=15/{$this->destination_latitude}/{$this->destination_longitude}";
        }

        return null;
    }

    /**
     * Get formatted preferred delivery time
     */
    public function getFormattedPreferredTimeAttribute()
    {
        if (!$this->preferred_delivery_time) {
            return 'Anytime';
        }

        $times = [
            'morning' => 'Morning (9 AM - 12 PM)',
            'afternoon' => 'Afternoon (12 PM - 3 PM)',
            'evening' => 'Evening (3 PM - 6 PM)',
            'night' => 'Night (6 PM - 9 PM)',
        ];

        return $times[$this->preferred_delivery_time] ?? $this->preferred_delivery_time;
    }

    /* ==================== SHIPMENT STATUS ACCESSORS ==================== */

    /**
     * Get tracking number for this sale (from latest shipment)
     */
    public function getTrackingNumberAttribute()
    {
        $shipment = $this->latestShipment;
        return $shipment ? $shipment->tracking_number : null;
    }

    /**
     * Get shipment status for this sale
     */
    public function getShipmentStatusAttribute()
    {
        $shipment = $this->latestShipment;

        if (!$shipment) {
            return $this->requires_shipping ? 'no_shipment' : 'not_required';
        }

        return $shipment->status;
    }

    /**
     * Get shipment status badge class
     */
    public function getShipmentStatusBadgeAttribute()
    {
        $status = $this->shipment_status;

        return match($status) {
            'pending' => 'warning',
            'picked' => 'info',
            'in_transit' => 'primary',
            'out_for_delivery' => 'secondary',
            'delivered' => 'success',
            'failed' => 'danger',
            'returned' => 'dark',
            'no_shipment' => 'light',
            'not_required' => 'light',
            default => 'secondary'
        };
    }

    /**
     * Get shipment status display text
     */
    public function getShipmentStatusDisplayAttribute()
    {
        $status = $this->shipment_status;

        return match($status) {
            'pending' => '⏳ Pending',
            'picked' => '📦 Picked Up',
            'in_transit' => '🚚 In Transit',
            'out_for_delivery' => '🚀 Out for Delivery',
            'delivered' => '✅ Delivered',
            'failed' => '❌ Failed',
            'returned' => '🔄 Returned',
            'no_shipment' => '📋 Not Created',
            'not_required' => '🚫 Not Required',
            default => ucfirst(str_replace('_', ' ', $status))
        };
    }

    /**
     * Check if sale has active shipment
     */
    public function getHasActiveShipmentAttribute()
    {
        return $this->activeShipments()->exists();
    }

    /**
     * Check if sale is ready for shipment
     */
    public function getReadyForShipmentAttribute()
    {
        return $this->requires_shipping &&
               !$this->has_active_shipment &&
               $this->payment_status !== 'unpaid';
    }

    /**
     * Check if sale is fully shipped
     */
    public function getIsFullyShippedAttribute()
    {
        if (!$this->requires_shipping) {
            return true;
        }

        return $this->shipments()->exists() &&
               $this->shipments()->where('status', 'delivered')->count() > 0;
    }

    /**
     * Get shipping progress percentage
     */
    public function getShippingProgressAttribute()
    {
        if (!$this->requires_shipping) {
            return 100;
        }

        $shipment = $this->latestShipment;
        if (!$shipment) {
            return 0;
        }

        return $shipment->progress_percentage;
    }

    /* ==================== PAYMENT ACCESSORS ==================== */

    /**
     * Get due amount for this sale
     */
    public function getDueAmountAttribute()
    {
        $totalPaid = $this->payments()
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');

        return max(0, $this->grand_total - $totalPaid);
    }

    /**
     * Get formatted due amount
     */
    public function getFormattedDueAttribute()
    {
        return '₹ ' . number_format($this->due_amount, 2);
    }

    /**
     * Get payment status with badge color
     */
    public function getPaymentStatusBadgeAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'partial' => 'warning',
            'emi' => 'info',
            default => 'danger'
        };
    }

    /**
     * Get payment status display text
     */
    public function getPaymentStatusDisplayAttribute()
    {
        return match($this->payment_status) {
            'paid' => '✅ Paid',
            'partial' => '⚠️ Partial',
            'emi' => '📊 EMI',
            'unpaid' => '❌ Unpaid',
            default => ucfirst($this->payment_status)
        };
    }

    /**
     * Get payment progress percentage
     */
    public function getPaymentProgressAttribute()
    {
        if ($this->grand_total <= 0) {
            return 0;
        }

        return min(100, round(($this->paid_amount / $this->grand_total) * 100));
    }

    /**
     * Get total shipping charges
     */
    public function getTotalShippingChargesAttribute()
    {
        return ($this->shipping_charge ?? 0) +
               ($this->cod_charge ?? 0) +
               ($this->insurance_charge ?? 0) +
               ($this->packing_charge ?? 0);
    }

    /* ==================== CUSTOM METHODS ==================== */

    /**
     * Create shipment from this sale
     */
    public function createShipment($data = [])
    {
        if (!$this->requires_shipping) {
            throw new \Exception('This sale does not require shipping');
        }

        // Check if shipment already exists
        if ($this->shipments()->exists()) {
            throw new \Exception('Shipment already exists for this sale');
        }

        $shipment = new Shipment();
        $shipment->sale_id = $this->id;
        $shipment->customer_id = $this->customer_id;
        $shipment->shipment_number = (new Shipment())->generateShipmentNumber();

        // Receiver details
        $shipment->receiver_name = $data['receiver_name'] ?? $this->final_receiver_name;
        $shipment->receiver_phone = $data['receiver_phone'] ?? $this->final_receiver_phone;
        $shipment->receiver_alternate_phone = $data['receiver_alternate_phone'] ?? null;

        // Shipping address
        $shipment->shipping_address = $data['shipping_address'] ?? $this->shipping_address;
        $shipment->landmark = $data['landmark'] ?? null;
        $shipment->city = $data['city'] ?? $this->city;
        $shipment->state = $data['state'] ?? $this->state;
        $shipment->pincode = $data['pincode'] ?? $this->pincode;
        $shipment->country = $data['country'] ?? 'India';

        // ✅ IMPORTANT: Save coordinates from sale
        if ($this->coordinates) {
            $shipment->destination_latitude = $this->destination_latitude;
            $shipment->destination_longitude = $this->destination_longitude;
            $shipment->place_id = $this->place_id;
        }

        // Package details
        $shipment->declared_value = $this->grand_total;
        $shipment->quantity = $this->items->sum('quantity');
        $shipment->payment_mode = $this->payment_status === 'paid' ? 'prepaid' : 'cod';
        $shipment->shipping_method = $data['shipping_method'] ?? 'standard';

        // Delivery preferences
        $shipment->delivery_instructions = $data['delivery_instructions'] ?? $this->delivery_instructions;

        if ($this->preferred_delivery_date) {
            $shipment->estimated_delivery_date = $this->preferred_delivery_date;
        }

        // Status
        $shipment->status = 'pending';
        $shipment->created_by = auth()->id() ?? 1;
        $shipment->save();

        // Add initial tracking
        $shipment->updateTracking(
            'pending',
            $this->city ?? 'Warehouse',
            'Shipment created from invoice #' . $this->invoice_no
        );

        // Update sale shipping status
        $this->shipping_status = 'shipment_created';
        $this->save();

        return $shipment;
    }

    /**
     * Update location coordinates for this sale
     */
    public function updateCoordinates($latitude, $longitude, $placeId = null)
    {
        $this->destination_latitude = $latitude;
        $this->destination_longitude = $longitude;

        if ($placeId) {
            $this->place_id = $placeId;
        }

        $this->location_verified = true;
        $this->save();

        // Also update any pending shipments
        foreach ($this->pendingShipments as $shipment) {
            $shipment->destination_latitude = $latitude;
            $shipment->destination_longitude = $longitude;
            $shipment->place_id = $placeId;
            $shipment->save();
        }

        return $this;
    }

    /**
     * Mark as shipped
     */
    public function markAsShipped()
    {
        $this->shipping_status = 'shipped';
        $this->shipped_at = now();
        $this->save();

        return $this;
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered()
    {
        $this->shipping_status = 'delivered';
        $this->delivered_at = now();
        $this->save();

        return $this;
    }

    /**
     * Recalculate payment status based on payments
     */
    public function recalculatePaymentStatus()
    {
        $totalPaid = $this->payments()
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');

        $this->paid_amount = $totalPaid;

        if ($totalPaid <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($totalPaid >= $this->grand_total) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }

        $this->save();

        return $this->payment_status;
    }

    /**
     * Calculate shipping charges
     */
    public function calculateShippingCharges($weight = null, $codAmount = null)
    {
        // Default calculation logic - can be customized
        $weight = $weight ?? $this->items->sum(function($item) {
            return ($item->product->weight ?? 0.5) * $item->quantity;
        });

        $baseRate = 50;
        $this->shipping_charge = $weight * $baseRate;

        // COD charges
        if ($this->payment_status !== 'paid' && $codAmount) {
            if ($codAmount <= 5000) {
                $this->cod_charge = 30;
            } elseif ($codAmount <= 10000) {
                $this->cod_charge = 50;
            } elseif ($codAmount <= 25000) {
                $this->cod_charge = 100;
            } else {
                $this->cod_charge = $codAmount * 0.005;
            }
        }

        // Insurance (0.1% for values > 10000)
        if ($this->grand_total > 10000) {
            $this->insurance_charge = $this->grand_total * 0.001;
        }

        $this->total_shipping_charge = $this->total_shipping_charges;
        $this->save();

        return [
            'shipping' => $this->shipping_charge,
            'cod' => $this->cod_charge,
            'insurance' => $this->insurance_charge,
            'packing' => $this->packing_charge,
            'total' => $this->total_shipping_charge
        ];
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope for unpaid invoices
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope for paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for partial invoices
     */
    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    /**
     * Scope for invoices requiring shipping
     */
    public function scopeRequiresShipping($query)
    {
        return $query->where('requires_shipping', true);
    }

    /**
     * Scope for invoices without shipment
     */
    public function scopeWithoutShipment($query)
    {
        return $query->where('requires_shipping', true)
                     ->whereDoesntHave('shipments');
    }

    /**
     * Scope for invoices with location coordinates
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('destination_latitude')
                     ->whereNotNull('destination_longitude');
    }

    /**
     * Scope for invoices with verified locations
     */
    public function scopeLocationVerified($query)
    {
        return $query->where('location_verified', true);
    }

    /**
     * Scope for invoices in a specific city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope for invoices in a specific pincode
     */
    public function scopeInPincode($query, $pincode)
    {
        return $query->where('pincode', $pincode);
    }

    /**
     * Scope for today's invoices
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    /**
     * Scope for this month's invoices
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
                     ->whereYear('sale_date', now()->year);
    }

    /**
     * Scope for invoices with preferred delivery date
     */
    public function scopeWithPreferredDate($query)
    {
        return $query->whereNotNull('preferred_delivery_date');
    }

    /**
     * Scope for invoices ready for shipment
     */
    public function scopeReadyForShipment($query)
    {
        return $query->requiresShipping()
                     ->withoutShipment()
                     ->where('payment_status', '!=', 'unpaid');
    }

    /**
     * Scope for shipped but not delivered
     */
    public function scopeShipped($query)
    {
        return $query->where('shipping_status', 'shipped');
    }

    /**
     * Scope for delivered invoices
     */
    public function scopeShippingDelivered($query)
    {
        return $query->where('shipping_status', 'delivered');
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Generate unique invoice number
     */
    /**
 * Generate unique invoice number
 */
public static function generateInvoiceNumber()
{
    $year = date('Y');
    $prefix = 'INV-' . $year . '-';

    // Get the latest invoice number for this year
    $latestInvoice = self::where('invoice_no', 'LIKE', $prefix . '%')
        ->orderBy('id', 'desc')
        ->lockForUpdate()
        ->first();

    if ($latestInvoice) {
        // Extract the number from the invoice_no
        $lastNumber = (int) str_replace($prefix, '', $latestInvoice->invoice_no);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
}

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'total_revenue' => self::sum('grand_total'),
            'paid' => self::where('payment_status', 'paid')->count(),
            'partial' => self::where('payment_status', 'partial')->count(),
            'unpaid' => self::where('payment_status', 'unpaid')->count(),
            'requires_shipping' => self::requiresShipping()->count(),
            'ready_for_shipment' => self::readyForShipment()->count(),
            'shipped' => self::shipped()->count(),
            'delivered' => self::shippingDelivered()->count(),
            'total_shipping_charges' => self::sum('total_shipping_charge'),
        ];
    }

protected static function boot()
{
    parent::boot();

    static::creating(function ($sale) {
        if (empty($sale->invoice_no)) {
            // Generate unique invoice number with locking to prevent duplicates
            $year = date('Y');
            $prefix = 'INV-' . $year . '-';

            // Use a database transaction with lock to ensure uniqueness
            $latestInvoice = self::where('invoice_no', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($latestInvoice) {
                // Extract the number from the invoice_no
                $lastNumber = (int) str_replace($prefix, '', $latestInvoice->invoice_no);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $sale->invoice_no = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        }

        if (empty($sale->sale_date)) {
            $sale->sale_date = now();
        }

        if (empty($sale->payment_status)) {
            $sale->payment_status = 'unpaid';
        }

        if (empty($sale->paid_amount)) {
            $sale->paid_amount = 0;
        }

        // Set shipping status
        if ($sale->requires_shipping && empty($sale->shipping_status)) {
            $sale->shipping_status = 'pending';
        } elseif (!$sale->requires_shipping) {
            $sale->shipping_status = null;
        }
    });

    static::created(function ($sale) {
        // Log creation event
        if (class_exists(\App\Events\ShipmentEvent::class)) {
            try {
                $sale->shipments->each(function($shipment) use ($sale) {
                    if (method_exists($shipment, 'createEvent')) {
                        $shipment->createEvent('sale_created', [
                            'sale_id' => $sale->id,
                            'invoice_no' => $sale->invoice_no
                        ]);
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Failed to create shipment event: ' . $e->getMessage());
            }
        }
    });

    static::updating(function ($sale) {
        // Auto-calculate total shipping charges
        $sale->total_shipping_charge = $sale->total_shipping_charges;
    });
}
}
