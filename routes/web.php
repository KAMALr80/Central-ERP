<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TenantController;

/*
|--------------------------------------------------------------------------
| Central Web Routes
|--------------------------------------------------------------------------
*/

$centralDomains = config('tenancy.central_domains', []);
$currentHost = request()->getHost();

if (in_array($currentHost, $centralDomains)) {
    Route::middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CentralDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/sync', [TenantController::class, 'sync'])->name('tenants.sync');
        
        Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/export', [App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::get('audit-logs/{id}', [App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');
        });
    });

    require __DIR__ . '/auth.php';
}

Route::get('/debug-host', function () {
    return [
        'host' => request()->getHost(),
        'http_host' => request()->getHttpHost(),
        'central_domains' => config('tenancy.central_domains'),
        'is_central' => in_array(request()->getHost(), config('tenancy.central_domains')),
    ];
});

Route::get('/test-central', function () {
    return "Central domain is working!";
});

Route::get('/setup-admin', function () {
    $admin = \App\Models\User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'Super Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true
        ]
    );
    return "Admin Created! Email: admin@admin.com | Password: admin123 <br> <a href='/login'>Go to Login</a>";
});
