<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

$app->make(Kernel::class)->bootstrap();

$tenantId = 'test';
$domain = 'test.localhost';

try {
    echo "Creating tenant: $tenantId...\n";
    
    // 1. Delete if exists (for fresh start)
    if ($t = Tenant::find($tenantId)) {
        echo "Old tenant found. Deleting...\n";
        $t->delete();
    }
    
    // 2. Create Tenant
    $tenant = Tenant::create([
        'id' => $tenantId,
        'name' => 'Test Company',
    ]);
    
    // 3. Create Domain
    $tenant->domains()->create([
        'domain' => $domain,
    ]);
    
    echo "Tenant and Domain created successfully.\n";
    echo "Running migrations and seeders for tenant $tenantId...\n";
    
    // 4. Seed the tenant (to create the tenant's admin user)
    // Note: Migrations are handled automatically by the TenantCreated event
    Artisan::call('tenants:seed', [
        '--tenants' => [$tenantId],
        '--class' => 'Database\Seeders\AdminSeeder',
    ]);
    
    echo Artisan::output();
    echo "Tenant setup complete!\n";
    echo "You can now visit: http://$domain\n";
    echo "Login: admin@smarterp.com / admin123\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
