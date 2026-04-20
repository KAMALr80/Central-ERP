# Role Management System - Documentation

## 📋 Overview

The Role Management system is a complete solution for managing user roles and their associated permissions in the BrainBean ERP system. 

### Features:
- ✅ **Create Roles** - Define new roles with custom names and descriptions
- ✅ **Assign Permissions** - Grant granular permissions to each role using checkboxes
- ✅ **Edit Roles** - Modify existing roles and update their permissions
- ✅ **Delete Roles** - Remove roles (with validation to prevent deletion if in use)
- ✅ **View All Roles** - Display all roles in a beautiful card-based UI
- ✅ **Default Roles** - Pre-configured roles: Admin, HR, Staff, Delivery Agent

---

## 🚀 Setup Instructions

### 1. **Run Migrations**
```bash
php artisan migrate
```

This will create the `roles` table with the following structure:
- `id` - Role ID
- `name` - Role name (e.g., "Admin", "Staff")
- `slug` - URL-friendly identifier (e.g., "admin", "staff")
- `description` - Role description
- `permissions` - JSON array of permission keys
- `timestamps` - Created/Updated dates
- `soft_deletes` - Soft delete support

### 2. **Seed Default Roles** (Optional)
```bash
php artisan db:seed --class=RoleSeeder
```

This will create 4 default roles:
- **Admin** - Full system access
- **HR** - Human Resources management
- **Staff** - Basic staff member access
- **Delivery Agent** - Logistics and delivery management

---

## 🎯 How to Use

### Access Role Management
1. Login as an **Admin** user
2. Go to the **Sidebar** (Left panel)
3. Click on **"Role Management"** (👨‍💼 icon)
4. You'll see the Role Management dashboard

### Create a New Role
1. Click the **"+ Create New Role"** button
2. Fill in the following:
   - **Role Name** (e.g., "Manager", "Support Agent")
   - **Role Slug** (e.g., "manager", "support_agent")
   - **Description** (optional)
3. **Select Permissions** by checking the checkboxes for each permission
4. Click **"Create Role"**

### Edit an Existing Role
1. On the Role Management page, find the role you want to edit
2. Click the **"✏️ Edit"** button on the role card
3. Update the role details and permissions
4. Click **"Update Role"**

### Delete a Role
1. Click the **"🗑️ Delete"** button on the role card
2. Confirm the deletion
3. **Note:** You cannot delete a role if users are assigned to it

---

## 📊 Available Permissions

Permissions are organized into categories:

### Dashboard
- `view_dashboard` - View Dashboard

### Sales
- `view_sales` - View Sales
- `create_sales` - Create Sales
- `edit_sales` - Edit Sales
- `delete_sales` - Delete Sales

### Purchases
- `view_purchases` - View Purchases
- `create_purchases` - Create Purchases
- `edit_purchases` - Edit Purchases
- `delete_purchases` - Delete Purchases

### Inventory
- `view_inventory` - View Inventory
- `edit_inventory` - Edit Inventory

### Employees
- `view_employees` - View Employees
- `create_employees` - Create Employees
- `edit_employees` - Edit Employees
- `delete_employees` - Delete Employees

### Customers
- `view_customers` - View Customers
- `create_customers` - Create Customers
- `edit_customers` - Edit Customers
- `delete_customers` - Delete Customers

### Attendance
- `view_attendance` - View Attendance
- `manage_attendance` - Manage Attendance

### Leaves
- `view_leaves` - View Leaves
- `manage_leaves` - Manage Leaves

### Reports
- `view_reports` - View Reports

### Logistics
- `view_logistics` - View Logistics
- `manage_logistics` - Manage Logistics

### Administration
- `manage_users` - Manage Users
- `manage_roles` - Manage Roles
- `view_approvals` - View Approvals
- `manage_approvals` - Manage Approvals

---

## 🏗️ File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Admin/
│           └── RoleController.php          # Role management logic
├── Models/
│   └── Role.php                            # Role model with permissions
database/
├── migrations/
│   └── 2026_04_18_000000_create_roles_table.php
└── seeders/
    └── RoleSeeder.php                      # Default roles seeder
resources/
└── views/
    └── admin/
        └── roles/
            ├── index.blade.php             # List all roles
            ├── create.blade.php            # Create new role form
            └── edit.blade.php              # Edit role form
routes/
└── web.php                                 # Role routes (admin.roles.*)
```

---

## 🔄 Routes

All Role Management routes are protected and only accessible by Admin users:

```php
GET    /admin/roles              -> admin.roles.index    (List all roles)
GET    /admin/roles/create       -> admin.roles.create   (Show create form)
POST   /admin/roles              -> admin.roles.store    (Store new role)
GET    /admin/roles/{role}/edit  -> admin.roles.edit     (Show edit form)
PUT    /admin/roles/{role}       -> admin.roles.update   (Update role)
DELETE /admin/roles/{role}       -> admin.roles.destroy  (Delete role)
```

---

## 🔐 Security

- **Admin Only Access**: Role management is restricted to users with `role === 'admin'`
- **Middleware Protection**: All routes are protected with middleware
- **Soft Deletes**: Roles are soft-deleted, maintaining data integrity
- **Validation**: All inputs are validated server-side
- **CSRF Protection**: All forms include CSRF tokens

---

## 📝 Default Roles

### Admin
- Full access to all features
- Can manage users, roles, and permissions
- Can view and manage all modules

### HR
- Can manage employees
- Can manage attendance and leaves
- Can view employee reports

### Staff
- Can view and create sales
- Can view and create customer records
- Can view their own attendance and leaves

### Delivery Agent
- Can view and manage logistics
- Can manage deliveries and shipments

---

## 🎨 UI Features

### Role Management Dashboard
- **Card-based Layout** - Beautiful grid display of all roles
- **Quick Stats** - Shows number of permissions per role
- **Action Buttons** - Edit and Delete buttons on each role card
- **Empty State** - Helpful message when no roles exist
- **Responsive Design** - Works on desktop, tablet, and mobile

### Role Edit/Create Forms
- **Organized Permission Groups** - Permissions grouped by category
- **Visual Feedback** - Checkbox styling with hover effects
- **Form Validation** - Real-time validation with error messages
- **Helpful Descriptions** - Clear descriptions for each field

---

## 🚨 Error Handling

- **Role Name Already Exists** - Shows error if trying to create duplicate role name
- **Cannot Delete In-Use Role** - Prevents deletion if users are assigned
- **Invalid Permissions** - Validates permission keys against allowed list
- **Not Authorized** - Returns 403 error if non-admin user tries to access

---

## 📚 Example Usage in Controllers

```php
// Check if user has role
if (auth()->user()->role === 'admin') {
    // Admin-only logic
}

// Get role with permissions
$role = Role::where('slug', 'staff')->first();
$permissions = $role->permissions; // Array of permission keys

// Create role
Role::create([
    'name' => 'Manager',
    'slug' => 'manager',
    'description' => 'Department Manager',
    'permissions' => ['view_sales', 'create_sales', 'edit_sales']
]);
```

---

## 🔄 Future Enhancements

Potential improvements for future versions:
- Role hierarchies (role inheritance)
- Permission templates
- Bulk permission management
- Role assignment directly from this interface
- Permission usage analytics
- Role duplication/cloning
- Export/Import roles

---

## 📧 Support

For issues or questions about the Role Management system, please contact the development team.

---

**Last Updated:** April 18, 2026
**Version:** 1.0.0
