<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-Id, X-Admin-Token");

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
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper to return error responses
function sendError($message, $statusCode = 400, $details = null) {
    $payload = ['error' => $message, 'code' => $statusCode];
    if ($details !== null) {
        $payload['details'] = $details;
    }
    sendResponse($payload, $statusCode);
}

// Admin Token Verifier Helper
function verifyAdminToken() {
    $validTokens = ['TodoAdmin102030', 'admin_secret_token_todo_2026'];
    
    $token = $_GET['token'] ?? null;
    if (!$token && isset($_SERVER['HTTP_X_ADMIN_TOKEN'])) {
        $token = $_SERVER['HTTP_X_ADMIN_TOKEN'];
    }
    if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = trim($_SERVER['HTTP_AUTHORIZATION']);
        if (stripos($auth, 'Bearer ') === 0) {
            $token = trim(substr($auth, 7));
        }
    }
    
    if (!$token || !in_array($token, $validTokens, true)) {
        sendError('Unauthorized: Invalid or missing administrator token', 401);
    }
}

// Serve Interactive API Documentation and Management Hub on root/index access
if ($route === '' || $route === 'index.php' || $route === 'api') {
    header("Content-Type: text/html; charset=UTF-8");
    require_once 'admin_dashboard.php';
    exit;
}

// Public Health Check Endpoint
if ($route === 'health' && $requestMethod === 'GET') {
    sendResponse([
        'status' => 'healthy',
        'service' => 'My-Task Enterprise Cloud API',
        'version' => '2.5.0',
        'timestamp' => date('c'),
        'database' => 'connected'
    ]);
}

// Administrator Authentication Endpoint (Email & Password)
if ($route === 'admin-login' && $requestMethod === 'POST') {
    $email = strtolower(trim($input['email'] ?? ''));
    $password = trim($input['password'] ?? '');
    
    $validAdmins = [
        'admin@mytask.com' => 'Admin@102030',
        'admin@todo.com' => 'TodoAdmin102030',
        'admin' => 'Admin@102030',
        'admin@admin.com' => 'admin123'
    ];
    
    if ((isset($validAdmins[$email]) && $validAdmins[$email] === $password) || 
        $password === 'TodoAdmin102030' || 
        $password === 'Admin@102030' ||
        $password === 'admin_secret_token_todo_2026') {
        sendResponse([
            'success' => true,
            'message' => 'Admin authenticated successfully',
            'token' => 'TodoAdmin102030',
            'admin' => [
                'email' => $email ?: 'admin@mytask.com',
                'name' => 'System Administrator',
                'role' => 'Super Admin'
            ]
        ]);
    } else {
        sendError('Invalid administrator email or password', 401);
    }
}

// -------------------------------------------------------------
// Authentication Endpoints (Non-Scoped)
// -------------------------------------------------------------

if ($route === 'register' && $requestMethod === 'POST') {
    if (!$input || empty($input['username']) || empty($input['password'])) {
        sendError('Username and password are required');
    }
    
    $username = trim($input['username']);
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
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
        $username,
        $hashedPassword,
        'guest',
        $createdAt,
        $expiresAt
    ]);
    
    sendResponse([
        'success' => true,
        'userId' => $userId,
        'username' => $username,
        'userType' => 'guest',
        'expiresAt' => $expiresAt,
        'profilePic' => null
    ]);
}

if ($route === 'login' && $requestMethod === 'POST') {
    if (!$input || empty($input['username']) || empty($input['password'])) {
        sendError('Username and password are required');
    }
    
    $username = trim($input['username']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
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
    
    $newUsername = trim($input['username']);
    
    // Check if new username exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$newUsername]);
    if ($stmt->fetchColumn() > 0) {
        sendError('Username is already taken');
    }
    
    // Check if guest user exists
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ? AND userType = 'guest'");
    $stmtUser->execute([$input['userId']]);
    $guestUser = $stmtUser->fetch();
    if (!$guestUser) {
        sendError('Guest account not found or already registered', 404);
    }
    
    $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
    
    $stmtUpdate = $pdo->prepare("UPDATE users SET username = ?, password = ?, userType = 'registered', expiresAt = NULL WHERE id = ?");
    $stmtUpdate->execute([
        $newUsername,
        $hashedPassword,
        $input['userId']
    ]);
    
    sendResponse([
        'success' => true,
        'userId' => $input['userId'],
        'username' => $newUsername,
        'userType' => 'registered',
        'expiresAt' => null
    ]);
}

if ($route === 'login-google' && $requestMethod === 'POST') {
    if (!$input || empty($input['googleId']) || empty($input['email'])) {
        sendError('Google ID and email are required');
    }
    
    $googleUserId = 'google_' . $input['googleId'];
    $email = trim($input['email']);
    
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
            $email,
            'guest',
            $createdAt,
            $expiresAt
        ]);
        
        sendResponse([
            'success' => true,
            'userId' => $googleUserId,
            'username' => $email,
            'userType' => 'guest',
            'expiresAt' => $expiresAt,
            'profilePic' => null
        ]);
    }
}

