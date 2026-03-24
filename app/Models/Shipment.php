<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\GoogleMapsService;
use Illuminate\Support\Facades\Log;

class Shipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Basic Information
        'shipment_number',
        'tracking_number',
        'sale_id',
        'customer_id',
        'assigned_to',

        // Receiver Details
        'receiver_name',
        'receiver_phone',
        'receiver_alternate_phone',

        // Address Details
        'shipping_address',
        'landmark',
        'city',
        'state',
        'pincode',
        'country',

        // Package Details
        'weight',
        'length',
        'width',
        'height',
        'quantity',
        'declared_value',
        'package_type',

        // Shipping Details
        'shipping_method',
        'courier_partner',
        'awb_number',

        // Charges
        'shipping_charge',
        'cod_charge',
        'insurance_charge',
        'total_charge',
        'payment_mode',

        // Status
        'status',
        'status_note',
        'delivery_order',
        'pickup_date',
        'estimated_delivery_date',
        'actual_delivery_date',

        // Location Tracking
        'destination_latitude',
        'destination_longitude',
        'current_latitude',
        'current_longitude',
        'location_accuracy',
        'last_location_update',
        'last_ping_at',
        'battery_level',
        'gps_signal_strength',

        // Route Information
        'distance_travelled',
        'estimated_delivery_time',
        'route_polyline',
        'place_id',

        // Delivery Instructions
        'delivery_instructions',

        // Proof of Delivery
        'pod_signature',
        'pod_photo',
        'delivery_notes',
        'pod_verified_at',
        'pod_verified_by',

        // Delivery Attempts
        'delivery_attempts',
        'last_delivery_attempt_at',

        // Return Information
        'return_reason',
        'return_initiated_by',

        // OTP Verification
        'delivery_otp',
        'otp_verified_at',

        // Audit
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        // Decimal values
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'cod_charge' => 'decimal:2',
        'insurance_charge' => 'decimal:2',
        'total_charge' => 'decimal:2',
        'distance_travelled' => 'decimal:2',

        // Dates
        'pickup_date' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
        'last_location_update' => 'datetime',
        'last_ping_at' => 'datetime',
        'pod_verified_at' => 'datetime',
        'last_delivery_attempt_at' => 'datetime',
        'otp_verified_at' => 'datetime',

        // Coordinates
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',

        // Integers
        'delivery_attempts' => 'integer',
        'battery_level' => 'integer',
        'delivery_order' => 'integer',
        'estimated_delivery_time' => 'integer',

        // Strings
        'gps_signal_strength' => 'string',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($shipment) {
            // Check if address fields changed, or if it's new and doesn't have coordinates
            $addressChanged = $shipment->isDirty('shipping_address') || 
                              $shipment->isDirty('city') || 
                              $shipment->isDirty('state') || 
                              $shipment->isDirty('pincode');
            
            $needsCoordinates = empty($shipment->destination_latitude) || empty($shipment->destination_longitude);

            if ($addressChanged || $needsCoordinates) {
                // Build the full address string for geocoding
                $addressParts = array_filter([
                    $shipment->shipping_address,
                    $shipment->landmark,
                    $shipment->city,
                    $shipment->state,
                    $shipment->pincode,
                    $shipment->country ?? 'India'
                ]);
                $fullAddress = implode(', ', $addressParts);

                if (!empty($fullAddress)) {
                    try {
                        $googleMaps = app(GoogleMapsService::class);
                        $result = $googleMaps->geocodeAddress($fullAddress);
                        
                        if ($result && isset($result['lat']) && isset($result['lng'])) {
                            $shipment->destination_latitude = $result['lat'];
                            $shipment->destination_longitude = $result['lng'];
                        }
                    } catch (\Exception $e) {
                        Log::error('Shipment Geocoding failed: ' . $e->getMessage(), ['shipment_id' => $shipment->id]);
                    }
                }
            }
        });

        static::creating(function ($shipment) {
            if (empty($shipment->shipment_number)) {
                $shipment->shipment_number = $shipment->generateShipmentNumber();
            }

            if (empty($shipment->tracking_number)) {
                $shipment->tracking_number = $shipment->generateTrackingNumber();
            }

            if (empty($shipment->country)) {
                $shipment->country = 'India';
            }

            if (empty($shipment->delivery_attempts)) {
                $shipment->delivery_attempts = 0;
            }
        });

        static::created(function ($shipment) {
            $shipment->createEvent('shipment_created');
        });

        static::updated(function ($shipment) {
            if ($shipment->isDirty('status')) {
                $shipment->createEvent('status_changed');
            }

            if ($shipment->isDirty('assigned_to')) {
                $shipment->createEvent('agent_changed');
            }
        });
    }

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the sale associated with this shipment
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /**
     * Get the customer associated with this shipment
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the delivery agent (user) assigned to this shipment
     */
    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the delivery agent details from delivery_agents table
     */
    public function agent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'assigned_to', 'user_id');
    }

    /**
     * Get all tracking records for this shipment
     */
    public function trackings()
    {
        return $this->hasMany(ShipmentTracking::class)->orderBy('tracked_at', 'desc');
    }

    /**
     * Get the latest tracking record
     */
    public function latestTracking()
    {
        return $this->hasOne(ShipmentTracking::class)->latestOfMany('tracked_at');
    }

    /**
     * Get all events for this shipment
     */
    public function events()
    {
        return $this->hasMany(ShipmentEvent::class)->orderBy('occurred_at', 'desc');
    }

    /**
     * Get courier shipment details
     */
    public function courierShipment()
    {
        return $this->hasOne(CourierShipment::class);
    }

    /**
     * Get the user who created this shipment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this shipment
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who verified POD
     */
    public function podVerifier()
    {
        return $this->belongsTo(User::class, 'pod_verified_by');
    }

    /**
     * Get the user who initiated return
     */
    public function returnInitiator()
    {
        return $this->belongsTo(User::class, 'return_initiated_by');
    }

    /**
     * Get the courier partner
     */
    public function courierPartner()
    {
        return $this->belongsTo(CourierPartner::class, 'courier_partner', 'code');
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope pending shipments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope shipments in transit
     */
    public function scopeInTransit($query)
    {
        return $query->whereIn('status', ['picked', 'in_transit', 'out_for_delivery']);
    }

    /**
     * Scope delivered shipments
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope shipments by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope shipments by courier partner
     */
    public function scopeByCourier($query, $courier)
    {
        return $query->where('courier_partner', $courier);
    }

    /**
     * Scope shipments by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope shipments assigned to specific agent
     */
    public function scopeAssignedTo($query, $agentId)
    {
        return $query->where('assigned_to', $agentId);
    }

    /**
     * Scope shipments that are overdue
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['delivered', 'failed', 'returned', 'cancelled'])
                     ->whereDate('estimated_delivery_date', '<', now());
    }

    /**
     * Scope shipments that are out for delivery
     */
    public function scopeOutForDelivery($query)
    {
        return $query->where('status', 'out_for_delivery');
    }

    /**
     * Scope shipments that are ready for pickup
     */
    public function scopeReadyForPickup($query)
    {
        return $query->where('status', 'pending')
                     ->whereNull('pickup_date')
                     ->whereDate('created_at', '<=', now());
    }

    /**
     * Scope shipments with live location
     */
    public function scopeWithLiveLocation($query)
    {
        return $query->whereNotNull('current_latitude')
                     ->whereNotNull('current_longitude')
                     ->whereNotNull('last_location_update');
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'picked' => 'info',
            'in_transit' => 'primary',
            'out_for_delivery' => 'secondary',
            'delivered' => 'success',
            'failed' => 'danger',
            'returned' => 'dark',
            'cancelled' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted status for display
     */
    public function getStatusDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get full formatted address
     */
    public function getFullAddressAttribute()
    {
        $address = $this->shipping_address;
        if ($this->landmark) {
            $address .= ', ' . $this->landmark;
        }
        $address .= ', ' . $this->city . ', ' . $this->state . ' - ' . $this->pincode;
        return $address;
    }

    /**
     * Get formatted declared value
     */
    public function getFormattedDeclaredValueAttribute()
    {
        return '₹ ' . number_format($this->declared_value, 2);
    }

    /**
     * Get formatted total charge
     */
    public function getFormattedTotalChargeAttribute()
    {
        return '₹ ' . number_format($this->total_charge, 2);
    }

    /**
     * Get formatted shipping charge
     */
    public function getFormattedShippingChargeAttribute()
    {
        return '₹ ' . number_format($this->shipping_charge, 2);
    }

    /**
     * Get formatted COD charge
     */
    public function getFormattedCodChargeAttribute()
    {
        return '₹ ' . number_format($this->cod_charge, 2);
    }

    /**
     * Get formatted insurance charge
     */
    public function getFormattedInsuranceChargeAttribute()
    {
        return '₹ ' . number_format($this->insurance_charge, 2);
    }

    /**
     * Get formatted distance travelled
     */
    public function getFormattedDistanceAttribute()
    {
        return $this->distance_travelled ? $this->distance_travelled . ' km' : 'N/A';
    }

    /**
     * Get estimated delivery time in hours
     */
    public function getEstimatedDeliveryHoursAttribute()
    {
        return $this->estimated_delivery_time ? $this->estimated_delivery_time . ' mins' : 'N/A';
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        $statusOrder = [
            'pending' => 0,
            'picked' => 25,
            'in_transit' => 50,
            'out_for_delivery' => 75,
            'delivered' => 100
        ];

        return $statusOrder[$this->status] ?? 0;
    }

    /**
     * Check if shipment is delivered
     */
    public function getIsDeliveredAttribute()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if shipment is in transit
     */
    public function getIsInTransitAttribute()
    {
        return in_array($this->status, ['picked', 'in_transit', 'out_for_delivery']);
    }

    /**
     * Check if shipment is pending
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if shipment is failed or returned
     */
    public function getIsFailedAttribute()
    {
        return in_array($this->status, ['failed', 'returned']);
    }

    /**
     * Check if shipment is cancellable
     */
    public function getIsCancellableAttribute()
    {
        return in_array($this->status, ['pending']);
    }

    /**
     * Check if POD is available
     */
    public function getHasPodAttribute()
    {
        return !is_null($this->pod_signature) || !is_null($this->pod_photo);
    }

    /**
     * Get POD signature URL
     */
    public function getPodSignatureUrlAttribute()
    {
        return $this->pod_signature ? asset('storage/' . $this->pod_signature) : null;
    }

    /**
     * Get POD photo URL
     */
    public function getPodPhotoUrlAttribute()
    {
        return $this->pod_photo ? asset('storage/' . $this->pod_photo) : null;
    }

    /**
     * Get delivery timeline summary
     */
    public function getTimelineSummaryAttribute()
    {
        $trackings = $this->trackings()->take(5)->get();
        return $trackings->map(function($track) {
            return [
                'status' => $track->status,
                'location' => $track->location,
                'time' => $track->tracked_at->diffForHumans(),
                'datetime' => $track->tracked_at->format('Y-m-d H:i:s')
            ];
        });
    }

    /**
     * Check if live tracking is available
     */
    public function getHasLiveLocationAttribute()
    {
        return !is_null($this->current_latitude) &&
               !is_null($this->current_longitude) &&
               !is_null($this->last_location_update);
    }

    /**
     * Get last ping time in human readable format
     */
    public function getLastPingHumanAttribute()
    {
        return $this->last_ping_at ? $this->last_ping_at->diffForHumans() : 'Never';
    }

    /**
     * Get battery status
     */
    public function getBatteryStatusAttribute()
    {
        if (is_null($this->battery_level)) {
            return 'Unknown';
        }

        if ($this->battery_level > 70) {
            return 'High 🔋';
        } elseif ($this->battery_level > 30) {
            return 'Medium ⚡';
        } elseif ($this->battery_level > 10) {
            return 'Low ⚠️';
        } else {
            return 'Critical 🔴';
        }
    }

    /**
     * Get GPS signal strength
     */
    public function getGpsSignalAttribute()
    {
        return $this->gps_signal_strength ?? 'Unknown';
    }

    /**
     * Check if delivery requires OTP
     */
    public function getRequiresOtpAttribute()
    {
        return !is_null($this->delivery_otp) && is_null($this->otp_verified_at);
    }

    /**
     * Check if OTP is verified
     */
    public function getIsOtpVerifiedAttribute()
    {
        return !is_null($this->otp_verified_at);
    }

    /**
     * Get delivery attempts count
     */
    public function getAttemptsCountAttribute()
    {
        return $this->delivery_attempts ?? 0;
    }

    /**
     * Get last attempt time
     */
    public function getLastAttemptHumanAttribute()
    {
        return $this->last_delivery_attempt_at ? $this->last_delivery_attempt_at->diffForHumans() : 'Never';
    }

    /* ==================== METHODS ==================== */

    /**
     * Update tracking status
     */
    public function updateTracking($status, $location = null, $remarks = null, $latitude = null, $longitude = null)
    {
        // Create tracking record
        $tracking = $this->trackings()->create([
            'status' => $status,
            'location' => $location ?? $this->city,
            'latitude' => $latitude ?? $this->current_latitude,
            'longitude' => $longitude ?? $this->current_longitude,
            'remarks' => $remarks,
            'tracked_at' => now()
        ]);

        // Update shipment status
        $this->status = $status;

        // Update dates based on status
        if ($status === 'delivered') {
            $this->actual_delivery_date = now();
        } elseif ($status === 'picked') {
            $this->pickup_date = now();
        }

        $this->save();

        // Create event
        $this->createEvent('status_updated', [
            'old_status' => $this->getOriginal('status'),
            'new_status' => $status,
            'location' => $location
        ]);

        return $tracking;
    }

    /**
     * Create a shipment event
     */
    public function createEvent($eventType, $metadata = [])
    {
        if (class_exists(ShipmentEvent::class)) {
            return $this->events()->create([
                'event_type' => $eventType,
                'status_from' => $this->getOriginal('status'),
                'status_to' => $this->status,
                'location' => $this->city,
                'latitude' => $this->current_latitude,
                'longitude' => $this->current_longitude,
                'metadata' => $metadata,
                'triggered_by' => auth()->check() ? 'user' : 'system',
                'triggered_by_id' => auth()->id(),
                'occurred_at' => now()
            ]);
        }

        return null;
    }

    /**
     * Generate unique shipment number
     */
    public function generateShipmentNumber()
    {
        $prefix = 'SHIP';
        $year = date('Y');
        $month = date('m');

        $lastShipment = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastShipment ? intval(substr($lastShipment->shipment_number, -4)) + 1 : 1;

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate tracking number (Flipkart/Amazon style)
     */
    public function generateTrackingNumber()
    {
        $prefix = 'TRK';
        $date = now()->format('Ymd');
        $id = str_pad($this->id ?? rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        return $prefix . $date . '-' . $id;
    }

    /**
     * Assign to delivery agent
     */
    public function assignToAgent($agentId, $userId = null)
    {
        $oldAgent = $this->assigned_to;

        $this->assigned_to = $agentId;
        $this->save();

        $this->updateTracking('assigned', null, "Assigned to agent ID: {$agentId}");

        $this->createEvent('agent_assigned', [
            'old_agent' => $oldAgent,
            'new_agent' => $agentId,
            'assigned_by' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Update live location
     */
    public function updateLiveLocation($latitude, $longitude, $accuracy = null, $battery = null, $gpsStrength = null)
    {
        $this->current_latitude = $latitude;
        $this->current_longitude = $longitude;
        $this->location_accuracy = $accuracy;
        $this->last_location_update = now();
        $this->last_ping_at = now();

        if ($battery) {
            $this->battery_level = $battery;
        }

        if ($gpsStrength) {
            $this->gps_signal_strength = $gpsStrength;
        }

        $this->save();

        return $this;
    }

    /**
     * Generate delivery OTP
     */
    public function generateOtp()
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->delivery_otp = $otp;
        $this->otp_verified_at = null;
        $this->save();

        return $otp;
    }

    /**
     * Verify delivery OTP
     */
    public function verifyOtp($otp)
    {
        if ($this->delivery_otp === $otp && is_null($this->otp_verified_at)) {
            $this->otp_verified_at = now();
            $this->save();

            $this->createEvent('otp_verified');

            return true;
        }

        return false;
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered($signature = null, $photo = null, $notes = null)
    {
        $this->status = 'delivered';
        $this->actual_delivery_date = now();

        if ($signature) {
            $this->pod_signature = $signature;
        }

        if ($photo) {
            $this->pod_photo = $photo;
        }

        if ($notes) {
            $this->delivery_notes = $notes;
        }

        $this->save();

        $this->updateTracking('delivered', $this->city, $notes);

        return true;
    }

    /**
     * Mark as failed delivery
     */
    public function markAsFailed($reason)
    {
        $this->status = 'failed';
        $this->status_note = $reason;

        $this->delivery_attempts = ($this->delivery_attempts ?? 0) + 1;
        $this->last_delivery_attempt_at = now();

        $this->save();

        $this->updateTracking('failed', $this->city, $reason);

        return true;
    }

    /**
     * Mark as returned
     */
    public function markAsReturned($reason, $initiatedBy = null)
    {
        $this->status = 'returned';
        $this->return_reason = $reason;
        $this->return_initiated_by = $initiatedBy ?? auth()->id();

        $this->save();

        $this->updateTracking('returned', $this->city, $reason);

        return true;
    }

    /**
     * Calculate distance to destination
     */
    public function distanceToDestination()
    {
        if (!$this->current_latitude || !$this->current_longitude ||
            !$this->destination_latitude || !$this->destination_longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->current_latitude);
        $lonFrom = deg2rad($this->current_longitude);
        $latTo = deg2rad($this->destination_latitude);
        $lonTo = deg2rad($this->destination_longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Calculate progress percentage based on location
     */
    public function calculateLocationProgress()
    {
        if (!$this->current_latitude || !$this->current_longitude ||
            !$this->destination_latitude || !$this->destination_longitude) {
            return $this->progress_percentage;
        }

        // Calculate using straight-line distance (simplified)
        $totalDistance = $this->calculateDistance(
            $this->getOriginal('destination_latitude') ?? 23.0225,
            $this->getOriginal('destination_longitude') ?? 72.5714,
            $this->destination_latitude,
            $this->destination_longitude
        );

        $remainingDistance = $this->distanceToDestination();

        if ($totalDistance == 0) {
            return 100;
        }

        $progress = (($totalDistance - $remainingDistance) / $totalDistance) * 100;

        return min(100, max(0, round($progress)));
    }

    /**
     * Calculate distance between two coordinates
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
