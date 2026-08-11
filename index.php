<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-Id");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'db.php';

// Set default Content-Type header to JSON
header("Content-Type: application/json; charset=UTF-8");

// Parse input JSON
$input = json_decode(file_get_contents('php://input'), true);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');
$parts = explode('/', trim($requestUri, '/'));

// Check if it is an API route
$apiIndex = array_search('api', $parts);
if ($apiIndex === false) {
    $route = isset($parts[0]) ? $parts[0] : '';
    $id = isset($parts[1]) ? $parts[1] : null;
    $subRoute = isset($parts[2]) ? $parts[2] : null;
} else {
    $route = isset($parts[$apiIndex + 1]) ? $parts[$apiIndex + 1] : '';
    $id = isset($parts[$apiIndex + 2]) ? $parts[$apiIndex + 2] : null;
    $subRoute = isset($parts[$apiIndex + 3]) ? $parts[$apiIndex + 3] : null;
}

// Helper to return JSON responses
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Helper to return error responses
function sendError($message, $statusCode = 400) {
    sendResponse(['error' => $message], $statusCode);
}

// Serve Interactive API Documentation on root/index access

if ($route === '' || $route === 'index.php' || $route === 'api') {
    header("Content-Type: text/html; charset=UTF-8");
    require_once 'admin_dashboard.php';
    exit;
}

// Extract scopes user context from headers
$userId = $_SERVER['HTTP_X_USER_ID'] ?? null;

// Routing logic for Non-Scoped Authentication Endpoints
if ($route === 'register' && $requestMethod === 'POST') {
    if (!$input || empty($input['username']) || empty($input['password'])) {
        sendError('Username and password are required');
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$input['username']]);
    if ($stmt->fetchColumn() > 0) {
        sendError('Username is already taken');
    }
    
    $userId = uniqid('user_', true);
    $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
    $createdAt = date('c');
    $expiresAt = date('c', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("INSERT INTO users (id, username, password, userType, createdAt, expiresAt) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $input['username'],
        $hashedPassword,
        'guest',
        $createdAt,
        $expiresAt
    ]);
    
    sendResponse([
        'success' => true,
        'userId' => $userId,
        'username' => $input['username'],
        'userType' => 'guest',
        'expiresAt' => $expiresAt,
        'profilePic' => null
    ]);
}

if ($route === 'login' && $requestMethod === 'POST') {
    if (!$input || empty($input['username']) || empty($input['password'])) {
        sendError('Username and password are required');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$input['username']]);
    $userObj = $stmt->fetch();
    
    if (!$userObj || !password_verify($input['password'], $userObj['password'])) {
        sendError('Invalid username or password', 401);
    }
    
    sendResponse([
        'success' => true,
        'userId' => $userObj['id'],
        'username' => $userObj['username'],
        'userType' => $userObj['userType'],
        'expiresAt' => $userObj['expiresAt'],
        'profilePic' => $userObj['profilePic']
    ]);
}

if ($route === 'register-guest' && $requestMethod === 'POST') {
    $guestId = uniqid('guest_', true);
    $username = 'guest_' . bin2hex(random_bytes(4));
    $createdAt = date('c');
    $expiresAt = date('c', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("INSERT INTO users (id, username, password, userType, createdAt, expiresAt) VALUES (?, ?, NULL, ?, ?, ?)");
    $stmt->execute([
        $guestId,
        $username,
        'guest',
        $createdAt,
        $expiresAt
    ]);
    
    sendResponse([
        'success' => true,
        'userId' => $guestId,
        'username' => $username,
        'userType' => 'guest',
        'expiresAt' => $expiresAt
    ]);
}

if ($route === 'convert-guest' && $requestMethod === 'POST') {
    if (!$input || empty($input['userId']) || empty($input['username']) || empty($input['password'])) {
        sendError('User ID, new username, and new password are required');
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$input['username']]);
    if ($stmt->fetchColumn() > 0) {
        sendError('Username is already taken');
    }
    
    // Check if guest user exists
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ? AND userType = 'guest'");
    $stmtUser->execute([$input['userId']]);
    $guestUser = $stmtUser->fetch();
    if (!$guestUser) {
        sendError('Guest account not found', 404);
    }
    
    $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
    
    $stmtUpdate = $pdo->prepare("UPDATE users SET username = ?, password = ?, userType = 'registered', expiresAt = NULL WHERE id = ?");
    $stmtUpdate->execute([
        $input['username'],
        $hashedPassword,
        $input['userId']
    ]);
    
    sendResponse([
        'success' => true,
        'userId' => $input['userId'],
        'username' => $input['username'],
        'userType' => 'registered',
        'expiresAt' => null
    ]);
}

