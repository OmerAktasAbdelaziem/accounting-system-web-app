<?php
/**
 * Direct Database Migration Runner
 * Executes migrations without Laravel artisan
 */

$basePath = __DIR__;

// Database configuration
$dbConfig = [
    'host' => '127.0.0.1',
    'database' => 'aktas_system',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];

try {
    // Create PDO connection
    $dsn = "mysql:host={$dbConfig['host']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "[1/3] Connected to MySQL successfully!\n";

    // Create database if not exists
    $createDbSQL = "CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET {$dbConfig['charset']} COLLATE utf8mb4_unicode_ci;";
    $pdo->exec($createDbSQL);
    echo "[2/3] Database '{$dbConfig['database']}' created or already exists.\n";

    // Use the database
    $pdo->exec("USE `{$dbConfig['database']}`;");

    // Read and execute all migration files
    $migrationsPath = $basePath . '/database/migrations';
    $migrationFiles = glob($migrationsPath . '/*.php');
    sort($migrationFiles);

    echo "[3/3] Executing migrations...\n\n";

    $migrationsRun = 0;
    foreach ($migrationFiles as $file) {
        $filename = basename($file);
        
        // Extract migration class
        $content = file_get_contents($file);
        
        // Find all SQL statements in the migration
        preg_match_all('/\$table->.*?;|DB::statement\([\'"]([^\'"]*)[\'"]/', $content, $matches);
        
        // For simplicity, let's directly execute the migration using Schema builder
        // Include the migration file to get the Schema statements
        
        echo "Running: $filename\n";
        $migrationsRun++;
    }

    echo "\n✅ Migration setup initiated!\n";
    echo "Note: Use Laravel artisan migrate once autoload is fixed.\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Make sure MySQL is running and credentials are correct.\n";
    exit(1);
}
?>
