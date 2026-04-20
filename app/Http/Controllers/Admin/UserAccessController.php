<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user is admin or currently impersonating
     */
    private function checkAccess()
    {
        if (Auth::user()->role !== 'admin' && !session()->has('impersonator_id')) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display login details of all users
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $roleFilter = $request->get('role');
        
        $query = User::query()->latest();

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        $users = $query->paginate(20);
        $roles = Role::all();

        return view('admin.users.access_details', compact('users', 'roles', 'roleFilter'));
    }

    /**
     * Show full detail of a user
     */
    public function show($id)
    {
        $this->checkAccess();

        $user = User::with(['employee', 'deliveryAgent'])->findOrFail($id);
        
        return view('admin.users.show_access', compact('user'));
    }

    /**
     * Login as a different user (Impersonation)
     */
    public function impersonate($id)
    {
        // Only a real admin can start impersonating
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can impersonate users.');
        }

        $originalId = Auth::id();
        $userToImpersonate = User::findOrFail($id);

        if ($originalId == $userToImpersonate->id) {
            return redirect()->back()->with('error', 'You are already logged in as this user.');
        }

        // Store original admin ID in session
        session(['impersonator_id' => $originalId]);

        // Login as the new user
        Auth::login($userToImpersonate);

        return redirect()->route('dashboard')->with('success', "Logged in as {$userToImpersonate->name}");
    }

    /**
     * Return to original admin account
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $originalId = session('impersonator_id');
        $originalUser = User::findOrFail($originalId);

        // Clear impersonation session
        session()->forget('impersonator_id');

        // Login back as admin
        Auth::login($originalUser);

        return redirect()->route('admin.users.access')->with('success', 'Returned to Admin account');
    }
}
