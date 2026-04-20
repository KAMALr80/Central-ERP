<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Customer extends Model
{
    use SoftDeletes, HasFactory, Auditable;

    protected $fillable = [
        // Basic Information
        'name',
        'mobile',
        'email',
        'gst_no',

        // Address Information
        'address',
        'city',
        'state',
        'pincode',
        'country',

        // ✅ NEW: Default Address Reference
        'default_address_id',

        // ✅ NEW: Customer Preferences
        'preferred_delivery_time',
        'delivery_instructions',
        'allow_sms_notifications',
        'allow_email_notifications',
        'allow_whatsapp_notifications',

        // ✅ NEW: Default Location Coordinates
        'default_latitude',
        'default_longitude',
        'default_place_id',

        // ✅ NEW: Tags & Notes
        'tags',
        'notes',

        // Financial
        'open_balance',
        'wallet_balance',

        // Audit
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        // JSON fields
        'tags' => 'array',

        // Booleans
        'allow_sms_notifications' => 'boolean',
        'allow_email_notifications' => 'boolean',
        'allow_whatsapp_notifications' => 'boolean',

        // Decimals
        'open_balance' => 'decimal:2',
        'wallet_balance' => 'decimal:2',

        // Coordinates
        'default_latitude' => 'decimal:8',
        'default_longitude' => 'decimal:8',

        // Dates
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /* ==================== EXISTING RELATIONSHIPS ==================== */

    /**
     * Get all sales for this customer
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all payments for this customer
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get wallet transactions for this customer
     */
    public function wallet()
    {
        return $this->hasMany(CustomerWallet::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the user who created this customer
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this customer
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ==================== NEW RELATIONSHIPS ==================== */

    /**
     * Get all addresses for this customer
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class)->orderBy('is_default', 'desc');
    }

    /**
     * Get default address for this customer
     */
    public function defaultAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'default_address_id');
    }

    /**
     * Get shipments for this customer
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get active shipments for this customer
     */
    public function activeShipments()
    {
        return $this->hasMany(Shipment::class)
                    ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery']);
    }

    /**
     * Get delivered shipments for this customer
     */
    public function deliveredShipments()
    {
        return $this->hasMany(Shipment::class)->where('status', 'delivered');
    }

    /**
     * Get shipment events for this customer
     */
    public function shipmentEvents()
    {
        return $this->hasManyThrough(ShipmentEvent::class, Shipment::class);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get full name with mobile for display
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->mobile . ')';
    }

    /**
     * Get full address with all components
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address ?? '';

        if ($this->city) {
            $address .= ($address ? ', ' : '') . $this->city;
        }
        if ($this->state) {
            $address .= ($address ? ', ' : '') . $this->state;
        }
        if ($this->pincode) {
            $address .= ($address ? ' - ' : '') . $this->pincode;
        }
        if ($this->country) {
            $address .= ($address ? ', ' : '') . $this->country;
        }

        return $address ?: 'No address provided';
    }

    /**
     * Get short address (city, state)
     */
    public function getShortAddressAttribute()
    {
        $parts = [];
        if ($this->city) $parts[] = $this->city;
        if ($this->state) $parts[] = $this->state;

        return !empty($parts) ? implode(', ', $parts) : 'Location not set';
    }

    /**
     * Get default coordinates as array
     */
    public function getDefaultCoordinatesAttribute()
    {
        if ($this->default_latitude && $this->default_longitude) {
            return [
                'lat' => $this->default_latitude,
                'lng' => $this->default_longitude
            ];
        }

        // Fallback to default address if exists
        if ($this->defaultAddress && $this->defaultAddress->coordinates) {
            return $this->defaultAddress->coordinates;
        }

        return null;
    }

    /**
     * Get Google Maps link for default location
     */
    public function getGoogleMapsLinkAttribute()
    {
        if ($this->default_coordinates) {
            return "https://www.google.com/maps?q={$this->default_latitude},{$this->default_longitude}";
        }

        return null;
    }

    /**
     * Get customer tags as array
     */
    public function getTagsListAttribute()
    {
        if (is_string($this->tags)) {
            return json_decode($this->tags, true) ?? [];
        }

        return $this->tags ?? [];
    }

    /**
     * Get formatted tags for display
     */
    public function getTagsDisplayAttribute()
    {
        $tags = $this->tags_list;

        if (empty($tags)) {
            return 'No tags';
        }

        return implode(', ', $tags);
    }

    /**
     * Get notification preferences summary
     */
    public function getNotificationPreferencesAttribute()
    {
        $preferences = [];

        if ($this->allow_sms_notifications) {
            $preferences[] = 'SMS';
        }
        if ($this->allow_email_notifications && $this->email) {
            $preferences[] = 'Email';
        }
        if ($this->allow_whatsapp_notifications) {
            $preferences[] = 'WhatsApp';
        }

        return !empty($preferences) ? implode(', ', $preferences) : 'None';
    }

    /**
     * Check if customer has any active shipments
     */
    public function getHasActiveShipmentsAttribute()
    {
        return $this->activeShipments()->exists();
    }

    /**
     * Get total spent by customer
     */
    public function getTotalSpentAttribute()
    {
        return $this->sales()->sum('grand_total');
    }

    /**
     * Get total paid by customer
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');
    }

    /**
     * Get total due from customer
     */
    public function getTotalDueAttribute()
    {
        return $this->total_spent - $this->total_paid;
    }

    /**
     * Get formatted total spent
     */
    public function getFormattedTotalSpentAttribute()
    {
        return '₹ ' . number_format($this->total_spent, 2);
    }

    /**
     * Get formatted total paid
     */
    public function getFormattedTotalPaidAttribute()
    {
        return '₹ ' . number_format($this->total_paid, 2);
    }

    /**
     * Get formatted total due
     */
    public function getFormattedTotalDueAttribute()
    {
        return '₹ ' . number_format($this->total_due, 2);
    }

    /**
     * Get formatted open balance
     */
    public function getFormattedOpenBalanceAttribute()
    {
        return '₹ ' . number_format($this->open_balance, 2);
    }

    /**
     * Get formatted wallet balance
     */
    public function getFormattedWalletBalanceAttribute()
    {
        return '₹ ' . number_format($this->wallet_balance, 2);
    }

    /**
     * Get current wallet balance (alias for backward compatibility)
     */
    public function getCurrentWalletBalanceAttribute()
    {
        return $this->wallet_balance ?? 0;
    }

    /**
     * Get total number of shipments
     */
    public function getShipmentsCountAttribute()
    {
        return $this->shipments()->count();
    }

    /**
     * Get delivered shipments count
     */
    public function getDeliveredCountAttribute()
    {
        return $this->deliveredShipments()->count();
    }

    /**
     * Get customer's preferred delivery time slot
     */
    public function getPreferredTimeSlotAttribute()
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

    /* ==================== METHODS ==================== */

    /**
     * Create a new address for this customer
     */
    public function addAddress($data)
    {
        $address = $this->addresses()->create($data);

        // If this is the first address or marked as default, set as default
        if ($this->addresses()->count() === 1 || ($data['is_default'] ?? false)) {
            $this->setDefaultAddress($address->id);
        }

        return $address;
    }

    /**
     * Set default address
     */
    public function setDefaultAddress($addressId)
    {
        // Remove default from all addresses
        $this->addresses()->update(['is_default' => false]);

        // Set new default
        $this->addresses()->where('id', $addressId)->update(['is_default' => true]);
        $this->default_address_id = $addressId;

        // Update default coordinates from address
        $address = $this->addresses()->find($addressId);
        if ($address) {
            $this->default_latitude = $address->latitude;
            $this->default_longitude = $address->longitude;
            $this->default_place_id = $address->place_id;
        }

        $this->save();

        return $this;
    }

    /**
     * Update customer location
     */
    public function updateLocation($latitude, $longitude, $placeId = null)
    {
        $this->default_latitude = $latitude;
        $this->default_longitude = $longitude;

        if ($placeId) {
            $this->default_place_id = $placeId;
        }

        $this->save();

        return $this;
    }

    /**
     * Add tags to customer
     */
    public function addTags($tags)
    {
        $currentTags = $this->tags_list;

        if (is_string($tags)) {
            $tags = [$tags];
        }

        $newTags = array_unique(array_merge($currentTags, $tags));
        $this->tags = $newTags;
        $this->save();

        return $this;
    }

    /**
     * Remove tags from customer
     */
    public function removeTags($tags)
    {
        $currentTags = $this->tags_list;

        if (is_string($tags)) {
            $tags = [$tags];
        }

        $newTags = array_diff($currentTags, $tags);
        $this->tags = array_values($newTags);
        $this->save();

        return $this;
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences($preferences)
    {
        if (isset($preferences['sms'])) {
            $this->allow_sms_notifications = $preferences['sms'];
        }

        if (isset($preferences['email'])) {
            $this->allow_email_notifications = $preferences['email'];
        }

        if (isset($preferences['whatsapp'])) {
            $this->allow_whatsapp_notifications = $preferences['whatsapp'];
        }

        $this->save();

        return $this;
    }

    /**
     * Update wallet balance
     */
    public function updateWalletBalance($newBalance)
    {
        $this->wallet_balance = $newBalance;
        $this->save();

        return $this;
    }

    /**
     * Add to wallet balance
     */
    public function addToWallet($amount)
    {
        $this->wallet_balance = ($this->wallet_balance ?? 0) + $amount;
        $this->save();

        return $this;
    }

    /**
     * Deduct from wallet balance
     */
    public function deductFromWallet($amount)
    {
        $newBalance = ($this->wallet_balance ?? 0) - $amount;

        if ($newBalance < 0) {
            throw new \Exception('Insufficient wallet balance');
        }

        $this->wallet_balance = $newBalance;
        $this->save();

        return $this;
    }

    /**
     * Get customer summary for dashboard
     */
    public function getSummaryAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'total_spent' => $this->total_spent,
            'total_paid' => $this->total_paid,
            'total_due' => $this->total_due,
            'wallet_balance' => $this->wallet_balance,
            'open_balance' => $this->open_balance,
            'shipments_count' => $this->shipments_count,
            'delivered_count' => $this->delivered_count,
            'active_shipments' => $this->has_active_shipments,
            'preferred_time' => $this->preferred_time_slot,
            'tags' => $this->tags_list,
            'address' => $this->full_address,
            'coordinates' => $this->default_coordinates
        ];
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope customers with wallet balance
     */
    public function scopeWithWalletBalance($query)
    {
        return $query->where('wallet_balance', '>', 0);
    }

    /**
     * Scope customers with open balance
     */
    public function scopeWithOpenBalance($query)
    {
        return $query->where('open_balance', '>', 0);
    }

    /**
     * Scope customers with due amount
     */
    public function scopeWithDue($query)
    {
        return $query->whereHas('sales', function($q) {
            $q->where('payment_status', '!=', 'paid');
        });
    }

    /**
     * Scope customers by city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope customers by pincode
     */
    public function scopeInPincode($query, $pincode)
    {
        return $query->where('pincode', $pincode);
    }

    /**
     * Scope customers by tag
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope customers with coordinates
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('default_latitude')
                     ->whereNotNull('default_longitude');
    }

    /**
     * Scope customers who prefer specific delivery time
     */
    public function scopePreferredTime($query, $time)
    {
        return $query->where('preferred_delivery_time', $time);
    }

    /**
     * Scope customers who have active shipments
     */
    public function scopeWithActiveShipments($query)
    {
        return $query->whereHas('shipments', function($q) {
            $q->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery']);
        });
    }

    /**
     * Scope customers by search term
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('mobile', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('gst_no', 'like', "%{$term}%");
        });
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'with_wallet' => self::withWalletBalance()->count(),
            'with_open' => self::withOpenBalance()->count(),
            'with_due' => self::withDue()->count(),
            'total_wallet_balance' => self::sum('wallet_balance'),
            'total_open_balance' => self::sum('open_balance'),
            'total_customers_with_coordinates' => self::withCoordinates()->count(),
            'top_cities' => self::select('city', \DB::raw('count(*) as total'))
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->country)) {
                $customer->country = 'India';
            }

            if (empty($customer->wallet_balance)) {
                $customer->wallet_balance = 0;
            }

            if (empty($customer->open_balance)) {
                $customer->open_balance = 0;
            }

            // Default notification preferences
            if (!isset($customer->allow_sms_notifications)) {
                $customer->allow_sms_notifications = true;
            }

            if (!isset($customer->allow_email_notifications)) {
                $customer->allow_email_notifications = true;
            }

            if (!isset($customer->allow_whatsapp_notifications)) {
                $customer->allow_whatsapp_notifications = true;
            }
        });

        static::deleting(function ($customer) {
            // Handle soft delete for related records
            if (method_exists($customer, 'addresses')) {
                $customer->addresses()->delete();
            }
        });
    }
}