if ($route === 'login-google' && $requestMethod === 'POST') {
    if (!$input || empty($input['googleId']) || empty($input['email'])) {
        sendError('Google ID and email are required');
    }
    
    $googleUserId = 'google_' . $input['googleId'];
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$googleUserId]);
    $userObj = $stmt->fetch();
    
    if ($userObj) {
        sendResponse([
            'success' => true,
            'userId' => $userObj['id'],
            'username' => $userObj['username'],
            'userType' => $userObj['userType'],
            'expiresAt' => $userObj['expiresAt'],
            'profilePic' => $userObj['profilePic']
        ]);
    } else {
        // Create new user profile linked to Google ID
        $createdAt = date('c');
        $expiresAt = date('c', strtotime('+30 days'));
        $stmt = $pdo->prepare("INSERT INTO users (id, username, password, userType, createdAt, expiresAt) VALUES (?, ?, NULL, ?, ?, ?)");
        $stmt->execute([
            $googleUserId,
            $input['email'],
            'guest',
            $createdAt,
            $expiresAt
        ]);
        
        sendResponse([
            'success' => true,
            'userId' => $googleUserId,
            'username' => $input['email'],
            'userType' => 'guest',
            'expiresAt' => $expiresAt,
            'profilePic' => null
        ]);
    }
}

if ($route === 'admin-overview') {
    $token = $_GET['token'] ?? '';
    if ($token !== 'TodoAdmin102030') {
        sendError('Unauthorized', 401);
    }
    
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $trialUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE userType = 'guest'")->fetchColumn();
    $premiumUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE userType = 'registered'")->fetchColumn();
    $totalEmployees = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    $totalIroningWorkers = $pdo->query("SELECT COUNT(*) FROM ironing_workers")->fetchColumn();
    
    $stmt = $pdo->query("SELECT u.id, u.username, u.userType, u.createdAt, u.expiresAt, u.profilePic,
        (SELECT COUNT(*) FROM employees WHERE employees.userId = u.id) as employee_count,
        (SELECT COUNT(*) FROM ironing_workers WHERE ironing_workers.userId = u.id) as ironing_worker_count,
        (SELECT COUNT(*) FROM appliances WHERE appliances.userId = u.id) as appliance_count
        FROM users u
        ORDER BY u.createdAt DESC");
    $users = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'metrics' => [
            'total_users' => $totalUsers,
            'trial_users' => $trialUsers,
            'premium_users' => $premiumUsers,
            'total_employees' => $totalEmployees,
            'total_ironing_workers' => $totalIroningWorkers
        ],
        'users' => $users
    ]);
}

if ($route === 'admin-user-details') {
    $token = $_GET['token'] ?? '';
    if ($token !== 'TodoAdmin102030') {
        sendError('Unauthorized', 401);
    }
    $targetUserId = $_GET['userId'] ?? '';
    if (empty($targetUserId)) {
        sendError('User ID is required');
    }
    
    $stmtEmp = $pdo->prepare("SELECT * FROM employees WHERE userId = ?");
    $stmtEmp->execute([$targetUserId]);
    $employees = $stmtEmp->fetchAll();
    
    $stmtIron = $pdo->prepare("SELECT * FROM ironing_workers WHERE userId = ?");
    $stmtIron->execute([$targetUserId]);
    $ironingWorkers = $stmtIron->fetchAll();
    
    $stmtApp = $pdo->prepare("SELECT * FROM appliances WHERE userId = ?");
    $stmtApp->execute([$targetUserId]);
    $appliances = $stmtApp->fetchAll();
    
    sendResponse([
        'success' => true,
        'employees' => $employees,
        'ironingWorkers' => $ironingWorkers,
        'appliances' => $appliances
    ]);
}

