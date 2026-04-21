<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Carbon\Carbon;

use App\Helpers\GeoHelper;
use App\Models\User;
use App\Services\TwoFactorService;

class AuthenticatedSessionController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show login page
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request - Simple email/password authentication
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        /* ================= STEP 1: Find user ================= */
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->handleFailedLogin($request);
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        /* ================= STEP 2: Check account status ================= */

        // Staff approval check
        if ($user->role === 'staff' && $user->status !== 'approved') {
            return back()->withErrors([
                'email' => 'Your account is pending approval. Please wait for admin verification.',
            ])->withInput($request->only('email'));
        }

        // Delivery Agent approval check
        if ($user->role === 'delivery_agent') {
            $agent = DeliveryAgent::where('user_id', $user->id)->first();
            if ($agent && $agent->approval_status !== 'approved') {
                return back()->withErrors([
                    'email' => 'Your agent account is pending admin approval. You will be notified once approved.',
                ])->withInput($request->only('email'));
            }
        }

        /* ================= STEP 3: Check if account is locked ================= */
        if ($this->isAccountLocked($user)) {
            $unlockTime = Carbon::parse($user->locked_until)->diffForHumans();
            return back()->withErrors([
                'email' => "Account temporarily locked. Try again {$unlockTime}.",
            ])->withInput($request->only('email'));
        }

        /* ================= STEP 4: Reset login attempts on successful password ================= */
        $user->login_attempts = 0;
        $user->save();

        // Check if 2FA is enabled
        if ($user->two_factor_enabled) {
            return $this->requireTwoFactorVerification($user, $request);
        }

        // Direct login without 2FA
        return $this->completeLogin($user, $request);
    }

    /**
     * Require 2FA verification before completing login
     */
    private function requireTwoFactorVerification(User $user, Request $request): RedirectResponse
    {
        // Store user ID and remember preference in session for 2FA verification
        Session::put('2fa:user:id', $user->id);
        Session::put('2fa:remember', $request->boolean('remember'));
        Session::put('2fa:login:attempt', now()->timestamp);

        Log::info('2FA verification required', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        // Redirect to 2FA verification page
        return redirect()->route('2fa.verify');
    }

    /**
     * Complete login after successful 2FA verification (called from TwoFactorController)
     */
    public function completeLoginAfter2FA(User $user, Request $request, $remember = false): RedirectResponse
    {
        return $this->completeLogin($user, $request, $remember);
    }

    /**
     * Complete the login process (actual authentication)
     */
    private function completeLogin(User $user, Request $request, $remember = null): RedirectResponse
    {
        // Use passed remember value or get from request
        $rememberFlag = $remember ?? $request->boolean('remember');

        // Update login tracking
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->login_attempts = 0;
        $user->save();

        // Login the user
        Auth::login($user, $rememberFlag);
        $request->session()->regenerate();

        Log::info('Login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => $request->ip(),
            'two_factor_used' => Session::has('2fa:verified') ? true : false
        ]);

        // Clear 2FA session data if present
        Session::forget('2fa:user:id');
        Session::forget('2fa:remember');
        Session::forget('2fa:login:attempt');
        Session::forget('2fa:verified');

        // Check password age (optional)
        if ($user->password_updated_at && $user->password_updated_at->diffInDays(now()) > 90) {
            return redirect()->route('password.request')
                ->with('warning', 'Your password is over 90 days old. Please update for security.');
        }

        // ==================== ROLE-BASED REDIRECT ====================
        $prefix = tenant() ? '' : 'central.';

        // Admin Dashboard
        if ($user->role === 'admin') {
            return redirect()->route($prefix . 'dashboard')
                ->with('success', "Welcome back, Admin {$user->name}!");
        }

        // Delivery Agent Dashboard
        if ($user->role === 'delivery_agent') {
            // Check if agent profile exists
            $agent = DeliveryAgent::where('user_id', $user->id)->first();

            if (!$agent) {
                Log::warning('Delivery agent login but no agent profile found', ['user_id' => $user->id]);
                return redirect()->route($prefix . 'login')->withErrors([
                    'email' => 'Agent profile not found. Please contact admin.'
                ]);
            }

            // Update agent online status
            $agent->is_online = true;
            $agent->last_active_at = now();
            $agent->save();

            return redirect()->route($prefix . 'agent.dashboard')
                ->with('success', "Welcome back, {$agent->name}!");
        }

        // HR Dashboard
        if ($user->role === 'hr') {
            return redirect()->route($prefix . 'hr.dashboard')
                ->with('success', "Welcome back, HR {$user->name}!");
        }

        // Staff Dashboard
        if ($user->role === 'staff') {
            return redirect()->route($prefix . 'staff.dashboard')
                ->with('success', "Welcome back, {$user->name}!");
        }

        // Default fallback
        return redirect()->intended(route($prefix . 'dashboard'))
            ->with('success', "Welcome back, {$user->name}!");
    }

    /**
     * Handle failed login attempt
     */
    private function handleFailedLogin(Request $request): void
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Increment login attempts using your column
            $user->login_attempts = ($user->login_attempts ?? 0) + 1;

            // Lock account after 5 failed attempts
            if ($user->login_attempts >= 5) {
                $user->locked_until = now()->addMinutes(15);

                Log::warning('Account locked due to failed logins', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip()
                ]);
            }

            $user->save();
        }

        Log::info('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    /**
     * Check if account is locked
     */
    private function isAccountLocked(User $user): bool
    {
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            return true;
        }

        // Reset lock if expired
        if ($user->locked_until && now()->greaterThan($user->locked_until)) {
            $user->locked_until = null;
            $user->login_attempts = 0;
            $user->save();
        }

        return false;
    }

    /**
     * Logout user
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // Update agent online status if user is a delivery agent
            if ($user->role === 'delivery_agent') {
                $agent = DeliveryAgent::where('user_id', $user->id)->first();
                if ($agent) {
                    $agent->is_online = false;
                    $agent->save();
                }
            }

            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirectUrl = tenant() ? url('/' . tenant('id') . '/login') : url('/login');
        return redirect($redirectUrl)->with('status', 'You have been successfully logged out.');
    }
}
