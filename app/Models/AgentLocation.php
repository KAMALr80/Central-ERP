<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agent_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_id',
        'shipment_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'battery_level',
        'recorded_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'integer',
        'battery_level' => 'integer',
        'recorded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'recorded_at',
        'created_at',
        'updated_at'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the delivery agent associated with this location.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgent::class, 'agent_id', 'user_id');
    }

    /**
     * Get the user associated with this location (alternative relationship).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the shipment associated with this location.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope a query to only include locations for a specific agent.
     */
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope a query to only include locations for a specific shipment.
     */
    public function scopeForShipment($query, $shipmentId)
    {
        return $query->where('shipment_id', $shipmentId);
    }

    /**
     * Scope a query to only include locations from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('recorded_at', today());
    }

    /**
     * Scope a query to only include locations from last N hours.
     */
    public function scopeLastHours($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope a query to only include locations with good accuracy.
     */
    public function scopeWithGoodAccuracy($query, $maxAccuracy = 50)
    {
        return $query->where('accuracy', '<=', $maxAccuracy);
    }

    /**
     * Scope a query to order by recorded time (latest first).
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }

    /**
     * Scope a query to order by recorded time (oldest first).
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('recorded_at', 'asc');
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get the location as an array.
     */
    public function getLocationArrayAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
            'accuracy' => $this->accuracy,
            'speed' => $this->speed,
            'heading' => $this->heading,
            'battery' => $this->battery_level,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'recorded_at_human' => $this->recorded_at?->diffForHumans()
        ];
    }

    /**
     * Get the location as a formatted string.
     */
    public function getLocationStringAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Get the Google Maps URL for this location.
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Get the OpenStreetMap URL for this location.
     */
    public function getOpenStreetMapUrlAttribute(): string
    {
        return "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}";
    }

    /**
     * Get the accuracy status (good, fair, poor).
     */
    public function getAccuracyStatusAttribute(): string
    {
        if (!$this->accuracy) return 'unknown';

        if ($this->accuracy <= 20) return 'excellent';
        if ($this->accuracy <= 50) return 'good';
        if ($this->accuracy <= 100) return 'fair';
        return 'poor';
    }

    /**
     * Get the battery status (full, medium, low, critical).
     */
    public function getBatteryStatusAttribute(): string
    {
        if (!$this->battery_level) return 'unknown';

        if ($this->battery_level >= 75) return 'full';
        if ($this->battery_level >= 50) return 'medium';
        if ($this->battery_level >= 20) return 'low';
        return 'critical';
    }

    /**
     * Get the speed in km/h as formatted string.
     */
    public function getFormattedSpeedAttribute(): string
    {
        if (!$this->speed) return 'N/A';

        return round($this->speed, 1) . ' km/h';
    }

    /* ==================== HELPER METHODS ==================== */

    /**
     * Check if this location is recent (within last N minutes).
     */
    public function isRecent($minutes = 5): bool
    {
        if (!$this->recorded_at) return false;

        return $this->recorded_at->diffInMinutes(now()) <= $minutes;
    }

    /**
     * Check if this location has good accuracy.
     */
    public function hasGoodAccuracy(): bool
    {
        return $this->accuracy && $this->accuracy <= 50;
    }

    /**
     * Get distance to another location in kilometers.
     */
    public function distanceTo($latitude, $longitude): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $latTo = deg2rad($latitude);
        $lonFrom = deg2rad($this->longitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Get distance to another location in meters.
     */
    public function distanceToInMeters($latitude, $longitude): float
    {
        return $this->distanceTo($latitude, $longitude) * 1000;
    }

    /**
     * Get distance to a shipment's destination.
     */
    public function distanceToShipment(Shipment $shipment): ?float
    {
        if (!$shipment->destination_latitude || !$shipment->destination_longitude) {
            return null;
        }

        return $this->distanceTo(
            $shipment->destination_latitude,
            $shipment->destination_longitude
        );
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get the latest location for an agent.
     */
    public static function getLatestForAgent($agentId): ?self
    {
        return self::forAgent($agentId)
            ->latestFirst()
            ->first();
    }

    /**
     * Get all locations for an agent on a specific date.
     */
    public static function getForAgentOnDate($agentId, $date): \Illuminate\Database\Eloquent\Collection
    {
        return self::forAgent($agentId)
            ->whereDate('recorded_at', $date)
            ->oldestFirst()
            ->get();
    }

    /**
     * Get the route path for an agent (all locations ordered by time).
     */
    public static function getRoutePathForAgent($agentId, $startDate = null, $endDate = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::forAgent($agentId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->oldestFirst();

        if ($startDate) {
            $query->whereDate('recorded_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('recorded_at', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Get the total distance traveled by an agent between two locations.
     */
    public static function getTotalDistanceForAgent($agentId, $startDate = null, $endDate = null): float
    {
        $locations = self::getRoutePathForAgent($agentId, $startDate, $endDate);

        if ($locations->count() < 2) {
            return 0;
        }

        $totalDistance = 0;

        for ($i = 0; $i < $locations->count() - 1; $i++) {
            $current = $locations[$i];
            $next = $locations[$i + 1];

            $totalDistance += $current->distanceTo($next->latitude, $next->longitude);
        }

        return round($totalDistance, 2);
    }

    /**
     * Clean up old location records (keep last 7 days by default).
     */
    public static function cleanupOldRecords($daysToKeep = 7): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return self::where('recorded_at', '<', $cutoffDate)->delete();
    }

    /**
     * Get location statistics for an agent.
     */
    public static function getStatisticsForAgent($agentId, $date = null): array
    {
        $date = $date ?: today();

        $locations = self::forAgent($agentId)
            ->whereDate('recorded_at', $date)
            ->get();

        if ($locations->isEmpty()) {
            return [
                'total_points' => 0,
                'total_distance' => 0,
                'avg_accuracy' => null,
                'max_speed' => null,
                'avg_speed' => null,
                'start_time' => null,
                'end_time' => null,
                'duration_minutes' => 0
            ];
        }

        $totalDistance = 0;
        $speeds = [];
        $accuracies = [];

        for ($i = 0; $i < $locations->count() - 1; $i++) {
            $current = $locations[$i];
            $next = $locations[$i + 1];

            $totalDistance += $current->distanceTo($next->latitude, $next->longitude);

            if ($current->speed) {
                $speeds[] = $current->speed;
            }

            if ($current->accuracy) {
                $accuracies[] = $current->accuracy;
            }
        }

        return [
            'total_points' => $locations->count(),
            'total_distance' => round($totalDistance, 2),
            'avg_accuracy' => !empty($accuracies) ? round(array_sum($accuracies) / count($accuracies), 1) : null,
            'max_speed' => !empty($speeds) ? round(max($speeds), 1) : null,
            'avg_speed' => !empty($speeds) ? round(array_sum($speeds) / count($speeds), 1) : null,
            'start_time' => $locations->first()->recorded_at,
            'end_time' => $locations->last()->recorded_at,
            'duration_minutes' => $locations->first()->recorded_at->diffInMinutes($locations->last()->recorded_at)
        ];
    }
}
