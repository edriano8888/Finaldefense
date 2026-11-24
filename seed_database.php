<?php
/**
 * Automatic Database Seeder for Railway Deployment
 * Runs automatically on container startup via Dockerfile.
 * Idempotent: safe to run on every redeploy without duplicating accounts.
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

echo "[Seeder] Connecting to MySQL at {$host}:{$port} (db: {$name})...\n";

$mysqli = null;
for ($attempt = 1; $attempt <= 10; $attempt++) {
    $mysqli = @new mysqli($host, $user, $pass, $name, $port);
    if ($mysqli && !$mysqli->connect_errno) {
        break;
    }
    echo "[Seeder] Attempt {$attempt}/10 failed: " . ($mysqli ? $mysqli->connect_error : 'unknown') . ". Retrying in 2s...\n";
    sleep(2);
}

if (!$mysqli || $mysqli->connect_errno) {
    fwrite(STDERR, "[Seeder] Could not connect to MySQL after 10 attempts. Aborting seeder.\n");
    exit(1);
}

$mysqli->set_charset('utf8mb4');

// 1. Import schema if `users` table does not exist yet
$tableCheck = $mysqli->query("SHOW TABLES LIKE 'users'");
if (!$tableCheck || $tableCheck->num_rows === 0) {
    $schemaPath = __DIR__ . '/armful_db_schema.sql';
    if (!file_exists($schemaPath)) {
        fwrite(STDERR, "[Seeder] Schema file not found at {$schemaPath}\n");
        exit(1);
    }
    echo "[Seeder] Fresh database detected. Importing armful_db_schema.sql...\n";
    $sql = file_get_contents($schemaPath);
    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
    if ($mysqli->multi_query($sql)) {
        do {
            if ($res = $mysqli->store_result()) {
                $res->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());
    }
    if ($mysqli->errno) {
        fwrite(STDERR, "[Seeder] Schema import warning/error: " . $mysqli->error . "\n");
    }
    $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
    echo "[Seeder] Schema import completed.\n";
} else {
    echo "[Seeder] Database tables already exist. Skipping schema import.\n";
}

// Helper to upsert a user by email (only inserts if not already present)
function ensureUser(mysqli $db, string $name, string $email, string $plainPassword, string $role, string $platformRole): int {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $stmt->close();
        echo "[Seeder] User already exists: {$email} (ID: {$row['id']})\n";
        return (int)$row['id'];
    }
    $stmt->close();

    $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
    $status = 'active';
    $ins = $db->prepare("INSERT INTO users (name, email, password, role, platform_role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $ins->bind_param('ssssss', $name, $email, $hash, $role, $platformRole, $status);
    $ins->execute();
    $newId = (int)$ins->insert_id;
    $ins->close();
    echo "[Seeder] Created {$role} account: {$email} (ID: {$newId})\n";
    return $newId;
}

// 2. Seed Default Admin, Manager, and Employee Accounts directly in seeder (idempotent)
$adminId    = ensureUser($mysqli, 'Instroom Admin', 'admin@armfulmedia.com', 'admin123', 'admin', 'superadmin');
$managerId  = ensureUser($mysqli, 'Manager', 'manager@armfulmedia.com', 'manager123', 'manager', 'user');
$employeeId = ensureUser($mysqli, 'Employee', 'employee@armfulmedia.com', 'employee123', 'employee', 'user');

// 3. Ensure at least one Default Brand exists and link seeded users
$brandCheck = $mysqli->query("SELECT id FROM brands ORDER BY id ASC LIMIT 1");
$brandId = 0;
if ($brandCheck && $brandRow = $brandCheck->fetch_assoc()) {
    $brandId = (int)$brandRow['id'];
    echo "[Seeder] Default brand already exists (ID: {$brandId}).\n";
} else {
    $brandName = 'JBL';
    $industry = 'Consumer Electronics';
    $ownerId = $adminId > 0 ? $adminId : 1;
    $bStmt = $mysqli->prepare("INSERT INTO brands (name, industry, owner_id) VALUES (?, ?, ?)");
    $bStmt->bind_param('ssi', $brandName, $industry, $ownerId);
    $bStmt->execute();
    $brandId = (int)$bStmt->insert_id;
    $bStmt->close();
    echo "[Seeder] Created default brand: {$brandName} (ID: {$brandId})\n";
}

// 5. Link Admin, Manager, and Employee to the Default Brand in `brand_members`
if ($brandId > 0) {
    $members = [
        [$adminId, 'owner'],
        [$managerId, 'manager'],
        [$employeeId, 'collaborator'],
    ];
    foreach ($members as [$uid, $bRole]) {
        if ($uid <= 0) continue;
        $mStmt = $mysqli->prepare("INSERT IGNORE INTO brand_members (brand_id, user_id, role) VALUES (?, ?, ?)");
        $mStmt->bind_param('iis', $brandId, $uid, $bRole);
        $mStmt->execute();
        $mStmt->close();
    }
    echo "[Seeder] Linked seeded users to brand ID {$brandId}.\n";
}

$mysqli->close();
echo "[Seeder] Database seeding finished successfully!\n";
