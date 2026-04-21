<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

$app->make(Kernel::class)->bootstrap();

try {
    // 1. Try to create database if it doesn't exist
    $dbName = config('database.connections.central.database');
    $user = config('database.connections.central.username');
    $pass = config('database.connections.central.password');
    $host = config('database.connections.central.host');
    
    echo "Connecting to MySQL at $host...\n";
    
    $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbName` ensured.\n";
    
    // 2. Run central migrations
    echo "Running central migrations...\n";
    $exitCode = Artisan::call('migrate', [
        '--path' => 'database/migrations/central',
        '--force' => true,
    ]);
    
    echo Artisan::output();
    echo "Migration exit code: $exitCode\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
