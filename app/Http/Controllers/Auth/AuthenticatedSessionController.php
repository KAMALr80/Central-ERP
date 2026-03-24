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

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login page
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request with location-based OTP and role-based redirect
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
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

        /* ================= STEP 5: CHECK IF USER IS ADMIN - NO OTP REQUIRED ================= */
        if ($user->role === 'admin') {
            Log::info('Admin login - bypassing OTP', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return $this->directLogin($user, $request);
        }

        /* ================= STEP 6: Process location data for non-admin users ================= */
        $userLat = $request->lat;
        $userLng = $request->lng;

        $officeLat = env('OFFICE_LAT', '22.524768');
        $officeLng = env('OFFICE_LNG', '72.955568');
        $allowedRadius = (float) env('ALLOWED_RADIUS_KM', 1);

        /* ================= STEP 7: Location-based decision for non-admin users ================= */

        // CASE A: Location OFF/DENIED → EMAIL OTP REQUIRED
        if (!$userLat || !$userLng) {
            return $this->sendLoginOtp(
                $user,
                '📍 Location access required. OTP sent to your email for security verification.'
            );
        }

        // Calculate distance from office
        $distance = GeoHelper::distanceKm(
            (float) $officeLat,
            (float) $officeLng,
            (float) $userLat,
            (float) $userLng
        );

        // Log location for audit
        $this->logLoginLocation($user, $userLat, $userLng, $distance);

        // CASE B: Outside office radius → EMAIL OTP REQUIRED
        if ($distance > $allowedRadius) {
            return $this->sendLoginOtp(
                $user,
                "📍 You are " . number_format($distance, 2) . "km from office (max allowed: {$allowedRadius}km). OTP sent for verification."
            );
        }

        // CASE C: Inside office radius → DIRECT LOGIN
        return $this->directLogin($user, $request);
    }

    /**
     * Send login OTP via email
     */
    private function sendLoginOtp(User $user, string $message): RedirectResponse
    {
        // Check cooldown (60 seconds)
        if ($user->otp_last_sent_at && !$this->canResendOtp($user)) {
            $waitTime = $this->getResendWaitSeconds($user);
            return back()->withErrors([
                'email' => "Please wait {$waitTime} seconds before requesting another OTP.",
            ]);
        }

        // Generate secure 6-digit OTP
        $otp = sprintf("%06d", random_int(0, 999999));

        // Save OTP using your existing columns
        $user->login_otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->otp_last_sent_at = now();
        $user->otp_attempts = 0; // Reset OTP attempts
        $user->save();

        // Store in session for verification
        Session::put('otp_user_id', $user->id);
        Session::put('otp_purpose', 'login');
        Session::put('otp_email', $user->email);
        Session::put('otp_sent_at', now()->timestamp);
        Session::put('otp_expires_at', now()->addMinutes(5)->timestamp);

        // Send OTP via email
        $this->sendOtpEmail($user, $otp, 'Login');

        Log::info('Login OTP sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => request()->ip()
        ]);

        return redirect()->route('otp.verify')->with('status', $message);
    }

    /**
     * Direct login without OTP - WITH ROLE-BASED REDIRECT
     */
    private function directLogin(User $user, Request $request): RedirectResponse
    {
        // Update login tracking using your existing columns
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->login_attempts = 0;

        // Clear any OTP data
        $user->login_otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Login the user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Clear OTP session
        Session::forget(['otp_user_id', 'otp_purpose', 'otp_sent_at', 'otp_email', 'otp_expires_at']);

        Log::info('Direct login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => $request->ip()
        ]);

        // Check password age (optional)
        if ($user->password_updated_at && $user->password_updated_at->diffInDays(now()) > 90) {
            return redirect()->route('password.request')
                ->with('warning', 'Your password is over 90 days old. Please update for security.');
        }

        // ==================== ROLE-BASED REDIRECT ====================

        // Admin Dashboard
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')
                ->with('success', "Welcome back, Admin {$user->name}!");
        }

        // Delivery Agent Dashboard
        if ($user->role === 'delivery_agent') {
            // Check if agent profile exists
            $agent = DeliveryAgent::where('user_id', $user->id)->first();

            if (!$agent) {
                Log::warning('Delivery agent login but no agent profile found', ['user_id' => $user->id]);
                return redirect()->route('login')->withErrors([
                    'email' => 'Agent profile not found. Please contact admin.'
                ]);
            }

            // Update agent online status
            $agent->is_online = true;
            $agent->last_active_at = now();
            $agent->save();

            return redirect()->route('agent.dashboard')
                ->with('success', "Welcome back, {$agent->name}!");
        }

        // HR Dashboard
        if ($user->role === 'hr') {
            return redirect()->route('hr.dashboard')
                ->with('success', "Welcome back, HR {$user->name}!");
        }

        // Staff Dashboard
        if ($user->role === 'staff') {
            return redirect()->route('staff.dashboard')
                ->with('success', "Welcome back, {$user->name}!");
        }

        // Default fallback
        return redirect()->intended(route('dashboard'))
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
     * Check if user can resend OTP (60-second cooldown)
     */
    private function canResendOtp(User $user): bool
    {
        if (!$user->otp_last_sent_at) {
            return true;
        }

        return Carbon::parse($user->otp_last_sent_at)->diffInSeconds(now()) >= 60;
    }

    /**
     * Get remaining seconds for resend cooldown
     */
    private function getResendWaitSeconds(User $user): int
    {
        if (!$user->otp_last_sent_at) {
            return 0;
        }

        $elapsed = Carbon::parse($user->otp_last_sent_at)->diffInSeconds(now());
        return max(0, 60 - $elapsed);
    }

    /**
     * Send OTP email
     */
    private function sendOtpEmail(User $user, string $otp, string $purpose): void
    {
        try {
            Mail::send('emails.otp', [
                'otp' => $otp,
                'purpose' => $purpose,
                'name' => $user->name,
                'email' => $user->email,
                'year' => date('Y')
            ], function ($message) use ($user, $purpose) {
                $message->to($user->email, $user->name)
                        ->subject("🔐 ERP {$purpose} OTP Verification");
            });
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'user' => $user->email,
                'error' => $e->getMessage()
            ]);

            // Fallback to raw email
            $this->sendRawOtpEmail($user, $otp, $purpose);
        }
    }

    /**
     * Send raw OTP email as fallback
     */
    private function sendRawOtpEmail(User $user, string $otp, string $purpose): void
    {
        try {
            Mail::raw(
                "Hello {$user->name},\n\n" .
                "Your ERP {$purpose} OTP is: {$otp}\n\n" .
                "This OTP is valid for 5 minutes.\n" .
                "If you did not request this, please ignore this email.\n\n" .
                "© " . date('Y') . " ERP System",
                function ($message) use ($user, $purpose) {
                    $message->to($user->email)
                            ->subject("ERP {$purpose} OTP");
                }
            );
        } catch (\Exception $e) {
            Log::error('Fallback email failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log login location
     */
    private function logLoginLocation(User $user, float $lat, float $lng, float $distance): void
    {
        Log::info('Login location', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'latitude' => $lat,
            'longitude' => $lng,
            'distance_from_office' => number_format($distance, 2) . 'km',
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
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

        return redirect('/')->with('status', 'You have been successfully logged out.');
    }
}
