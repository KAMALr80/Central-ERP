<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Auditable;

class User extends Authenticatable
{
    // use HasFactory, Notifiable, SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Auditable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
          'two_factor_enabled',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',

        // ✅ NEW FIELDS
        'agent_id',
        'is_online',
        'last_login_at',
        'last_login_ip',
        'login_count',
        'fcm_token',

        // Security
        'email_verified_at',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_online' => 'boolean',
        'login_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /* ==================== EXISTING RELATIONSHIPS ==================== */

    /**
     * Get the employee associated with this user
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get sales created by this user
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    /**
     * Get purchases created by this user
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'user_id');
    }

    /**
     * Get shipments created by this user
     */
    public function createdShipments()
    {
        return $this->hasMany(Shipment::class, 'created_by');
    }

    /**
     * Get shipments assigned to this user (as delivery agent)
     */
    public function assignedShipments()
    {
        return $this->hasMany(Shipment::class, 'assigned_to');
    }

    /**
     * Get attendance marked by this user
     */
    public function markedAttendances()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    /**
     * Get leaves approved by this user
     */
    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    /**
     * Get leaves rejected by this user
     */
    public function rejectedLeaves()
    {
        return $this->hasMany(Leave::class, 'rejected_by');
    }

    /* ==================== NEW RELATIONSHIPS ==================== */

    /**
     * Get the delivery agent profile associated with this user
     */
    public function deliveryAgent()
    {
        return $this->hasOne(DeliveryAgent::class, 'user_id');
    }

    /**
     * Get all shipments where this user updated tracking
     */
    public function trackedShipments()
    {
        return $this->hasMany(ShipmentTracking::class, 'updated_by');
    }

    /**
     * Get shipment events triggered by this user
     */
    public function triggeredEvents()
    {
        return $this->hasMany(ShipmentEvent::class, 'triggered_by_id');
    }

    /**
     * Get POD verifications by this user
     */
    public function verifiedPods()
    {
        return $this->hasMany(Shipment::class, 'pod_verified_by');
    }

    /**
     * Get return initiations by this user
     */
    public function initiatedReturns()
    {
        return $this->hasMany(Shipment::class, 'return_initiated_by');
    }

    /**
     * Get customers created by this user
     */
    public function createdCustomers()
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    /**
     * Get customers updated by this user
     */
    public function updatedCustomers()
    {
        return $this->hasMany(Customer::class, 'updated_by');
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get display name with role
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }

    /**
     * Get display name for delivery agent
     */
    public function getAgentDisplayNameAttribute()
    {
        if ($this->deliveryAgent) {
            return $this->deliveryAgent->name . ' (' . $this->deliveryAgent->agent_code . ')';
        }

        return $this->display_name;
    }

    /**
     * Get user role with badge color
     */
    public function getRoleBadgeAttribute()
    {
        $colors = [
            'admin' => 'danger',
            'hr' => 'success',
            'staff' => 'info',
            'delivery_agent' => 'warning'
        ];

        return $colors[$this->role] ?? 'secondary';
    }

