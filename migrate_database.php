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

if (!$hasUsersTable) {
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
} else {
    echo "[Migrate] Database tables already exist. Checking incremental schema patches...\n";
}

// Incremental idempotent schema patches (safe to run on every startup)
$addColumnIfMissing = function(mysqli $db, string $table, string $column, string $definition) {
    $res = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    if ($res && $res->num_rows === 0) {
        if ($db->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}")) {
            echo "[Migrate] Added missing column {$table}.{$column}\n";
        } else {
            fwrite(STDERR, "[Migrate] Failed adding {$table}.{$column}: " . $db->error . "\n");
        }
    }
};

$addColumnIfMissing($mysqli, 'collaborations', 'date_reviewed', 'DATETIME DEFAULT NULL');
$addColumnIfMissing($mysqli, 'influencer_profiles', 'first_name', 'VARCHAR(100) DEFAULT NULL');
$addColumnIfMissing($mysqli, 'influencer_profiles', 'last_name', 'VARCHAR(100) DEFAULT NULL');
$addColumnIfMissing($mysqli, 'influencer_profiles', 'contact_info', 'VARCHAR(255) DEFAULT NULL');
$addColumnIfMissing($mysqli, 'influencer_profiles', 'gender', 'VARCHAR(50) DEFAULT NULL');
$addColumnIfMissing($mysqli, 'influencer_profiles', 'niche', 'VARCHAR(100) DEFAULT NULL');

$mysqli->query("CREATE TABLE IF NOT EXISTS `influencer_custom_columns` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `brand_id` INT(11) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `field_type` VARCHAR(50) NOT NULL DEFAULT 'text',
    `section` VARCHAR(50) NOT NULL DEFAULT 'influencer_details',
    `sort_order` INT(11) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

$mysqli->query("CREATE TABLE IF NOT EXISTS `influencer_custom_values` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `collaboration_id` INT(11) NOT NULL,
    `column_id` INT(11) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_collab_col` (`collaboration_id`, `column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

$mysqli->close();
echo "[Migrate] Database migration finished successfully!\n";