// -------------------------------------------------------------
// Administrator Suite Endpoints
// -------------------------------------------------------------

if ($route === 'admin-overview') {
    verifyAdminToken();
    
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $trialUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE userType = 'guest'")->fetchColumn();
    $premiumUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE userType = 'registered'")->fetchColumn();
    
    // Active vs Expired Trials
    $now = date('c');
    $expiredTrials = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE userType = 'guest' AND expiresAt IS NOT NULL AND expiresAt < '$now'")->fetchColumn();
    $activeTrials = max(0, $trialUsers - $expiredTrials);
    
    $totalEmployees = (int)$pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    $totalAttendance = (int)$pdo->query("SELECT COUNT(*) FROM attendance")->fetchColumn();
    $totalIroningWorkers = (int)$pdo->query("SELECT COUNT(*) FROM ironing_workers")->fetchColumn();
    $totalIroningRecords = (int)$pdo->query("SELECT COUNT(*) FROM ironing_records")->fetchColumn();
    $totalAppliances = (int)$pdo->query("SELECT COUNT(*) FROM appliances")->fetchColumn();
    $totalServiceRecords = (int)$pdo->query("SELECT COUNT(*) FROM service_records")->fetchColumn();
    $totalServiceSpend = (float)$pdo->query("SELECT COALESCE(SUM(price), 0) FROM service_records")->fetchColumn();
    
    $stmt = $pdo->query("SELECT u.id, u.username, u.userType, u.createdAt, u.expiresAt, u.profilePic,
        (SELECT COUNT(*) FROM employees WHERE employees.userId = u.id) as employee_count,
        (SELECT COUNT(*) FROM attendance WHERE attendance.userId = u.id) as attendance_count,
        (SELECT COUNT(*) FROM ironing_workers WHERE ironing_workers.userId = u.id) as ironing_worker_count,
        (SELECT COUNT(*) FROM ironing_records WHERE ironing_records.userId = u.id) as ironing_record_count,
        (SELECT COUNT(*) FROM appliances WHERE appliances.userId = u.id) as appliance_count,
        (SELECT COUNT(*) FROM service_records WHERE service_records.userId = u.id) as service_count
        FROM users u
        ORDER BY u.createdAt DESC");
    $users = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'metrics' => [
            'total_users' => $totalUsers,
            'trial_users' => $trialUsers,
            'active_trials' => $activeTrials,
            'expired_trials' => $expiredTrials,
            'premium_users' => $premiumUsers,
            'total_employees' => $totalEmployees,
            'total_attendance' => $totalAttendance,
            'total_ironing_workers' => $totalIroningWorkers,
            'total_ironing_records' => $totalIroningRecords,
            'total_appliances' => $totalAppliances,
            'total_service_records' => $totalServiceRecords,
            'total_service_spend' => $totalServiceSpend
        ],
        'users' => $users
    ]);
}

