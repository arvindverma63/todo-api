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
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My-Task API Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #009688;
            --primary-dark: #00796b;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            
            --get-color: #3b82f6;
            --post-color: #10b981;
            --put-color: #f59e0b;
            --delete-color: #ef4444;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
            padding: 24px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 24px;
        }
        
        .logo-section h1 {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #009688, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        
        .logo-section p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .server-status {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #10b981;
        }
        
        .base-url-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .base-url-card code {
            font-family: monospace;
            background: rgba(0, 0, 0, 0.2);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 14px;
            color: var(--primary);
            font-weight: 600;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
            margin-top: 28px;
            border-left: 4px solid var(--primary);
            padding-left: 12px;
            letter-spacing: -0.3px;
        }
        
        .endpoint-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .endpoint-header {
            padding: 14px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 16px;
            user-select: none;
        }
        
        .method-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
            min-width: 68px;
            text-align: center;
        }
        
        .method-badge.get { background-color: var(--get-color); }
        .method-badge.post { background-color: var(--post-color); }
        .method-badge.put { background-color: var(--put-color); }
        .method-badge.delete { background-color: var(--delete-color); }
        
        .endpoint-path {
            font-family: monospace;
            font-weight: 600;
            font-size: 14px;
            flex-grow: 1;
        }
        
        .endpoint-desc {
            font-size: 13px;
            color: var(--text-muted);
        }
        
        .endpoint-details {
            display: none;
            padding: 20px;
            border-top: 1px solid var(--border);
            background-color: rgba(15, 23, 42, 0.4);
        }
        
        .endpoint-details.active {
            display: block;
        }
        
        .detail-row {
            margin-bottom: 16px;
        }
        
        .detail-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        pre {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            overflow-x: auto;
            font-family: monospace;
            font-size: 13px;
        }
        
        .testing-section {
            background-color: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            margin-top: 12px;
        }
        
        .testing-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .testing-input-row {
            display: flex;
            gap: 10px;
        }
        
        .testing-input-row input {
            flex-grow: 1;
            background-color: var(--bg-dark);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 10px 14px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 13px;
        }
        
        .testing-input-row input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .btn {
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
        }
        
        .response-box {
            margin-top: 12px;
            max-height: 250px;
            overflow-y: auto;
        }
        
        .response-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            font-size: 12px;
            color: var(--text-muted);
        }
        
        .response-status {
            font-weight: 700;
        }
        
        textarea.body-input {
            width: 100%;
            height: 120px;
            background-color: var(--bg-dark);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 10px 14px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 13px;
            resize: vertical;
        }
        
        textarea.body-input:focus {
            outline: none;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-section">
                <h1>My-Task API Hub</h1>
                <p>Interactive REST API Developer Sandbox & Documentation</p>
            </div>
            <div class="server-status">
                <span class="status-dot"></span>
                <span>SYSTEM ONLINE</span>
            </div>
        </header>

        <div class="base-url-card">
            <div>
                <span style="font-weight: 600; font-size: 14px; margin-right: 8px;">API Base URL:</span>
                <code id="baseUrlCode">http://localhost/api</code>
            </div>
            <span style="font-size: 12px; color: var(--text-muted);">Include <code>X-User-Id</code> header in all scoped requests</span>
        </div>

        <!-- 0. User Authentication -->
        <h2 class="section-title">User Authentication & Guest Sessions</h2>

        <!-- POST /api/register -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/register</span>
                <span class="endpoint-desc">Register a new user account</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "username": "new_user",
  "password": "secure_password"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-register">{
  "username": "test_member_" + Math.floor(Math.random()*1000),
  "password": "Password@123"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/register', 'body-register')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/login -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/login</span>
                <span class="endpoint-desc">Authenticate and login</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-login">{
  "username": "existing_user",
  "password": "Password@123"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/login', 'body-login')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/register-guest -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/register-guest</span>
                <span class="endpoint-desc">Create 30-day guest user account</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <button class="btn" onclick="testRequest('POST', '/api/register-guest')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/convert-guest -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/convert-guest</span>
                <span class="endpoint-desc">Upgrade guest account to fully registered member</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "userId": "guest_user_id_here",
  "username": "chosen_new_username",
  "password": "chosen_new_password"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-convert-guest">{
  "userId": "replace_with_active_guest_id",
  "username": "new_converted_user",
  "password": "Password@123"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/convert-guest', 'body-convert-guest')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/login-google -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/login-google</span>
                <span class="endpoint-desc">Authenticate/Provision account using Google OAuth ID</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "googleId": "google_oauth_sub_id",
  "email": "user@gmail.com",
  "displayName": "User Name"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-login-google">{
  "googleId": "1000343378364",
  "email": "tester@gmail.com",
  "displayName": "API Sandbox Tester"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/login-google', 'body-login-google')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Attendance & Helpers -->
        <h2 class="section-title">Helper Attendance Management</h2>
        
        <!-- GET /api/employees -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/employees</span>
                <span class="endpoint-desc">List all house helpers / employees</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">Add User ID Header:</div>
                        <input type="text" id="header-employees-get" placeholder="X-User-Id Header Value" style="background:var(--bg-dark); border:1px solid var(--border); color:#fff; padding:10px; border-radius:10px; margin-bottom:10px;">
                        <button class="btn" onclick="testRequestWithHeader('GET', '/api/employees', null, 'header-employees-get')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Response Overlay Dialog/Panel -->
        <div style="margin-top: 48px; border-top: 1px solid var(--border); padding-top: 24px; display: none;" id="liveResponsePanel">
            <h3 style="font-size: 16px; margin-bottom: 8px; color: var(--primary);">Live Sandbox Response Console</h3>
            <div class="response-header">
                <span>Request Endpoint: <code id="consoleRequestUrl">/api</code></span>
                <span>HTTP Code: <span id="consoleResponseStatus" class="response-status">200 OK</span></span>
            </div>
            <pre class="response-box"><code id="consoleResponseCode">{}</code></pre>
        </div>
    </div>

    <script>
        const origin = window.location.origin;
        document.getElementById('baseUrlCode').innerText = origin + '/api';

        function toggleDetails(header) {
            const card = header.parentElement;
            const details = card.querySelector('.endpoint-details');
            details.classList.toggle('active');
        }

        function showConsole(url, status, statusText, data) {
            document.getElementById('liveResponsePanel').style.display = 'block';
            document.getElementById('consoleRequestUrl').innerText = url;
            document.getElementById('consoleResponseStatus').innerText = status + ' ' + statusText;
            
            const codeEl = document.getElementById('consoleResponseCode');
            codeEl.innerText = JSON.stringify(data, null, 2);
            document.getElementById('liveResponsePanel').scrollIntoView({ behavior: 'smooth' });
        }

        async function makeRequest(method, path, body = null, userIdHeader = null) {
            const url = window.location.origin + path;
            const headers = { 'Content-Type': 'application/json' };
            if (userIdHeader) {
                headers['X-User-Id'] = userIdHeader;
            }
            const options = { method, headers };
            if (body) {
                options.body = typeof body === 'string' ? body : JSON.stringify(body);
            }

            try {
                const response = await fetch(url, options);
                const data = await response.json();
                showConsole(path, response.status, response.statusText, data);
            } catch (err) {
                showConsole(path, 0, 'Connection Refused', { error: err.message });
            }
        }

        function testRequest(method, path, bodyTextareaId = null) {
            let body = null;
            if (bodyTextareaId) {
                const rawText = document.getElementById(bodyTextareaId).value;
                try {
                    body = eval('(' + rawText + ')');
                } catch(e) {
                    body = JSON.parse(rawText);
                }
            }
            makeRequest(method, path, body);
        }

        function testRequestWithHeader(method, path, bodyTextareaId, headerInputId) {
            const headerVal = document.getElementById(headerInputId).value.trim();
            if (!headerVal) {
                alert('Please provide an X-User-Id for scoped endpoints');
                return;
            }
            makeRequest(method, path, null, headerVal);
        }
    </script>
</body>
</html>
    <?php
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
    
    $stmt = $pdo->prepare("INSERT INTO users (id, username, password, userType, createdAt, expiresAt) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $input['username'],
        $hashedPassword,
        'registered',
        $createdAt,
        null
    ]);
    
    sendResponse([
        'success' => true,
        'userId' => $userId,
        'username' => $input['username'],
        'userType' => 'registered',
        'expiresAt' => null,
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
        $stmt = $pdo->prepare("INSERT INTO users (id, username, password, userType, createdAt, expiresAt) VALUES (?, ?, NULL, ?, ?, NULL)");
        $stmt->execute([
            $googleUserId,
            $input['email'],
            'registered',
            $createdAt
        ]);
        
        sendResponse([
            'success' => true,
            'userId' => $googleUserId,
            'username' => $input['email'],
            'userType' => 'registered',
            'expiresAt' => null,
            'profilePic' => null
        ]);
    }
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
