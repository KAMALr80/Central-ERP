<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\Tenant;

try {
    if ($t = Tenant::find('test')) {
        try { $t->delete(); } catch (\Exception $e) {}
    }
    $tenant = Tenant::create([
        'id' => 'test',
        'name' => 'Test Company',
    ]);

    $tenant->domains()->create([
        'domain' => 'test.localhost',
    ]);

    echo "Tenant created successfully: " . $tenant->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