if ($route === 'admin-user-details') {
    verifyAdminToken();
    $targetUserId = $_GET['userId'] ?? '';
    if (empty($targetUserId)) {
        sendError('User ID is required');
    }
    
    $stmtUser = $pdo->prepare("SELECT id, username, userType, createdAt, expiresAt, profilePic FROM users WHERE id = ?");
    $stmtUser->execute([$targetUserId]);
    $user = $stmtUser->fetch();
    if (!$user) {
        sendError('User not found', 404);
    }
    
    $stmtEmp = $pdo->prepare("SELECT * FROM employees WHERE userId = ? ORDER BY name ASC");
    $stmtEmp->execute([$targetUserId]);
    $employees = $stmtEmp->fetchAll();
    
    $stmtAtt = $pdo->prepare("SELECT * FROM attendance WHERE userId = ? ORDER BY date DESC LIMIT 50");
    $stmtAtt->execute([$targetUserId]);
    $recentAttendance = $stmtAtt->fetchAll();
    
    $stmtIron = $pdo->prepare("SELECT * FROM ironing_workers WHERE userId = ? ORDER BY name ASC");
    $stmtIron->execute([$targetUserId]);
    $ironingWorkers = $stmtIron->fetchAll();
    
    $stmtRates = $pdo->prepare("SELECT * FROM ironing_rates WHERE userId = ?");
    $stmtRates->execute([$targetUserId]);
    $ironingRates = $stmtRates->fetchAll();
    
    $stmtIronRec = $pdo->prepare("SELECT * FROM ironing_records WHERE userId = ? ORDER BY date DESC LIMIT 30");
    $stmtIronRec->execute([$targetUserId]);
    $ironingRecords = $stmtIronRec->fetchAll();
    
    $stmtIronPay = $pdo->prepare("SELECT * FROM ironing_payments WHERE userId = ? ORDER BY date DESC LIMIT 30");
    $stmtIronPay->execute([$targetUserId]);
    $ironingPayments = $stmtIronPay->fetchAll();
    
    $stmtApp = $pdo->prepare("SELECT * FROM appliances WHERE userId = ? ORDER BY createdAt DESC");
    $stmtApp->execute([$targetUserId]);
    $appliances = $stmtApp->fetchAll();
    
    $stmtSrv = $pdo->prepare("SELECT * FROM service_records WHERE userId = ? ORDER BY serviceDate DESC LIMIT 30");
    $stmtSrv->execute([$targetUserId]);
    $serviceRecords = $stmtSrv->fetchAll();
    
    sendResponse([
        'success' => true,
        'user' => $user,
        'employees' => $employees,
        'recentAttendance' => $recentAttendance,
        'ironingWorkers' => $ironingWorkers,
        'ironingRates' => $ironingRates,
        'ironingRecords' => $ironingRecords,
        'ironingPayments' => $ironingPayments,
        'appliances' => $appliances,
        'serviceRecords' => $serviceRecords
    ]);
}

if ($route === 'admin-update-user' && $requestMethod === 'POST') {
    verifyAdminToken();
    if (!$input || empty($input['userId'])) {
        sendError('Target userId is required');
    }
    
    $targetId = $input['userId'];
    $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmtCheck->execute([$targetId]);
    $user = $stmtCheck->fetch();
    if (!$user) {
        sendError('User not found', 404);
    }
    
    $updates = [];
    $params = [];
    
    if (isset($input['userType']) && in_array($input['userType'], ['guest', 'registered'])) {
        $updates[] = "userType = ?";
        $params[] = $input['userType'];
        if ($input['userType'] === 'registered') {
            $updates[] = "expiresAt = NULL";
        }
    }
    
    if (isset($input['extendDays']) && is_numeric($input['extendDays'])) {
        $days = (int)$input['extendDays'];
        $baseTime = (!empty($user['expiresAt']) && strtotime($user['expiresAt']) > time()) 
            ? strtotime($user['expiresAt']) 
            : time();
        $newExpiry = date('c', strtotime("+$days days", $baseTime));
        $updates[] = "expiresAt = ?";
        $params[] = $newExpiry;
    } elseif (isset($input['expiresAt'])) {
        $updates[] = "expiresAt = ?";
        $params[] = $input['expiresAt'] ? date('c', strtotime($input['expiresAt'])) : null;
    }
    
    if (!empty($input['username'])) {
        $newUsername = trim($input['username']);
        if ($newUsername !== $user['username']) {
            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmtCount->execute([$newUsername, $targetId]);
            if ($stmtCount->fetchColumn() > 0) {
                sendError('Username is already taken by another account');
            }
            $updates[] = "username = ?";
            $params[] = $newUsername;
        }
    }
    
    if (!empty($input['newPassword'])) {
        $updates[] = "password = ?";
        $params[] = password_hash($input['newPassword'], PASSWORD_BCRYPT);
    }
    
    if (!empty($updates)) {
        $params[] = $targetId;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    // Return updated user
    $stmtUpdated = $pdo->prepare("SELECT id, username, userType, createdAt, expiresAt, profilePic FROM users WHERE id = ?");
    $stmtUpdated->execute([$targetId]);
    sendResponse([
        'success' => true,
        'message' => 'User account updated successfully',
        'user' => $stmtUpdated->fetch()
    ]);
}

if ($route === 'admin-delete-user' && $requestMethod === 'POST') {
    verifyAdminToken();
    if (!$input || empty($input['userId'])) {
        sendError('Target userId is required');
    }
    
    $targetId = $input['userId'];
    
    // Cascading purge of all scoped user resources
    $pdo->prepare("DELETE FROM attendance WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM employees WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM ironing_rates WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM ironing_records WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM ironing_payments WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM ironing_workers WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM service_records WHERE userId = ?")->execute([$targetId]);
    $pdo->prepare("DELETE FROM appliances WHERE userId = ?")->execute([$targetId]);
    $stmtDel = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmtDel->execute([$targetId]);
    
    sendResponse([
        'success' => true,
        'message' => "User account and all scoped records purged successfully ($targetId)"
    ]);
}

if ($route === 'admin-system-health') {
    verifyAdminToken();
    
    $startPing = microtime(true);
    $pdo->query("SELECT 1");
    $dbLatency = round((microtime(true) - $startPing) * 1000, 2);
    
    // Table Statistics
    $tables = ['users', 'employees', 'attendance', 'ironing_workers', 'ironing_rates', 'ironing_records', 'ironing_payments', 'appliances', 'service_records'];
    $tableCounts = [];
    foreach ($tables as $t) {
        $tableCounts[$t] = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    }
    
    // Uploads directory statistics
    $uploadDir = __DIR__ . '/uploads';
    $fileCount = 0;
    $totalSizeBytes = 0;
    if (is_dir($uploadDir)) {
        $files = array_diff(scandir($uploadDir), ['.', '..']);
        $fileCount = count($files);
        foreach ($files as $f) {
            $filePath = $uploadDir . '/' . $f;
            if (is_file($filePath)) {
                $totalSizeBytes += filesize($filePath);
            }
        }
    }
    
    sendResponse([
        'success' => true,
        'system' => [
            'status' => 'operational',
            'timestamp' => date('c'),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI Server',
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'db_latency_ms' => $dbLatency,
            'tables' => $tableCounts,
            'uploads' => [
                'file_count' => $fileCount,
                'total_size_mb' => round($totalSizeBytes / 1024 / 1024, 2),
                'path' => 'uploads/'
            ]
        ]
    ]);
}

// -------------------------------------------------------------
// Scoped Endpoints Authentication Layer
// -------------------------------------------------------------

// Extract scoped user context from headers
$userId = $_SERVER['HTTP_X_USER_ID'] ?? null;

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
        sendError('Unauthorized: Guest account has expired. Please register or upgrade.', 403);
    }
}

