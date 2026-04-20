<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Get all available permissions
    public static function getAvailablePermissions()
    {
        return [
            // Dashboard
            'view_dashboard' => 'View Dashboard',

            // Sales
            'view_sales' => 'View Sales',
            'create_sales' => 'Create Sales',
            'edit_sales' => 'Edit Sales',
            'delete_sales' => 'Delete Sales',
            'export_sales' => 'Export Sales',
            'view_invoices' => 'View Invoices',

            // Purchases
            'view_purchases' => 'View Purchases',
            'create_purchases' => 'Create Purchases',
            'edit_purchases' => 'Edit Purchases',
            'delete_purchases' => 'Delete Purchases',

            // Inventory
            'view_inventory' => 'View Inventory',
            'create_inventory' => 'Create Inventory',
            'edit_inventory' => 'Edit Inventory',
            'delete_inventory' => 'Delete Inventory',
            'view_stock_alerts' => 'View Stock Alerts',
            'generate_barcodes' => 'Generate Barcodes',

            // Employees
            'view_employees' => 'View Employees',
            'create_employees' => 'Create Employees',
            'edit_employees' => 'Edit Employees',
            'delete_employees' => 'Delete Employees',
            'view_employee_details' => 'View Employee Details',

            // Customers
            'view_customers' => 'View Customers',
            'create_customers' => 'Create Customers',
            'edit_customers' => 'Edit Customers',
            'delete_customers' => 'Delete Customers',
            'manage_customer_wallet' => 'Manage Customer Wallet',

            // Attendance
            'view_attendance' => 'View Attendance',
            'mark_attendance' => 'Mark Attendance',
            'edit_attendance' => 'Edit Attendance',
            'view_attendance_reports' => 'View Attendance Reports',
            'export_attendance' => 'Export Attendance',

            // Leaves
            'view_leaves' => 'View Leaves',
            'apply_leaves' => 'Apply Leaves',
            'edit_leaves' => 'Edit/Approve Leaves',
            'manage_leave_policies' => 'Manage Leave Policies',

            // Logistics & Shipments
            'view_shipments' => 'View Shipments',
            'create_shipments' => 'Create Shipments',
            'edit_shipments' => 'Edit/Update Status',
            'delete_shipments' => 'Delete Shipments',
            'track_shipments' => 'Track Shipments',
            'manage_logistics_agents' => 'Manage Logistics Agents',
            'manage_service_areas' => 'Manage Service Areas',
            'view_live_tracking' => 'View Live Tracking',

            // Reports
            'view_reports' => 'View Reports Dashboard',
            'view_sales_reports' => 'View Sales Reports',
            'view_customers_reports' => 'View Customers Reports',
            'view_inventory_reports' => 'View Inventory Reports',
            'view_logistics_reports' => 'View Logistics Reports',
            'view_employee_reports' => 'View Employee Reports',
            'view_purchase_reports' => 'View Purchase Reports',
            'view_attendance_reports' => 'View Attendance Reports',

            // EMI & Payments
            'view_emi' => 'View EMI Plans',
            'manage_emi' => 'Manage EMI',
            'view_payments' => 'View Payments',
            'manage_payments' => 'Manage Payments',

            // System Management
            'manage_users' => 'Manage Users',
            'manage_roles' => 'Manage Roles',
            'manage_settings' => 'Manage App Settings',
            'view_approvals' => 'View Approvals',
            'manage_approvals' => 'Manage Approvals',
        ];
    }

    // Scope: Get role by slug
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Get permissions array with labels
    public function getPermissionsWithLabels()
    {
        $available = self::getAvailablePermissions();
        return array_intersect_key($available, array_flip($this->permissions ?? []));
    }
}
