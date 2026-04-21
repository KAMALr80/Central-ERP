<?php
$host = '127.0.0.1';
$db   = 'laravel_central';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connection to MySQL successful!\n";
     
     $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
     if ($stmt->fetch()) {
         echo "Database '$db' exists.\n";
     } else {
         echo "Database '$db' does NOT exist.\n";
         // Try to create it
         $pdo->exec("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
         echo "Database '$db' created successfully.\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage() . "\n";
}
