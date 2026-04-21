<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|alpha_dash|unique:tenants,id',
            'name' => 'required|string|max:255',
            'domain' => 'required|string',
        ]);

        $currentHost = request()->getHost();
        $isLocal = in_array($currentHost, ['127.0.0.1', 'localhost']);
        $domainSuffix = $isLocal ? '.localhost' : '.' . $currentHost;
        $domainName = $request->domain;
        
        if (!str_contains($domainName, '.')) {
            $domainName .= $domainSuffix;
        }

        // Check uniqueness for the FULL domain
        if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $domainName)->exists()) {
            return back()->withErrors(['domain' => 'This domain is already taken.'])->withInput();
        }

        $tenant = Tenant::create([
            'id' => $request->id,
            'name' => $request->name,
        ]);

        $tenant->domains()->create([
            'domain' => $domainName,
        ]);

        // PHASE 2: Tenant Admin Initialization
        $tenant->run(function () use ($request) {
            // 1. Create Default Roles
            $adminRole = \App\Models\Role::create([
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access',
                'permissions' => array_keys(\App\Models\Role::getAvailablePermissions()), // All permissions
            ]);

            \App\Models\Role::create([
                'name' => 'HR Manager',
                'slug' => 'hr',
                'description' => 'Human Resources and Staff management',
                'permissions' => ['view_dashboard', 'view_employees', 'view_attendance', 'view_leaves'],
            ]);

            \App\Models\Role::create([
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'General business operations',
                'permissions' => ['view_dashboard', 'view_sales', 'view_customers'],
            ]);

            // 2. Create the Super Admin User for this tenant
            \App\Models\User::create([
                'name' => $request->name . ' Admin',
                'email' => 'admin@' . $request->id . '.com',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]);
        });

        return redirect()->route('central.admin.tenants.index')->with('success', 'Company environment launched and Admin account created!');
    }

    public function edit($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant = Tenant::findOrFail($id);
        $tenant->update([
            'name' => $request->name,
        ]);

        return redirect()->route('central.admin.tenants.index')->with('success', 'Company details updated.');
    }

    public function sync($id)
    {
        $tenant = Tenant::findOrFail($id);
        
        // 1. Run migrations for this specific tenant
        \Illuminate\Support\Facades\Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
        ]);

        // 2. Initialize Roles and Admin if they don't exist (Fix for old companies)
        $tenant->run(function () use ($tenant) {
            if (\App\Models\Role::count() === 0) {
                \App\Models\Role::create([
                    'name' => 'Administrator',
                    'slug' => 'admin',
                    'description' => 'Full system access',
                    'permissions' => array_keys(\App\Models\Role::getAvailablePermissions()),
                ]);

                \App\Models\Role::create([
                    'name' => 'HR Manager',
                    'slug' => 'hr',
                    'description' => 'Human Resources and Staff management',
                    'permissions' => ['view_dashboard', 'view_employees', 'view_attendance', 'view_leaves'],
                ]);

                \App\Models\Role::create([
                    'name' => 'Staff',
                    'slug' => 'staff',
                    'description' => 'General business operations',
                    'permissions' => ['view_dashboard', 'view_sales', 'view_customers'],
                ]);
            }

            $adminEmail = 'admin@' . $tenant->id . '.com';
            if (!\App\Models\User::where('email', $adminEmail)->exists()) {
                \App\Models\User::create([
                    'name' => $tenant->name . ' Admin',
                    'email' => $adminEmail,
                    'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                    'role' => 'admin',
                    'status' => 'active',
                ]);
            }
        });

        return redirect()->route('central.admin.tenants.index')->with('success', 'Database synchronized and initialized for ' . $tenant->name);
    }

    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
        return redirect()->route('central.admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
