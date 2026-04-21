<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app->make(Kernel::class)->bootstrap();

try {
    echo "Ensuring users and roles tables exist in central database...\n";
    
    // Run users table migrations
    $userMigrations = [
        'database/migrations/tenant/0001_01_01_000000_create_users_table.php',
        'database/migrations/tenant/0001_01_01_000001_create_cache_table.php',
        'database/migrations/tenant/2026_01_06_173926_add_role_to_users_table.php',
        'database/migrations/tenant/2026_01_08_100849_add_status_to_users_table.php',
        'database/migrations/tenant/2026_01_27_230034_add_otp_fields_to_users.php',
        'database/migrations/tenant/2026_02_03_203710_add_deleted_at_to_users_table.php',
        'database/migrations/tenant/2026_04_17_174514_add_two_factor_columns_to_users_table.php',
        'database/migrations/tenant/2026_03_10_163644_add_security_fields_to_users.php',
        'database/migrations/tenant/2026_03_19_163608_update_users_table.php',
        'database/migrations/tenant/2026_03_19_173739_fix_users_table_columns.php',
    ];

    foreach ($userMigrations as $path) {
        echo "Running migration: $path...\n";
        Artisan::call('migrate', [
            '--path' => $path,
            '--force' => true,
        ]);
        echo Artisan::output();
    }
    
    // Run roles table migration
    Artisan::call('migrate', [
        '--path' => 'database/migrations/tenant/2026_04_18_000000_create_roles_table.php',
        '--force' => true,
    ]);

    // Run audit logs table migration (needed for admin dashboard)
    Artisan::call('migrate', [
        '--path' => 'database/migrations/tenant/2026_04_20_201320_create_audit_logs_table.php',
        '--force' => true,
    ]);

    echo "Tables created. Creating Super Admin user...\n";
    
    $adminEmail = 'admin@smarterp.com';
    $user = User::where('email', $adminEmail)->first();
    
    if (!$user) {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => $adminEmail,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        echo "Super Admin created: $adminEmail / admin123\n";
    } else {
        echo "Super Admin already exists.\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
