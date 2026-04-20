<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define default roles with their permissions
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access to the system',
                'permissions' => [
                    'view_dashboard',
                    'view_sales', 'create_sales', 'edit_sales', 'delete_sales',
                    'view_purchases', 'create_purchases', 'edit_purchases', 'delete_purchases',
                    'view_inventory', 'edit_inventory',
                    'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
                    'view_customers', 'create_customers', 'edit_customers', 'delete_customers',
                    'view_attendance', 'manage_attendance',
                    'view_leaves', 'manage_leaves',
                    'view_reports',
                    'view_logistics', 'manage_logistics',
                    'manage_users', 'manage_roles', 'view_approvals', 'manage_approvals',
                ],
            ],
            [
                'name' => 'HR',
                'slug' => 'hr',
                'description' => 'Human Resources Manager - Can manage employees, attendance, and leaves',
                'permissions' => [
                    'view_dashboard',
                    'view_employees', 'create_employees', 'edit_employees',
                    'view_attendance', 'manage_attendance',
                    'view_leaves', 'manage_leaves',
                    'view_reports',
                ],
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff Member - Can view sales, customers, and manage their own attendance',
                'permissions' => [
                    'view_dashboard',
                    'view_sales', 'create_sales',
                    'view_customers', 'create_customers',
                    'view_attendance',
                    'view_leaves',
                ],
            ],
            [
                'name' => 'Delivery Agent',
                'slug' => 'delivery_agent',
                'description' => 'Delivery Agent - Can manage deliveries and track shipments',
                'permissions' => [
                    'view_dashboard',
                    'view_logistics', 'manage_logistics',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}
