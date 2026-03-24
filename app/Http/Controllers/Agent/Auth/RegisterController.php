<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('agent.auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'delivery_agent',
                'status' => 'active',
            ]);

            // Generate agent code
            $lastAgent = DeliveryAgent::orderBy('id', 'desc')->first();
            $lastNumber = $lastAgent ? (int) substr($lastAgent->agent_code, 2) : 0;
            $agentCode = 'AG' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // Create delivery agent with pending approval
            $deliveryAgent = DeliveryAgent::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'agent_code' => $agentCode,
                'status' => 'offline',
                'is_active' => true,
                'approval_status' => 'pending_approval', // ✅ This makes it appear in approval page
            ]);

            DB::commit();

            Log::info('New agent registered', [
                'agent_id' => $deliveryAgent->id,
                'email' => $user->email,
                'name' => $request->name
            ]);

            // ✅ Redirect to LOGIN PAGE with success message
            return redirect()->route('login')
                ->with('success', '✅ Registration successful! Your account is pending admin approval. You will be notified once approved.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent registration failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }
}
