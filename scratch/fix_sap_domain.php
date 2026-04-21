<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = \App\Models\Tenant::find('sap');
if ($tenant) {
    $domain = $tenant->domains()->first();
    if ($domain) {
        $domain->update(['domain' => 'sap.localhost']);
        echo "Updated sap to sap.localhost\n";
    }
}
