<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
*/

if (!in_array(request()->getHost(), config('tenancy.central_domains', []))) {
    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        // PreventAccessFromCentralDomains::class,
    ])->group(function () {
        // Include the original ERP routes
        require __DIR__ . '/erp.php';
    });
}