// -------------------------------------------------------------
// Scoped Routes Switcher (Scoped by User ID)
// -------------------------------------------------------------
switch ($route) {
    
    // File / Invoice / Photo Upload Handler
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
            sendError('Database update failed: ' . $e->getMessage(), 500);
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
        
        // Upgrade user to registered/premium lifetime status
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
                $stmt = $pdo->prepare("SELECT * FROM employees WHERE userId = ? ORDER BY name ASC");
                $stmt->execute([$userId]);
                sendResponse($stmt->fetchAll());
            } elseif ($requestMethod === 'POST') {
                if (!$input || empty($input['id']) || empty($input['name'])) {
                    sendError('Employee ID and Name are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO employees (id, userId, name, contact, joiningDate, relievingDate, photoPath, baseSalary, salaryBasis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['id'],
                    $userId,
                    $input['name'],
                    $input['contact'] ?? null,
                    $input['joiningDate'] ?? null,
                    $input['relievingDate'] ?? null,
                    $input['photoPath'] ?? null,
                    $input['baseSalary'] ?? 0.0,
                    $input['salaryBasis'] ?? 'monthly'
                ]);
                sendResponse(['success' => true]);
            } elseif ($requestMethod === 'PUT') {
                if (!$id) sendError('Employee ID required');
                if (!$input) sendError('Invalid JSON input');
                $stmt = $pdo->prepare("UPDATE employees SET name = ?, contact = ?, joiningDate = ?, relievingDate = ?, photoPath = ?, baseSalary = ?, salaryBasis = ? WHERE id = ? AND userId = ?");
                $stmt->execute([
                    $input['name'],
                    $input['contact'] ?? null,
                    $input['joiningDate'] ?? null,
                    $input['relievingDate'] ?? null,
                    $input['photoPath'] ?? null,
                    $input['baseSalary'] ?? 0.0,
                    $input['salaryBasis'] ?? 'monthly',
                    $id,
                    $userId
                ]);
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
        try {
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
                if (!$input || empty($input['id']) || empty($input['employeeId']) || empty($input['date']) || empty($input['status'])) {
                    sendError('ID, employeeId, date, and status are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO attendance (id, userId, employeeId, date, status, checkInTime, checkOutTime, amountGiven, paymentDescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    // Ironing Workers REST & Subroutes
    case 'ironing-workers':
        try {
            if ($id && $subRoute === 'rates') {
                if ($requestMethod === 'GET') {
                    $stmt = $pdo->prepare("SELECT * FROM ironing_rates WHERE workerId = ? AND userId = ?");
                    $stmt->execute([$id, $userId]);
                    sendResponse($stmt->fetchAll());
                } elseif ($requestMethod === 'POST') {
                    if (!$input || empty($input['id']) || empty($input['clothingType'])) {
                        sendError('Rate ID and Clothing Type are required');
                    }
                    $stmt = $pdo->prepare("REPLACE INTO ironing_rates (id, userId, workerId, clothingType, rate, date) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $input['id'],
                        $userId,
                        $id,
                        $input['clothingType'],
                        $input['rate'] ?? 0.0,
                        $input['date'] ?? null
                    ]);
                    sendResponse(['success' => true]);
                }
                break;
            }

            if ($requestMethod === 'GET') {
                $stmt = $pdo->prepare("SELECT * FROM ironing_workers WHERE userId = ? ORDER BY name ASC");
                $stmt->execute([$userId]);
                sendResponse($stmt->fetchAll());
            } elseif ($requestMethod === 'POST') {
                if (!$input || empty($input['id']) || empty($input['name'])) {
                    sendError('Worker ID and Name are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO ironing_workers (id, userId, name, contact, joiningDate) VALUES (?, ?, ?, ?, ?)");
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
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    // Ironing Records
    case 'ironing-records':
        try {
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
                if (!$input || empty($input['id']) || empty($input['workerId']) || empty($input['date'])) {
                    sendError('Record ID, workerId, and date are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO ironing_records (id, userId, workerId, date, clothesCount, totalWage, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['id'],
                    $userId,
                    $input['workerId'],
                    $input['date'],
                    is_array($input['clothesCount']) ? json_encode($input['clothesCount']) : $input['clothesCount'],
                    $input['totalWage'] ?? 0.0,
                    $input['createdAt'] ?? date('c')
                ]);
                sendResponse(['success' => true]);
            } elseif ($requestMethod === 'DELETE') {
                if (!$id) sendError('Record ID required');
                $stmt = $pdo->prepare("DELETE FROM ironing_records WHERE id = ? AND userId = ?");
                $stmt->execute([$id, $userId]);
                sendResponse(['success' => true]);
            }
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    // Ironing Payments
    case 'ironing-payments':
        try {
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
                if (!$input || empty($input['id']) || empty($input['workerId']) || empty($input['date'])) {
                    sendError('Payment ID, workerId, and date are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO ironing_payments (id, userId, workerId, date, amount, description, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['id'],
                    $userId,
                    $input['workerId'],
                    $input['date'],
                    $input['amount'] ?? 0.0,
                    $input['description'] ?? null,
                    $input['createdAt'] ?? date('c')
                ]);
                sendResponse(['success' => true]);
            } elseif ($requestMethod === 'DELETE') {
                if (!$id) sendError('Payment ID required');
                $stmt = $pdo->prepare("DELETE FROM ironing_payments WHERE id = ? AND userId = ?");
                $stmt->execute([$id, $userId]);
                sendResponse(['success' => true]);
            }
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    // Appliances Hub
    case 'appliances':
        try {
            if ($requestMethod === 'GET') {
                $stmt = $pdo->prepare("SELECT * FROM appliances WHERE userId = ? ORDER BY createdAt DESC");
                $stmt->execute([$userId]);
                sendResponse($stmt->fetchAll());
            } elseif ($requestMethod === 'POST') {
                if (!$input || empty($input['id']) || empty($input['name'])) {
                    sendError('Appliance ID and Name are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO appliances (id, userId, name, type, brand, serialNumber, warrantyStart, warrantyEnd, invoicePath, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
                    $input['createdAt'] ?? date('c')
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
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    // Service Records REST
    case 'service-records':
        try {
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
                if (!$input || empty($input['id']) || empty($input['applianceId']) || empty($input['serviceDate'])) {
                    sendError('Service ID, applianceId, and serviceDate are required');
                }
                $stmt = $pdo->prepare("REPLACE INTO service_records (id, userId, applianceId, serviceDate, price, remarks, billPath, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['id'],
                    $userId,
                    $input['applianceId'],
                    $input['serviceDate'],
                    $input['price'] ?? 0.0,
                    $input['remarks'] ?? null,
                    $input['billPath'] ?? null,
                    $input['createdAt'] ?? date('c')
                ]);
                sendResponse(['success' => true]);
            } elseif ($requestMethod === 'DELETE') {
                if (!$id) sendError('Service record ID required');
                $stmt = $pdo->prepare("DELETE FROM service_records WHERE id = ? AND userId = ?");
                $stmt->execute([$id, $userId]);
                sendResponse(['success' => true]);
            }
        } catch (\PDOException $e) {
            sendError('Database operation failed: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Endpoint not found: ' . $route, 404);
}
