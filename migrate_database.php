<?php
/**
 * Automatic Database Migration (Tables + Full Data) for Railway Deployment
 * Runs on container startup when AUTO_MIGRATE_DATABASE=true.
 * Idempotent: imports database/armful_db_seed.sql only when the database is empty (no `users` table),
 * or when FORCE_MIGRATE_DATABASE=true.
 */

$autoMigrate = strtolower(trim((string)(getenv('AUTO_MIGRATE_DATABASE') ?: 'false')));
$forceMigrate = strtolower(trim((string)(getenv('FORCE_MIGRATE_DATABASE') ?: 'false')));

if (!in_array($autoMigrate, ['true', '1', 'yes'], true)) {
    echo "[Migrate] AUTO_MIGRATE_DATABASE is not enabled. Skipping.\n";
    exit(0);
}

$host = getenv('DB_HOSTNAME') ?: getenv('MYSQLHOST') ?: '127.0.0.1';
$port = (int)(getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: 3306);
$user = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '';
$name = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'railway';

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
$shouldForce = in_array($forceMigrate, ['true', '1', 'yes'], true);

if ($hasUsersTable && !$shouldForce) {
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
