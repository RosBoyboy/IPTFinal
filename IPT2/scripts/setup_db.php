<?php
/**
 * Setup script: creates DB if missing, runs migrations, and inserts a test user.
 * Run: php scripts/setup_db.php
 */

function parseEnv($path)
{
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $v = trim($v);
        // strip surrounding quotes
        if ((strlen($v) >= 2) && ($v[0] === '"' && $v[strlen($v)-1] === '"' || $v[0] === "'" && $v[strlen($v)-1] === "'")) {
            $v = substr($v, 1, -1);
        }
        $env[trim($k)] = $v;
    }
    return $env;
}

$root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
$envPath = realpath($root . '.env');
if (!file_exists($envPath)) {
    echo ".env not found at $envPath\n";
    exit(1);
}
$env = parseEnv($envPath);

$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';
$dbName = $env['DB_DATABASE'] ?? 'iptdash';

echo "Using DB {$dbUser}@{$dbHost}:{$dbPort} ({$dbName})\n";

// create database
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '{$dbName}' created or already exists.\n";
} catch (PDOException $e) {
    echo "Failed to create database: " . $e->getMessage() . "\n";
    exit(1);
}

// run migrations
chdir($root);
echo "Running migrations...\n";
passthru('php artisan migrate --force 2>&1', $ret);
if ($ret !== 0) {
    echo "Migrations failed (exit $ret).\n";
    // continue — migrations may have nothing to do
}

// insert test user if not exists
try {
    $dsnDb = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo2 = new PDO($dsnDb, $dbUser, $dbPass);
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo2->prepare('SELECT COUNT(*) FROM users WHERE username = :username OR email = :email');
    $stmt->execute([':username' => 'admin', ':email' => 'admin@example.com']);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        echo "Test user already exists — skipping insertion.\n";
    } else {
        $hash = password_hash('secret123', PASSWORD_BCRYPT);
        $insert = $pdo2->prepare('INSERT INTO users (name, username, email, password, created_at, updated_at) VALUES (:name, :username, :email, :password, NOW(), NOW())');
        $insert->execute([':name' => 'Admin', ':username' => 'admin', ':email' => 'admin@example.com', ':password' => $hash]);
        echo "Test user 'admin' created with password 'secret123'.\n";
    }
} catch (PDOException $e) {
    echo "Failed to create test user: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Setup finished. You can now visit the app and log in with username 'admin' / password 'secret123'.\n";