// Verify Scoped Endpoints Header
if (!$userId) {
    sendError('Unauthorized: Missing X-User-Id header context', 401);
}

// Validate User Expiry for Guests
$stmtVerify = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtVerify->execute([$userId]);
$activeUserObj = $stmtVerify->fetch();

if (!$activeUserObj) {
    sendError('Unauthorized: Invalid active session', 401);
}

if ($activeUserObj['userType'] === 'guest' && !empty($activeUserObj['expiresAt'])) {
    if (time() > strtotime($activeUserObj['expiresAt'])) {
        sendError('Unauthorized: Guest account has expired. Please register to convert.', 403);
    }
}

// -------------------------------------------------------------
// Route Switcher (Scoped by User ID)
switch ($route) {
    
    // File Upload Handler
    case 'upload':
        if ($requestMethod !== 'POST') {
            sendError('Method Not Allowed', 405);
        }
        if (!isset($_FILES['file'])) {
            sendError('No file uploaded');
        }
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            sendError('File upload error code: ' . $file['error']);
        }
        
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('file_', true) . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            sendResponse([
                'success' => true,
                'path' => $targetPath
            ]);
        } else {
            sendError('Failed to save uploaded file', 500);
        }
        break;

    case 'get-profile':
        if ($requestMethod !== 'GET') {
            sendError('Method Not Allowed', 405);
        }
        sendResponse([
            'success' => true,
            'userId' => $activeUserObj['id'],
            'username' => $activeUserObj['username'],
            'userType' => $activeUserObj['userType'],
            'profilePic' => $activeUserObj['profilePic'],
            'expiresAt' => $activeUserObj['expiresAt']
        ]);
        break;

    case 'update-profile':
        if ($requestMethod !== 'POST') {
            sendError('Method Not Allowed', 405);
        }
        if (!$input || empty($input['username'])) {
            sendError('Username is required');
        }
        $newUsername = trim($input['username']);
        $profilePic = isset($input['profilePic']) ? trim($input['profilePic']) : $activeUserObj['profilePic'];
        
        // Verify unique username if changed
        if ($newUsername !== $activeUserObj['username']) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$newUsername]);
            if ($stmt->fetchColumn() > 0) {
                sendError('Username is already taken');
            }
        }
        
        try {
            if (!empty($input['password'])) {
                $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
                $stmtUpdate = $pdo->prepare("UPDATE users SET username = ?, password = ?, profilePic = ? WHERE id = ?");
                $stmtUpdate->execute([$newUsername, $hashedPassword, $profilePic, $userId]);
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE users SET username = ?, profilePic = ? WHERE id = ?");
                $stmtUpdate->execute([$newUsername, $profilePic, $userId]);
            }
        } catch (\PDOException $e) {
            // Check if column is missing (SQLSTATE 42S22 or 1054)
            if ($e->getCode() === '42S22' || strpos($e->getMessage(), '1054') !== false) {
                try {
                    // Self-heal table schema
                    $pdo->exec("ALTER TABLE users ADD COLUMN profilePic VARCHAR(255) NULL");
                    
                    // Retry statement
                    if (!empty($input['password'])) {
                        $stmtUpdate = $pdo->prepare("UPDATE users SET username = ?, password = ?, profilePic = ? WHERE id = ?");
                        $stmtUpdate->execute([$newUsername, $hashedPassword, $profilePic, $userId]);
                    } else {
                        $stmtUpdate = $pdo->prepare("UPDATE users SET username = ?, profilePic = ? WHERE id = ?");
                        $stmtUpdate->execute([$newUsername, $profilePic, $userId]);
                    }
                } catch (\PDOException $ex) {
                    sendError('Database auto-heal alteration failed: ' . $ex->getMessage(), 500);
                }
            } else {
                sendError('Database update failed: ' . $e->getMessage(), 500);
            }
        }
        
        sendResponse([
            'success' => true,
            'username' => $newUsername,
            'profilePic' => $profilePic
        ]);
        break;

    case 'update-subscription':
        if ($requestMethod !== 'POST') {
            sendError('Method Not Allowed', 405);
        }
        
        // Upgrade user to registered/premium status
        $stmtUpdate = $pdo->prepare("UPDATE users SET userType = 'registered', expiresAt = NULL WHERE id = ?");
        $stmtUpdate->execute([$userId]);
        
        sendResponse([
            'success' => true,
            'userType' => 'registered',
            'expiresAt' => null
        ]);
        break;

    // Employees REST
    case 'employees':
        try {
            if ($requestMethod === 'GET') {
                $stmt = $pdo->prepare("SELECT * FROM employees WHERE userId = ?");
                $stmt->execute([$userId]);
                sendResponse($stmt->fetchAll());
            } elseif ($requestMethod === 'POST') {
                if (!$input) sendError('Invalid JSON input');
                try {
                    $stmt = $pdo->prepare("INSERT INTO employees (id, userId, name, contact, joiningDate, baseSalary, salaryBasis, photoPath, relievingDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $input['id'],
                        $userId,
                        $input['name'],
                        $input['contact'] ?? null,
                        $input['joiningDate'] ?? null,
                        $input['baseSalary'] ?? 0.0,
                        $input['salaryBasis'] ?? 'monthly',
                        $input['photoPath'] ?? null,
                        $input['relievingDate'] ?? null
                    ]);
                } catch (\PDOException $ex) {
                    if ($ex->getCode() === '42S22' || strpos($ex->getMessage(), '1054') !== false) {
                        try {
                            $pdo->exec("ALTER TABLE employees ADD COLUMN photoPath VARCHAR(255) NULL, ADD COLUMN relievingDate VARCHAR(30) NULL");
                        } catch (\PDOException $alterEx) {
                            // ignore if columns already exist
                        }
                        $stmt = $pdo->prepare("INSERT INTO employees (id, userId, name, contact, joiningDate, baseSalary, salaryBasis, photoPath, relievingDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $input['id'],
                            $userId,
                            $input['name'],
                            $input['contact'] ?? null,
                            $input['joiningDate'] ?? null,
                            $input['baseSalary'] ?? 0.0,
                            $input['salaryBasis'] ?? 'monthly',
                            $input['photoPath'] ?? null,
                            $input['relievingDate'] ?? null
                        ]);
                    } else {
                        throw $ex;
                    }
                }
                sendResponse(['success' => true]);
            } elseif ($requestMethod === 'PUT') {
                if (!$id) sendError('Employee ID required');
                if (!$input) sendError('Invalid JSON input');
                try {
                    $stmt = $pdo->prepare("UPDATE employees SET name = ?, contact = ?, joiningDate = ?, baseSalary = ?, salaryBasis = ?, photoPath = ?, relievingDate = ? WHERE id = ? AND userId = ?");
                    $stmt->execute([
                        $input['name'],
                        $input['contact'] ?? null,
                        $input['joiningDate'] ?? null,
                        $input['baseSalary'] ?? 0.0,
                        $input['salaryBasis'] ?? 'monthly',
                        $input['photoPath'] ?? null,
                        $input['relievingDate'] ?? null,
                        $id,
                        $userId
                    ]);
                } catch (\PDOException $ex) {
                    if ($ex->getCode() === '42S22' || strpos($ex->getMessage(), '1054') !== false) {
                        try {
                            $pdo->exec("ALTER TABLE employees ADD COLUMN photoPath VARCHAR(255) NULL, ADD COLUMN relievingDate VARCHAR(30) NULL");
                        } catch (\PDOException $alterEx) {
                            // ignore
                        }
                        $stmt = $pdo->prepare("UPDATE employees SET name = ?, contact = ?, joiningDate = ?, baseSalary = ?, salaryBasis = ?, photoPath = ?, relievingDate = ? WHERE id = ? AND userId = ?");
                        $stmt->execute([
                            $input['name'],
                            $input['contact'] ?? null,
                            $input['joiningDate'] ?? null,
                            $input['baseSalary'] ?? 0.0,
                            $input['salaryBasis'] ?? 'monthly',
                            $input['photoPath'] ?? null,
                            $input['relievingDate'] ?? null,
                            $id,
                            $userId
                        ]);
                    } else {
                        throw $ex;
                    }
                }
                sendResponse(['success' => true]);
            } elseif ($requestMethod === 'DELETE') {
                if (!$id) sendError('Employee ID required');
                $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ? AND userId = ?");
                $stmt->execute([$id, $userId]);
                // Clean up scoped attendance
                $pdo->prepare("DELETE FROM attendance WHERE employeeId = ? AND userId = ?")->execute([$id, $userId]);
                sendResponse(['success' => true]);
            }
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    // Attendance REST
    case 'attendance':
        if ($requestMethod === 'GET') {
            $employeeId = $_GET['employeeId'] ?? null;
            if ($employeeId) {
                $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employeeId = ? AND userId = ? ORDER BY date DESC");
                $stmt->execute([$employeeId, $userId]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM attendance WHERE userId = ? ORDER BY date DESC");
                $stmt->execute([$userId]);
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO attendance (id, userId, employeeId, date, status, checkInTime, checkOutTime, amountGiven, paymentDescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $userId,
                $input['employeeId'],
                $input['date'],
                $input['status'],
                $input['checkInTime'] ?? null,
                $input['checkOutTime'] ?? null,
                $input['amountGiven'] ?? 0.0,
                $input['paymentDescription'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Attendance log ID required');
            $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = ? AND userId = ?");
            $stmt->execute([$id, $userId]);
            sendResponse(['success' => true]);
        }
        break;

    // Ironing Workers REST & Subroutes
    case 'ironing-workers':
        if ($id && $subRoute === 'rates') {
            if ($requestMethod === 'GET') {
                $stmt = $pdo->prepare("SELECT * FROM ironing_rates WHERE workerId = ? AND userId = ?");
                $stmt->execute([$id, $userId]);
                sendResponse($stmt->fetchAll());
            } elseif ($requestMethod === 'POST') {
                if (!$input) sendError('Invalid JSON input');
                $stmt = $pdo->prepare("REPLACE INTO ironing_rates (id, userId, workerId, clothingType, rate, date) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['id'],
                    $userId,
                    $id,
                    $input['clothingType'],
                    $input['rate'],
                    $input['date'] ?? null
                ]);
                sendResponse(['success' => true]);
            }
            break;
        }

        if ($requestMethod === 'GET') {
            $stmt = $pdo->prepare("SELECT * FROM ironing_workers WHERE userId = ?");
            $stmt->execute([$userId]);
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO ironing_workers (id, userId, name, contact, joiningDate) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $userId,
                $input['name'],
                $input['contact'] ?? null,
                $input['joiningDate'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Worker ID required');
            $stmt = $pdo->prepare("DELETE FROM ironing_workers WHERE id = ? AND userId = ?");
            $stmt->execute([$id, $userId]);
            // Clean worker details scoped
            $pdo->prepare("DELETE FROM ironing_rates WHERE workerId = ? AND userId = ?")->execute([$id, $userId]);
            $pdo->prepare("DELETE FROM ironing_records WHERE workerId = ? AND userId = ?")->execute([$id, $userId]);
            $pdo->prepare("DELETE FROM ironing_payments WHERE workerId = ? AND userId = ?")->execute([$id, $userId]);
            sendResponse(['success' => true]);
        }
        break;

    // Ironing Records
    case 'ironing-records':
        if ($requestMethod === 'GET') {
            $workerId = $_GET['workerId'] ?? null;
            if ($workerId) {
                $stmt = $pdo->prepare("SELECT * FROM ironing_records WHERE workerId = ? AND userId = ? ORDER BY date DESC");
                $stmt->execute([$workerId, $userId]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM ironing_records WHERE userId = ? ORDER BY date DESC");
                $stmt->execute([$userId]);
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO ironing_records (id, userId, workerId, date, clothesCount, totalWage, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $userId,
                $input['workerId'],
                $input['date'],
                is_array($input['clothesCount']) ? json_encode($input['clothesCount']) : $input['clothesCount'],
                $input['totalWage'],
                $input['createdAt'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Record ID required');
            $stmt = $pdo->prepare("DELETE FROM ironing_records WHERE id = ? AND userId = ?");
            $stmt->execute([$id, $userId]);
            sendResponse(['success' => true]);
        }
        break;

    // Ironing Payments
    case 'ironing-payments':
        if ($requestMethod === 'GET') {
            $workerId = $_GET['workerId'] ?? null;
            if ($workerId) {
                $stmt = $pdo->prepare("SELECT * FROM ironing_payments WHERE workerId = ? AND userId = ? ORDER BY date DESC");
                $stmt->execute([$workerId, $userId]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM ironing_payments WHERE userId = ? ORDER BY date DESC");
                $stmt->execute([$userId]);
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO ironing_payments (id, userId, workerId, date, amount, description, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $userId,
                $input['workerId'],
                $input['date'],
                $input['amount'],
                $input['description'] ?? null,
                $input['createdAt'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Payment ID required');
            $stmt = $pdo->prepare("DELETE FROM ironing_payments WHERE id = ? AND userId = ?");
            $stmt->execute([$id, $userId]);
            sendResponse(['success' => true]);
        }
        break;

    // Appliances Hub
    case 'appliances':
        if ($requestMethod === 'GET') {
            $stmt = $pdo->prepare("SELECT * FROM appliances WHERE userId = ? ORDER BY createdAt DESC");
            $stmt->execute([$userId]);
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO appliances (id, userId, name, type, brand, serialNumber, warrantyStart, warrantyEnd, invoicePath, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $userId,
                $input['name'],
                $input['type'] ?? null,
                $input['brand'] ?? null,
                $input['serialNumber'] ?? null,
                $input['warrantyStart'] ?? null,
                $input['warrantyEnd'] ?? null,
                $input['invoicePath'] ?? null,
                $input['createdAt'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'PUT') {
            if (!$id) sendError('Appliance ID required');
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("UPDATE appliances SET name = ?, type = ?, brand = ?, serialNumber = ?, warrantyStart = ?, warrantyEnd = ?, invoicePath = ?, createdAt = ? WHERE id = ? AND userId = ?");
            $stmt->execute([
                $input['name'],
                $input['type'] ?? null,
                $input['brand'] ?? null,
                $input['serialNumber'] ?? null,
                $input['warrantyStart'] ?? null,
                $input['warrantyEnd'] ?? null,
                $input['invoicePath'] ?? null,
                $input['createdAt'] ?? null,
                $id,
                $userId
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Appliance ID required');
            $stmt = $pdo->prepare("DELETE FROM appliances WHERE id = ? AND userId = ?");
            $stmt->execute([$id, $userId]);
            // Clean scoped repair history
            $pdo->prepare("DELETE FROM service_records WHERE applianceId = ? AND userId = ?")->execute([$id, $userId]);
            sendResponse(['success' => true]);
        }
        break;

    // Service Records REST
    case 'service-records':
        if ($requestMethod === 'GET') {
            $applianceId = $_GET['applianceId'] ?? null;
            if ($applianceId) {
                $stmt = $pdo->prepare("SELECT * FROM service_records WHERE applianceId = ? AND userId = ? ORDER BY serviceDate DESC");
                $stmt->execute([$applianceId, $userId]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM service_records WHERE userId = ? ORDER BY serviceDate DESC");
                $stmt->execute([$userId]);
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO service_records (id, userId, applianceId, serviceDate, price, remarks, billPath, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $userId,
                $input['applianceId'],
                $input['serviceDate'],
                $input['price'],
                $input['remarks'] ?? null,
                $input['billPath'] ?? null,
                $input['createdAt'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Service record ID required');
            $stmt = $pdo->prepare("DELETE FROM service_records WHERE id = ? AND userId = ?");
            $stmt->execute([$id, $userId]);
            sendResponse(['success' => true]);
        }
        break;

    default:
        sendError('Endpoint not found: ' . $route, 404);
}
