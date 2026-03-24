<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAgent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Basic Information
        'user_id',
        'agent_code',
        'name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'pincode',
        'approval_status',  // pending_approval, approved, rejected
        'approved_by',
        'approved_at',
        'rejection_reason',

        // Vehicle Details
        'vehicle_type',
        'vehicle_number',
        'license_number',

        // Documents
        'aadhar_card',
        'driving_license',
        'photo',

        // Bank Details
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id',

        // Employment
        'employment_type',
        'joining_date',
        'salary',
        'commission_type',
        'commission_value',

        // Service Areas
        'service_areas',

        // Location Tracking
        'current_latitude',
        'current_longitude',
        'last_location_update',
        'last_online_at',
        'last_offline_at',
        'total_online_minutes',

        // Device Information
        'device_id',
        'device_model',
        'app_version',
        'fcm_token',

        // Performance Metrics
        'total_deliveries',
        'successful_deliveries',
        'rating',
        'avg_delivery_time',
        'on_time_delivery_rate',
        'customer_feedback_count',

        // Shift Details
        'shift_start_time',
        'shift_end_time',
        'current_shift_id',

        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_group',

        // Status
        'is_active',
        'status'
    ];

    protected $casts = [
        // JSON fields
        'service_areas' => 'array',
        'approved_at' => 'datetime',
        // Dates
        'joining_date' => 'date',
        'last_location_update' => 'datetime',
        'last_online_at' => 'datetime',
        'last_offline_at' => 'datetime',

        // Decimals
        'salary' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'rating' => 'decimal:2',
        'avg_delivery_time' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',

        // Coordinates
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',

        // Integers
        'total_deliveries' => 'integer',
        'successful_deliveries' => 'integer',
        'total_online_minutes' => 'integer',
        'customer_feedback_count' => 'integer',

        // Times
        'shift_start_time' => 'datetime:H:i',
        'shift_end_time' => 'datetime:H:i',

        // Booleans
        'is_active' => 'boolean',
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the user account associated with this delivery agent
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get shipments assigned to this delivery agent
     */
    public function assignedShipments()
    {
        return $this->hasMany(Shipment::class, 'assigned_to', 'user_id');
    }

    /**
     * Get current active shipments
     */
    public function activeShipments()
    {
        return $this->assignedShipments()
            ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery']);
    }

    /**
     * Get completed shipments
     */
    public function completedShipments()
    {
        return $this->assignedShipments()->where('status', 'delivered');
    }

    /**
     * Get failed shipments
     */
    public function failedShipments()
    {
        return $this->assignedShipments()->whereIn('status', ['failed', 'returned']);
    }

    /**
     * Get today's shipments
     */
    public function todaysShipments()
    {
        return $this->assignedShipments()->whereDate('created_at', today());
    }

    /**
     * Get performance logs
     */
    public function performanceLogs()
    {
        return $this->hasMany(AgentPerformanceLog::class, 'agent_id');
    }

    /**
     * Get today's performance log
     */
    public function todayPerformance()
    {
        return $this->hasOne(AgentPerformanceLog::class)
            ->whereDate('log_date', today());
    }

    /**
     * Get route history
     */
    public function routeHistory()
    {
        return $this->hasMany(RouteHistory::class);
    }

    /**
     * Get saved routes
     */
    public function savedRoutes()
    {
        return $this->hasMany(SavedRoute::class, 'agent_id');
    }

    /**
     * Get location history
     */
    public function locationHistory()
    {
        return $this->hasMany(AgentLocation::class, 'agent_id', 'user_id');
    }

    /**
     * Get current shipment (active delivery)
     */
    public function currentShipment()
    {
        return $this->hasOne(Shipment::class, 'assigned_to', 'user_id')
            ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery']);
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope available agents
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->where('is_active', true);
    }

    /**
     * Scope busy agents
     */
    public function scopeBusy($query)
    {
        return $query->where('status', 'busy');
    }

    /**
     * Scope offline agents
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    /**
     * Scope active agents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope by vehicle type
     */
    public function scopeWithVehicle($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    /**
     * Scope agents with live location
     */
    public function scopeWithLiveLocation($query)
    {
        return $query->whereNotNull('current_latitude')
                     ->whereNotNull('current_longitude')
                     ->whereNotNull('last_location_update');
    }

    /**
     * Scope agents online in last N minutes
     */
    public function scopeOnlineInLast($query, $minutes = 15)
    {
        return $query->where('last_online_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope top performers
     */
    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->where('total_deliveries', '>', 0)
                     ->orderBy('rating', 'desc')
                     ->orderBy('successful_deliveries', 'desc')
                     ->limit($limit);
    }

    /**
     * Scope by service area
     */
    public function scopeServesArea($query, $area)
    {
        return $query->whereJsonContains('service_areas', $area);
    }

    /**
     * Scope by distance from coordinates
     */
    public function scopeNearby($query, $lat, $lng, $radius = 10)
    {
        // Haversine formula for nearby search
        $haversine = "(6371 * acos(cos(radians($lat))
                      * cos(radians(current_latitude))
                      * cos(radians(current_longitude) - radians($lng))
                      + sin(radians($lat))
                      * sin(radians(current_latitude))))";

        return $query->select('*')
                     ->selectRaw("{$haversine} AS distance")
                     ->whereRaw("{$haversine} <= ?", [$radius])
                     ->orderBy('distance');
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get full name with agent code
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->agent_code . ')';
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_deliveries > 0) {
            return round(($this->successful_deliveries / $this->total_deliveries) * 100, 2);
        }
        return 0;
    }

    /**
     * Get formatted success rate
     */
    public function getFormattedSuccessRateAttribute()
    {
        return $this->success_rate . '%';
    }

    /**
     * Get formatted salary
     */
    public function getFormattedSalaryAttribute()
    {
        return '₹ ' . number_format($this->salary, 2);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'available' => 'success',
            'busy' => 'warning',
            'offline' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get status with icon
     */
    public function getStatusWithIconAttribute()
    {
        $icons = [
            'available' => '✅ Available',
            'busy' => '🚚 Busy',
            'offline' => '⚫ Offline'
        ];

        return $icons[$this->status] ?? $this->status;
    }

    /**
     * Get vehicle type with icon
     */
    public function getVehicleWithIconAttribute()
    {
        $icons = [
            'bike' => '🏍️ Bike',
            'cycle' => '🚲 Cycle',
            'van' => '🚐 Van',
            'truck' => '🚛 Truck'
        ];

        return $icons[$this->vehicle_type] ?? $this->vehicle_type ?? 'Not Assigned';
    }

    /**
     * Get current location as string
     */
    public function getCurrentLocationAttribute()
    {
        if ($this->current_latitude && $this->current_longitude) {
            return $this->current_latitude . ', ' . $this->current_longitude;
        }
        return null;
    }

    /**
     * Get current location as array with additional info
     */
    public function getCurrentLocationArrayAttribute()
    {
        if ($this->current_latitude && $this->current_longitude) {
            return [
                'lat' => (float) $this->current_latitude,
                'lng' => (float) $this->current_longitude,
                'updated_at' => $this->last_location_update?->diffForHumans()
            ];
        }
        return null;
    }

    /**
     * Get last seen status
     */
    public function getLastSeenAttribute()
    {
        if ($this->status === 'online' && $this->last_online_at) {
            return $this->last_online_at->diffForHumans();
        }
        return $this->last_offline_at?->diffForHumans() ?? 'Never';
    }

    /**
     * Check if agent is available
     */
    public function getIsAvailableAttribute()
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Check if agent is busy
     */
    public function getIsBusyAttribute()
    {
        return $this->status === 'busy';
    }

    /**
     * Check if agent is online (location updated within last 5 minutes)
     */
    public function getIsOnlineAttribute()
    {
        if (!$this->last_location_update) return false;
        return $this->last_location_update->diffInMinutes(now()) <= 5;
    }

    /**
     * Get online status with indicator
     */
    public function getOnlineStatusAttribute()
    {
        return $this->is_online ? 'Online' : 'Offline';
    }

    /**
     * Get total earnings (base salary + commission)
     */
    public function getTotalEarningsAttribute()
    {
        $base = $this->salary ?? 0;
        $commission = 0;

        if ($this->commission_type === 'fixed') {
            $commission = ($this->commission_value ?? 0) * $this->successful_deliveries;
        } elseif ($this->commission_type === 'percentage') {
            // Calculate from shipment values
            $totalShipmentValue = $this->assignedShipments()
                ->where('status', 'delivered')
                ->sum('total_charge');
            $commission = ($totalShipmentValue * ($this->commission_value ?? 0)) / 100;
        }

        return $base + $commission;
    }

    /**
     * Get formatted total earnings
     */
    public function getFormattedEarningsAttribute()
    {
        return '₹ ' . number_format($this->total_earnings, 2);
    }

    /**
     * Get average rating with stars
     */
    public function getRatingStarsAttribute()
    {
        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $stars = str_repeat('★', $fullStars);
        $stars .= $halfStar ? '½' : '';
        $stars .= str_repeat('☆', $emptyStars);

        return $stars;
    }

    /**
     * Get today's delivery count
     */
    public function getTodayDeliveriesAttribute()
    {
        return $this->assignedShipments()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get today's successful deliveries
     */
    public function getTodaySuccessfulAttribute()
    {
        return $this->assignedShipments()
            ->whereDate('actual_delivery_date', today())
            ->where('status', 'delivered')
            ->count();
    }

    /**
     * Get current workload
     */
    public function getCurrentWorkloadAttribute()
    {
        return $this->activeShipments()->count();
    }

    /**
     * Get device info as string
     */
    public function getDeviceInfoAttribute()
    {
        if ($this->device_model && $this->app_version) {
            return $this->device_model . ' (v' . $this->app_version . ')';
        }
        return 'Unknown';
    }

    /**
     * Get formatted shift time
     */
    public function getShiftTimeAttribute()
    {
        if ($this->shift_start_time && $this->shift_end_time) {
            return $this->shift_start_time->format('h:i A') . ' - ' .
                   $this->shift_end_time->format('h:i A');
        }
        return 'Not Set';
    }

    /**
     * Get emergency contact info
     */
    public function getEmergencyContactAttribute()
    {
        if ($this->emergency_contact_name && $this->emergency_contact_phone) {
            return $this->emergency_contact_name . ' (' . $this->emergency_contact_phone . ')';
        }
        return 'Not Provided';
    }

    /**
     * Get blood group with icon
     */
    public function getBloodGroupWithIconAttribute()
    {
        return $this->blood_group ? '🩸 ' . $this->blood_group : 'Not Provided';
    }

    /* ==================== METHODS ==================== */

    /**
     * Update agent status
     */
    public function updateStatus($status)
    {
        $oldStatus = $this->status;

        $this->status = $status;

        if ($status === 'available' || $status === 'busy') {
            $this->last_online_at = now();
        } elseif ($status === 'offline') {
            $this->last_offline_at = now();

            if ($this->last_online_at) {
                $onlineMinutes = $this->last_online_at->diffInMinutes(now());
                $this->total_online_minutes = ($this->total_online_minutes ?? 0) + $onlineMinutes;
            }
        }

        $this->save();

        // Log performance
        $this->logPerformance();

        return true;
    }

    /**
     * Update location - Enhanced version with history tracking
     */
    public function updateLocation($latitude, $longitude, $accuracy = null, $shipmentId = null)
    {
        $wasOffline = $this->status === 'offline';

        $this->current_latitude = $latitude;
        $this->current_longitude = $longitude;
        $this->last_location_update = now();

        // Auto set to available if moving and was offline
        if ($wasOffline && $this->is_active) {
            $this->status = 'available';
            $this->last_online_at = now();
        }

        $this->save();

        // Save to location history if table exists
        if (class_exists(\App\Models\AgentLocation::class) && \Illuminate\Support\Facades\Schema::hasTable('agent_locations')) {
            try {
                \App\Models\AgentLocation::create([
                    'agent_id' => $this->user_id,
                    'shipment_id' => $shipmentId,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'accuracy' => $accuracy,
                    'recorded_at' => now()
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to save location history: ' . $e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Update device info
     */
    public function updateDeviceInfo($deviceId, $deviceModel, $appVersion, $fcmToken = null)
    {
        $this->device_id = $deviceId;
        $this->device_model = $deviceModel;
        $this->app_version = $appVersion;

        if ($fcmToken) {
            $this->fcm_token = $fcmToken;
        }

        $this->save();

        return $this;
    }

    /**
     * Increment delivery stats
     */
    public function incrementDeliveries($successful = true)
    {
        $this->total_deliveries = ($this->total_deliveries ?? 0) + 1;

        if ($successful) {
            $this->successful_deliveries = ($this->successful_deliveries ?? 0) + 1;
        }

        $this->save();

        // Update performance log
        $this->logPerformance();

        return $this;
    }

    /**
     * Update rating
     */
    public function updateRating($newRating)
    {
        $totalFeedback = ($this->customer_feedback_count ?? 0) + 1;
        $currentTotal = ($this->rating ?? 0) * ($this->customer_feedback_count ?? 0);

        $this->rating = ($currentTotal + $newRating) / $totalFeedback;
        $this->customer_feedback_count = $totalFeedback;

        $this->save();

        return $this->rating;
    }

    /**
     * Update average delivery time
     */
    public function updateAvgDeliveryTime($deliveryTimeMinutes)
    {
        $totalDeliveries = $this->successful_deliveries ?? 0;
        $currentTotal = ($this->avg_delivery_time ?? 0) * ($totalDeliveries - 1);

        $this->avg_delivery_time = ($currentTotal + $deliveryTimeMinutes) / $totalDeliveries;
        $this->save();

        return $this->avg_delivery_time;
    }

    /**
     * Update on-time delivery rate
     */
    public function updateOnTimeRate($wasOnTime)
    {
        $totalDeliveries = $this->successful_deliveries ?? 0;
        $currentOnTime = ($this->on_time_delivery_rate ?? 0) * ($totalDeliveries - 1) / 100;

        $newOnTime = $currentOnTime + ($wasOnTime ? 1 : 0);
        $this->on_time_delivery_rate = ($newOnTime / $totalDeliveries) * 100;

        $this->save();

        return $this->on_time_delivery_rate;
    }

    /**
     * Assign shift
     */
    public function assignShift($shiftId, $startTime, $endTime)
    {
        $this->current_shift_id = $shiftId;
        $this->shift_start_time = $startTime;
        $this->shift_end_time = $endTime;
        $this->save();

        return $this;
    }

    /**
     * End current shift
     */
    public function endShift()
    {
        $this->current_shift_id = null;
        $this->shift_start_time = null;
        $this->shift_end_time = null;
        $this->save();

        return $this;
    }

    /**
     * Check if within shift hours
     */
    public function isWithinShift()
    {
        if (!$this->shift_start_time || !$this->shift_end_time) {
            return true; // No shift restrictions
        }

        $now = now();
        $start = $this->shift_start_time;
        $end = $this->shift_end_time;

        return $now->between($start, $end);
    }

    /**
     * Get nearest available agents
     */
    public static function findNearest($latitude, $longitude, $radius = 10, $limit = 5)
    {
        return self::available()
            ->withLiveLocation()
            ->nearby($latitude, $longitude, $radius)
            ->limit($limit)
            ->get();
    }

    /**
     * Log daily performance
     */
    public function logPerformance()
    {
        if (!class_exists(AgentPerformanceLog::class)) {
            return null;
        }

        $log = $this->performanceLogs()->firstOrNew(['log_date' => today()]);

        $log->shipments_assigned = $this->assignedShipments()->whereDate('created_at', today())->count();
        $log->shipments_delivered = $this->assignedShipments()
            ->whereDate('actual_delivery_date', today())
            ->where('status', 'delivered')
            ->count();
        $log->shipments_failed = $this->assignedShipments()
            ->whereDate('created_at', today())
            ->whereIn('status', ['failed', 'returned'])
            ->count();

        // Calculate total distance (simplified)
        $log->total_distance_km = 0; // Would need actual tracking data

        // Calculate active time
        if ($this->last_online_at && $this->last_offline_at) {
            $log->active_minutes = $this->last_online_at->diffInMinutes($this->last_offline_at);
        }

        $log->average_rating = $this->rating;

        // Calculate earnings
        if ($this->commission_type === 'fixed') {
            $log->commission_earned = ($this->commission_value ?? 0) * $log->shipments_delivered;
        }

        $log->total_earnings = ($this->salary ?? 0) + ($log->commission_earned ?? 0);

        $log->save();

        return $log;
    }

    /**
     * Generate unique agent code
     */
    public static function generateAgentCode()
    {
        $prefix = 'AG';
        $year = date('y');
        $month = date('m');

        $lastAgent = self::whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAgent) {
            $lastCode = substr($lastAgent->agent_code, -4);
            $sequence = intval($lastCode) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'available' => self::available()->count(),
            'busy' => self::busy()->count(),
            'offline' => self::offline()->count(),
            'active' => self::active()->count(),
            'online_now' => self::onlineInLast(15)->count(),
            'total_deliveries' => self::sum('successful_deliveries'),
            'top_performers' => self::topPerformers(5)->get(),
        ];
    }

    /* ==================== BOOT METHOD ==================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agent) {
            if (empty($agent->agent_code)) {
                $agent->agent_code = self::generateAgentCode();
            }

            if (empty($agent->status)) {
                $agent->status = 'offline';
            }

            if (empty($agent->total_deliveries)) {
                $agent->total_deliveries = 0;
                $agent->successful_deliveries = 0;
            }
        });

        static::created(function ($agent) {
            // Create initial performance log
            try {
                $agent->logPerformance();
            } catch (\Exception $e) {
                \Log::error('Failed to create performance log: ' . $e->getMessage());
            }
        });

        static::updated(function ($agent) {
            // Log performance daily
            if ($agent->isDirty('status') && $agent->status === 'offline') {
                try {
                    $agent->logPerformance();
                } catch (\Exception $e) {
                    \Log::error('Failed to update performance log: ' . $e->getMessage());
                }
            }
        });
    }
}
