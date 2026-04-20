<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Check if user is admin
    public function __construct()
    {
        $this->middleware('auth');
        // Only allow admin to manage roles
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Display list of all roles
     */
    public function index()
    {
        $roles = Role::whereNotIn('slug', ['admin', 'staff', 'hr', 'delivery_agent'])->paginate(10);
        $availablePermissions = Role::getAvailablePermissions();

        // Fetch default roles for assignment display
        $defaultRoles = [
            'admin' => Role::where('slug', 'admin')->first(),
            'staff' => Role::where('slug', 'staff')->first(),
            'hr' => Role::where('slug', 'hr')->first(),
            'delivery_agent' => Role::where('slug', 'delivery_agent')->first(),
        ];

        return view('admin.roles.index', [
            'roles' => $roles,
            'availablePermissions' => $availablePermissions,
            'defaultRoles' => $defaultRoles,
        ]);
    }

    /**
     * Show edit form for a role
     */
    public function edit(Role $role)
    {
        $availablePermissions = Role::getAvailablePermissions();
        $rolePermissions = $role->permissions ?? [];

        return view('admin.roles.edit', [
            'role' => $role,
            'availablePermissions' => $availablePermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update role with selected permissions
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::getAvailablePermissions())),
        ]);

        // Get selected permissions from request
        $permissions = $request->input('permissions', []);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully!");
    }

    /**
     * Delete a role
     */
    public function destroy(Role $role)
    {
        // Prevent deleting if role is assigned to any user
        $userCount = \DB::table('users')->where('role', $role->slug)->count();

        if ($userCount > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete role. {$userCount} user(s) are assigned to this role.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully!");
    }

    /**
     * Create a new role
     */
    public function create()
    {
        $availablePermissions = Role::getAvailablePermissions();

        return view('admin.roles.create', [
            'availablePermissions' => $availablePermissions,
        ]);
    }

    /**
     * Store a new role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::getAvailablePermissions())),
        ]);

        $permissions = $request->input('permissions', []);

        Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$validated['name']}' created successfully!");
    }
}
