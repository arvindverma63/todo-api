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
        // Log or handle table creation errors
    }
}

// 1. Employees Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS employees (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20),
    joiningDate VARCHAR(30),
    baseSalary DECIMAL(10,2),
    salaryBasis VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 2. Attendance Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS attendance (
    id VARCHAR(50) PRIMARY KEY,
    employeeId VARCHAR(50) NOT NULL,
    date VARCHAR(30) NOT NULL,
    status VARCHAR(20) NOT NULL,
    checkInTime VARCHAR(20),
    checkOutTime VARCHAR(20),
    amountGiven DECIMAL(10,2),
    paymentDescription TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 3. Ironing Workers Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_workers (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20),
    joiningDate VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 4. Ironing Rates Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_rates (
    id VARCHAR(50) PRIMARY KEY,
    workerId VARCHAR(50) NOT NULL,
    clothingType VARCHAR(50) NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    date VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 5. Ironing Records Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_records (
    id VARCHAR(50) PRIMARY KEY,
    workerId VARCHAR(50) NOT NULL,
    date VARCHAR(30) NOT NULL,
    clothesCount TEXT,
    totalWage DECIMAL(10,2) NOT NULL,
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 6. Ironing Payments Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS ironing_payments (
    id VARCHAR(50) PRIMARY KEY,
    workerId VARCHAR(50) NOT NULL,
    date VARCHAR(30) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 7. Appliances Table
createTable($pdo, "CREATE TABLE IF NOT EXISTS appliances (
    id VARCHAR(50) PRIMARY KEY,
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
    applianceId VARCHAR(50) NOT NULL,
    serviceDate VARCHAR(30) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    remarks TEXT,
    billPath VARCHAR(255),
    createdAt VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
