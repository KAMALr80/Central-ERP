<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;
use App\Services\AttendanceService;
use App\Services\LeaveService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Force register files if not already registered
        if (!$this->app->bound('files')) {
            $this->app->singleton('files', function ($app) {
                return new Filesystem();
            });
        }

        // ================= REGISTER SERVICES =================
        $this->app->singleton(AttendanceService::class, function ($app) {
            return new AttendanceService();
        });

        $this->app->singleton(LeaveService::class, function ($app) {
            return new LeaveService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production (Render / Cloud hosting)
        if (env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Pass pending counts to sidebar (Only if tenancy is initialized or tables exist)
        view()->composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $pendingStaffCount = 0;
                $pendingAgentCount = 0;
                $pendingHrCount = 0;

                // Only query if we are in a tenant context or if tables exist in central
                if (function_exists('tenancy') && tenancy()->initialized) {
                    $pendingStaffCount = \App\Models\User::where('role', 'staff')
                        ->whereNotIn('status', ['approved', 'rejected'])
                        ->count();
                    $pendingAgentCount = \App\Models\DeliveryAgent::whereNotIn('approval_status', ['approved', 'rejected'])
                        ->count();
                    $pendingHrCount = \App\Models\User::where('role', 'hr')
                        ->whereNotIn('status', ['approved', 'rejected'])
                        ->count();
                }

                $view->with('sidebarPendingStaff', $pendingStaffCount);
                $view->with('sidebarPendingAgent', $pendingAgentCount);
                $view->with('sidebarPendingHr', $pendingHrCount);
            }
        });
    }
}