    /**
     * Get user role display name
     */
    public function getRoleDisplayAttribute()
    {
        $roles = [
            'admin' => 'Administrator',
            'hr' => 'HR Manager',
            'staff' => 'Staff Member',
            'delivery_agent' => 'Delivery Agent'
        ];

        return $roles[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Get status with badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'suspended' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'active' => '✅ Active',
            'inactive' => '⭕ Inactive',
            'pending' => '⏳ Pending',
            'suspended' => '🚫 Suspended'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get online status with icon
     */
    public function getOnlineStatusAttribute()
    {
        if (!$this->is_online) {
            return '⚫ Offline';
        }

        if ($this->last_login_at && $this->last_login_at->gt(now()->subMinutes(5))) {
            return '🟢 Online';
        }

        return '🟡 Away';
    }

    /**
     * Get last seen time
     */
    public function getLastSeenAttribute()
    {
        if ($this->is_online && $this->last_login_at) {
            return $this->last_login_at->diffForHumans();
        }

        return 'Never';
    }

    /**
     * Get formatted last login
     */
    public function getFormattedLastLoginAttribute()
    {
        if (!$this->last_login_at) {
            return 'Never logged in';
        }

        return $this->last_login_at->format('d M Y, h:i A') . ' (IP: ' . ($this->last_login_ip ?? 'Unknown') . ')';
    }

    /**
     * Get login count display
     */
    public function getLoginCountDisplayAttribute()
    {
        $count = $this->login_count ?? 0;

        if ($count === 0) {
            return 'Never logged in';
        }

        return $count . ' ' . ($count === 1 ? 'time' : 'times');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is HR
     */
    public function isHr()
    {
        return $this->role === 'hr';
    }

    /**
     * Check if user is staff
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is delivery agent
     */
    public function isDeliveryAgent()
    {
        return $this->role === 'delivery_agent';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission)
    {
        // Admin has all permissions
        if ($this->role === 'admin') {
            return true;
        }

        // Fetch role permissions
        // Use static cache to avoid multiple DB queries in same request
        static $rolePermissions = [];

        if (!isset($rolePermissions[$this->role])) {
            $roleRecord = Role::where('slug', $this->role)->first();
            $rolePermissions[$this->role] = $roleRecord ? ($roleRecord->permissions ?? []) : [];
        }

        return in_array($permission, $rolePermissions[$this->role]);
    }

    /**
     * Check if user can access logistics module
     */
    public function canAccessLogistics()
    {
        return in_array($this->role, ['admin', 'hr', 'delivery_agent']);
    }

    /**
     * Check if user can manage agents
     */
    public function canManageAgents()
    {
        return in_array($this->role, ['admin', 'hr']);
    }

    /**
     * Check if user can create shipments
     */
    public function canCreateShipments()
    {
        return in_array($this->role, ['admin', 'hr', 'staff']);
    }

    /**
     * Get FCM token masked for security
     */
    public function getFcmTokenMaskedAttribute()
    {
        if (!$this->fcm_token) {
            return null;
        }

        return substr($this->fcm_token, 0, 10) . '...' . substr($this->fcm_token, -10);
    }

    /* ==================== METHODS ==================== */

    /**
     * Record user login
     */
    public function recordLogin($ip = null)
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ip ?? request()->ip();
        $this->login_count = ($this->login_count ?? 0) + 1;
        $this->is_online = true;
        $this->save();

        return $this;
    }

    /**
     * Record user logout
     */
    public function recordLogout()
    {
        $this->is_online = false;
        $this->save();

        return $this;
    }

    /**
     * Update online status
     */
    public function updateOnlineStatus($isOnline = true)
    {
        $this->is_online = $isOnline;

        if ($isOnline) {
            $this->last_login_at = now();
        }

        $this->save();

        return $this;
    }

    /**
     * Update FCM token for push notifications
     */
    public function updateFcmToken($token)
    {
        $this->fcm_token = $token;
        $this->save();

        return $this;
    }

    /**
     * Clear FCM token (logout)
     */
    public function clearFcmToken()
    {
        $this->fcm_token = null;
        $this->save();

        return $this;
    }

    /**
     * Get user's activity summary
     */
    public function getActivitySummaryAttribute()
    {
        return [
            'total_logins' => $this->login_count ?? 0,
            'last_login' => $this->formatted_last_login,
            'sales_created' => $this->sales()->count(),
            'shipments_created' => $this->createdShipments()->count(),
            'shipments_assigned' => $this->assignedShipments()->count(),
            'customers_created' => $this->createdCustomers()->count(),
            'attendance_marked' => $this->markedAttendances()->count(),
            'leaves_approved' => $this->approvedLeaves()->count()
        ];
    }

    /**
     * Get user's assigned deliveries (for delivery agents)
     */
    public function getAssignedDeliveriesAttribute()
    {
        if (!$this->isDeliveryAgent()) {
            return collect();
        }

        return $this->assignedShipments()
            ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
            ->orderBy('delivery_order')
            ->get();
    }

    /**
     * Get today's deliveries count
     */
    public function getTodayDeliveriesCountAttribute()
    {
        return $this->assignedShipments()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get user's dashboard stats
     */
    public function getDashboardStatsAttribute()
    {
        $stats = [
            'sales_today' => $this->sales()->whereDate('created_at', today())->count(),
            'shipments_today' => $this->createdShipments()->whereDate('created_at', today())->count(),
        ];

        if ($this->isDeliveryAgent()) {
            $stats['pending_deliveries'] = $this->assignedShipments()
                ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
                ->count();
            $stats['completed_today'] = $this->assignedShipments()
                ->whereDate('actual_delivery_date', today())
                ->where('status', 'delivered')
                ->count();
        }

        return $stats;
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope users by role
     */
    public function scopeOfRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope online users
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true)
                     ->where('last_login_at', '>=', now()->subMinutes(15));
    }

    /**
     * Scope delivery agents
     */
    public function scopeDeliveryAgents($query)
    {
        return $query->where('role', 'delivery_agent');
    }

    /**
     * Scope admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope HR staff
     */
    public function scopeHr($query)
    {
        return $query->where('role', 'hr');
    }

    /**
     * Scope staff
     */
    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    /**
     * Scope users with FCM token
     */
    public function scopeWithFcmToken($query)
    {
        return $query->whereNotNull('fcm_token');
    }

    /**
     * Scope users who logged in recently
     */
    public function scopeLoggedInRecently($query, $minutes = 60)
    {
        return $query->where('last_login_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope users by search term
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('role', 'like', "%{$term}%");
        });
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get online delivery agents
     */
    public static function onlineDeliveryAgents()
    {
        return self::deliveryAgents()
            ->online()
            ->with('deliveryAgent')
            ->get();
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'active' => self::active()->count(),
            'online' => self::online()->count(),
            'admins' => self::admins()->count(),
            'hr' => self::hr()->count(),
            'staff' => self::staff()->count(),
            'delivery_agents' => self::deliveryAgents()->count(),
            'online_agents' => self::deliveryAgents()->online()->count(),
            'with_fcm' => self::withFcmToken()->count()
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->status)) {
                $user->status = 'active';
            }

            if (empty($user->login_count)) {
                $user->login_count = 0;
            }

            if (empty($user->is_online)) {
                $user->is_online = false;
            }
        });

        static::created(function ($user) {
            // If user is delivery agent, create delivery agent profile
            if ($user->role === 'delivery_agent' && !$user->deliveryAgent) {
                // This will be handled by separate process
            }
        });

        static::deleting(function ($user) {
            // Handle related records
            if ($user->deliveryAgent) {
                $user->deliveryAgent->delete();
            }
        });
    }
}
