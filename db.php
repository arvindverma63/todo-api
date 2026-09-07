<?php
$host = '193.203.184.228';
$db   = 'u793412290_todo';
$user = 'u793412290_todo';
$pass = 'Todo@102030';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 5,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Helper to execute table creation
function createTable($pdo, $sql) {
    try {
        $pdo->exec($sql);
    } catch (\PDOException $e) {
        // Table creation handled gracefully
    }
}

// Helper to add column if missing
function addColumnIfNeeded($pdo, $tableName, $columnName, $columnDefinition) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
        $exists = $stmt->fetch();
        if (!$exists) {
            $pdo->exec("ALTER TABLE `$tableName` ADD COLUMN `$columnName` $columnDefinition");
        }
    } catch (\PDOException $e) {
        // Handled gracefully
    }
}

// Helper to add index if missing
function addIndexIfNeeded($pdo, $tableName, $indexColumn, $indexName = null) {
    $indexName = $indexName ?? "idx_{$tableName}_{$indexColumn}";
    try {
        $stmt = $pdo->query("SHOW INDEX FROM `$tableName` WHERE Key_name = '$indexName'");
        $exists = $stmt->fetch();
        if (!$exists) {
            $pdo->exec("ALTER TABLE `$tableName` ADD INDEX `$indexName` (`$indexColumn`)");
        }
    } catch (\PDOException $e) {
        // Handled gracefully
    }
}

// 0. Users Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(50) PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NULL,
    userType VARCHAR(20) NOT NULL DEFAULT 'guest',
    profilePic VARCHAR(255) NULL,
    createdAt VARCHAR(30) NOT NULL,
    expiresAt VARCHAR(30) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 1. Employees Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS employees (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20),
    joiningDate VARCHAR(30),
    relievingDate VARCHAR(30) NULL,
    photoPath VARCHAR(255) NULL,
    baseSalary DECIMAL(10,2) DEFAULT 0.00,
    salaryBasis VARCHAR(20) DEFAULT 'monthly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 2. Attendance Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS attendance (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    employeeId VARCHAR(50) NOT NULL,
    date VARCHAR(30) NOT NULL,
    status VARCHAR(20) NOT NULL,
    checkInTime VARCHAR(20),
    checkOutTime VARCHAR(20),
    amountGiven DECIMAL(10,2) DEFAULT 0.00,
    paymentDescription TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 3. Ironing Workers Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_workers (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20),
    joiningDate VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 4. Ironing Rates Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_rates (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    workerId VARCHAR(50) NOT NULL,
    clothingType VARCHAR(50) NOT NULL,
    rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    date VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 5. Ironing Records Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_records (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    workerId VARCHAR(50) NOT NULL,
    date VARCHAR(30) NOT NULL,
    clothesCount TEXT,
    totalWage DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 6. Ironing Payments Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_payments (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    workerId VARCHAR(50) NOT NULL,
    date VARCHAR(30) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    description TEXT,
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 7. Appliances Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS appliances (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    brand VARCHAR(50),
    serialNumber VARCHAR(100),
    warrantyStart VARCHAR(30),
    warrantyEnd VARCHAR(30),
    invoicePath VARCHAR(255),
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 8. Service Records Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS service_records (
    id VARCHAR(50) PRIMARY KEY,
    userId VARCHAR(50) NULL,
    applianceId VARCHAR(50) NOT NULL,
    serviceDate VARCHAR(30) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    remarks TEXT,
    billPath VARCHAR(255),
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Schema Columns & Indexes Self-Healing
$tables = [
    'employees',
    'attendance',
    'ironing_workers',
    'ironing_rates',
    'ironing_records',
    'ironing_payments',
    'appliances',
    'service_records'
];

foreach ($tables as $t) {
    addColumnIfNeeded($pdo, $t, 'userId', 'VARCHAR(50) NULL');
    addIndexIfNeeded($pdo, $t, 'userId');
}

// Ensure specific extra columns exist
addColumnIfNeeded($pdo, 'users', 'profilePic', 'VARCHAR(255) NULL');
addColumnIfNeeded($pdo, 'employees', 'photoPath', 'VARCHAR(255) NULL');
addColumnIfNeeded($pdo, 'employees', 'relievingDate', 'VARCHAR(30) NULL');
addIndexIfNeeded($pdo, 'attendance', 'employeeId');
addIndexIfNeeded($pdo, 'ironing_rates', 'workerId');
addIndexIfNeeded($pdo, 'ironing_records', 'workerId');
addIndexIfNeeded($pdo, 'ironing_payments', 'workerId');
addIndexIfNeeded($pdo, 'service_records', 'applianceId');
