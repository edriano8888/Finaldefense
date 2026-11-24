<?php
/**
 * Automatic Database Migration (Tables + Full Data) for Railway Deployment
 * Runs automatically on container startup via Dockerfile.
 * Idempotent: imports database/armful_db_seed.sql only when the database is empty (no `users` table).
 */

mysqli_report(MYSQLI_REPORT_OFF);

$getEnv = function(string $key): string {
    $val = getenv($key);
    if ($val !== false && $val !== '') return trim((string)$val, " \t\n\r\0\x0B\"'");
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return trim((string)$_ENV[$key], " \t\n\r\0\x0B\"'");
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return trim((string)$_SERVER[$key], " \t\n\r\0\x0B\"'");
    return '';
};

$host = $getEnv('DB_HOSTNAME') ?: $getEnv('MYSQLHOST') ?: '127.0.0.1';
$port = (int)($getEnv('DB_PORT') ?: $getEnv('MYSQLPORT') ?: 3306);
$user = $getEnv('DB_USERNAME') ?: $getEnv('MYSQLUSER') ?: 'root';
$pass = $getEnv('DB_PASSWORD') ?: $getEnv('MYSQLPASSWORD') ?: '';
$name = $getEnv('DB_NAME') ?: $getEnv('MYSQLDATABASE') ?: 'railway';

$dbUrl = $getEnv('MYSQL_URL') ?: $getEnv('DATABASE_URL');
if ($dbUrl !== '' && ($host === '127.0.0.1' || strpos($host, '${{') !== false)) {
    $p = parse_url($dbUrl);
    if (!empty($p['host'])) $host = $p['host'];
    if (!empty($p['port'])) $port = (int)$p['port'];
    if (!empty($p['user'])) $user = $p['user'];
    if (isset($p['pass']))  $pass = $p['pass'];
    if (!empty($p['path'])) $name = ltrim($p['path'], '/');
}

echo "[Migrate] Connecting to MySQL at {$host}:{$port} (db: {$name})...\n";

$mysqli = null;
for ($attempt = 1; $attempt <= 10; $attempt++) {
    $mysqli = @new mysqli($host, $user, $pass, $name, $port);
    if ($mysqli && !$mysqli->connect_errno) {
        break;
    }
    echo "[Migrate] Attempt {$attempt}/10 failed: " . ($mysqli ? $mysqli->connect_error : 'unknown') . ". Retrying in 2s...\n";
    sleep(2);
}

if (!$mysqli || $mysqli->connect_errno) {
    fwrite(STDERR, "[Migrate] Could not connect to MySQL after 10 attempts. Aborting migration.\n");
    exit(1);
}

$mysqli->set_charset('utf8mb4');

$tableCheck = $mysqli->query("SHOW TABLES LIKE 'users'");
$hasUsersTable = ($tableCheck && $tableCheck->num_rows > 0);

if ($hasUsersTable) {
    echo "[Migrate] Database tables already exist. Skipping full SQL import.\n";
    $mysqli->close();
    exit(0);
}

$sqlPath = __DIR__ . '/database/armful_db_seed.sql';
if (!file_exists($sqlPath)) {
    $sqlPath = __DIR__ . '/armful_db_schema.sql';
}

if (!file_exists($sqlPath)) {
    fwrite(STDERR, "[Migrate] No SQL migration file found.\n");
    $mysqli->close();
    exit(1);
}

echo "[Migrate] Importing tables and data from " . basename($sqlPath) . "...\n";
$sql = file_get_contents($sqlPath);

$mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
$mysqli->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

if ($mysqli->multi_query($sql)) {
    $count = 0;
    do {
        $count++;
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
    echo "[Migrate] Executed {$count} SQL statements.\n";
}

if ($mysqli->errno) {
    fwrite(STDERR, "[Migrate] SQL warning/error: " . $mysqli->error . "\n");
}

$mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
$mysqli->close();
echo "[Migrate] Database migration finished successfully!\n";
