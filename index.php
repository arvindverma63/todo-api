<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

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
// Support routes starting with /api/ or simply the endpoint (e.g., if run via base URL /todo-api/index.php/api/...)
$apiIndex = array_search('api', $parts);
if ($apiIndex === false) {
    // If '/api' is not in path, assume the path elements start after base directory
    // We'll normalize so the route corresponds to endpoint
    $route = isset($parts[0]) ? $parts[0] : '';
    $id = isset($parts[1]) ? $parts[1] : null;
    $subRoute = isset($parts[2]) ? $parts[2] : null;
} else {
    $route = isset($parts[$apiIndex + 1]) ? $parts[$apiIndex + 1] : '';
    $id = isset($parts[$apiIndex + 2]) ? $parts[$apiIndex + 2] : null;
    $subRoute = isset($parts[$apiIndex + 3]) ? $parts[$apiIndex + 3] : null;
}

// -------------------------------------------------------------
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

// -------------------------------------------------------------
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
        
        .file-upload-input {
            background-color: var(--bg-dark);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            width: 100%;
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
            <span style="font-size: 12px; color: var(--text-muted);">JSON payloads, active CORS filter</span>
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
                <div class="detail-row">
                    <div class="detail-title">Response Example (200 OK)</div>
                    <pre>[
  {
    "id": "demo_emp_1",
    "name": "Ramesh Kumar",
    "contact": "9876543210",
    "joiningDate": "2026-06-27",
    "baseSalary": 450.00,
    "salaryBasis": "daily"
  }
]</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <button class="btn" onclick="testRequest('GET', '/api/employees')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/employees -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/employees</span>
                <span class="endpoint-desc">Register a new helper</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "id": "unique_string_id",
  "name": "Sita Devi",
  "contact": "9999888877",
  "joiningDate": "2026-08-11",
  "baseSalary": 12000.00,
  "salaryBasis": "monthly"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-employees-post">{
  "id": "emp_test_" + Date.now(),
  "name": "Test Helper Name",
  "contact": "9999000011",
  "joiningDate": "2026-08-11",
  "baseSalary": 8500.00,
  "salaryBasis": "monthly"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/employees', 'body-employees-post')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DELETE /api/employees/{id} -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge delete">DELETE</span>
                <span class="endpoint-path">/api/employees/{id}</span>
                <span class="endpoint-desc">Remove a helper and clean logs</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <div class="testing-input-row">
                            <input type="text" id="param-employees-delete" placeholder="Employee ID (e.g. demo_emp_1)">
                        </div>
                        <button class="btn" onclick="testRequestPath('DELETE', '/api/employees/', 'param-employees-delete')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- GET /api/attendance -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/attendance</span>
                <span class="endpoint-desc">Fetch attendance records (optionally filter ?employeeId=X)</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <div class="testing-input-row">
                            <input type="text" id="query-attendance-get" placeholder="Employee ID Filter (Optional)">
                        </div>
                        <button class="btn" onclick="testRequestQuery('GET', '/api/attendance', 'query-attendance-get', 'employeeId')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/attendance -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/attendance</span>
                <span class="endpoint-desc">Log attendance entry or payment advance</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "id": "unique_string_id",
  "employeeId": "demo_emp_1",
  "date": "2026-08-11",
  "status": "present",
  "checkInTime": "09:00 AM",
  "checkOutTime": "06:00 PM",
  "amountGiven": 0.00,
  "paymentDescription": ""
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-attendance-post">{
  "id": "att_test_" + Date.now(),
  "employeeId": "demo_emp_1",
  "date": "2026-08-11",
  "status": "present",
  "checkInTime": "10:00 AM",
  "checkOutTime": "07:00 PM",
  "amountGiven": 100.00,
  "paymentDescription": "Lunch allowance"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/attendance', 'body-attendance-post')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Ironing Wages & Log -->
        <h2 class="section-title">Ironing Wages & records</h2>
        
        <!-- GET /api/ironing-workers -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/ironing-workers</span>
                <span class="endpoint-desc">List all ironing workers</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <button class="btn" onclick="testRequest('GET', '/api/ironing-workers')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- GET /api/ironing-workers/{id}/rates -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/ironing-workers/{id}/rates</span>
                <span class="endpoint-desc">Get cloth-specific rates for a worker</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <div class="testing-input-row">
                            <input type="text" id="param-worker-rates" placeholder="Worker ID (e.g. demo_worker_1)">
                        </div>
                        <button class="btn" onclick="testRequestPath('GET', '/api/ironing-workers/', 'param-worker-rates', '/rates')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/ironing-workers/{id}/rates -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/ironing-workers/{id}/rates</span>
                <span class="endpoint-desc">Add or update rate details for a worker</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "id": "rate_shirt",
  "clothingType": "Shirt",
  "rate": 6.50,
  "date": "2026-08-11"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <div class="testing-input-row">
                            <input type="text" id="param-worker-rates-post" placeholder="Worker ID (e.g. demo_worker_1)">
                        </div>
                        <textarea class="body-input" id="body-worker-rates-post">{
  "id": "rate_shirt_test",
  "clothingType": "Shirt",
  "rate": 8.00,
  "date": "2026-08-11"
}</textarea>
                        <button class="btn" onclick="testRequestPathWithBody('POST', '/api/ironing-workers/', 'param-worker-rates-post', '/rates', 'body-worker-rates-post')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Appliances & Warranty -->
        <h2 class="section-title">Appliance Repairs & Hub</h2>
        
        <!-- GET /api/appliances -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/appliances</span>
                <span class="endpoint-desc">List all appliances and warranties</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <button class="btn" onclick="testRequest('GET', '/api/appliances')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/appliances -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/appliances</span>
                <span class="endpoint-desc">Register a new home appliance</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "id": "unique_string_id",
  "name": "Sony Bravia Smart TV",
  "type": "Television",
  "brand": "Sony",
  "serialNumber": "SN-TV-29384",
  "warrantyStart": "2026-01-10",
  "warrantyEnd": "2027-01-10",
  "invoicePath": "uploads/receipt_demo.jpg",
  "createdAt": "2026-08-11"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-appliances-post">{
  "id": "app_test_" + Date.now(),
  "name": "Microwave Oven",
  "type": "Kitchen Appliance",
  "brand": "LG",
  "serialNumber": "LG-MW-9080",
  "warrantyStart": "2026-08-11",
  "warrantyEnd": "2028-08-11",
  "invoicePath": null,
  "createdAt": "2026-08-11"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/appliances', 'body-appliances-post')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- GET /api/service-records -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/service-records</span>
                <span class="endpoint-desc">Get repair logs timeline (optionally filter ?applianceId=X)</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <div class="testing-input-row">
                            <input type="text" id="query-service-get" placeholder="Appliance ID Filter (Optional)">
                        </div>
                        <button class="btn" onclick="testRequestQuery('GET', '/api/service-records', 'query-service-get', 'applianceId')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/service-records -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/service-records</span>
                <span class="endpoint-desc">Add a service repair log</span>
            </div>
            <div class="endpoint-details">
                <div class="detail-row">
                    <div class="detail-title">Request Body Template</div>
                    <pre>{
  "id": "unique_string_id",
  "applianceId": "demo_app_1",
  "serviceDate": "2026-08-11",
  "price": 2500.00,
  "remarks": "Display panel logic board replacement",
  "billPath": "uploads/bill_demo.jpg",
  "createdAt": "2026-08-11"
}</pre>
                </div>
                <div class="testing-section">
                    <div class="detail-title">Test Endpoint</div>
                    <div class="testing-form">
                        <textarea class="body-input" id="body-service-post">{
  "id": "srv_test_" + Date.now(),
  "applianceId": "demo_app_1",
  "serviceDate": "2026-08-11",
  "price": 1200.00,
  "remarks": "Regular maintenance and checkup",
  "billPath": null,
  "createdAt": "2026-08-11"
}</textarea>
                        <button class="btn" onclick="testRequest('POST', '/api/service-records', 'body-service-post')">Send Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Static Uploads Router -->
        <h2 class="section-title">Static File Uploads API</h2>
        
        <!-- POST /api/upload -->
        <div class="endpoint-card">
            <div class="endpoint-header" onclick="toggleDetails(this)">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/upload</span>
                <span class="endpoint-desc">Upload invoice or bill image</span>
            </div>
            <div class="endpoint-details">
                <div class="testing-section">
                    <div class="detail-title">Upload Test Image</div>
                    <div class="testing-form">
                        <input type="file" id="file-upload-el" class="file-upload-input">
                        <button class="btn" onclick="testFileUpload('file-upload-el')">Upload File</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Response Console -->
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
        // Set dynamic base URL display
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
            
            // Auto scroll to console
            document.getElementById('liveResponsePanel').scrollIntoView({ behavior: 'smooth' });
        }

        async function makeRequest(method, path, body = null) {
            const origin = window.location.origin;
            const url = origin + path;
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };
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

        function testRequestPath(method, basePath, paramInputId, suffix = '') {
            const val = document.getElementById(paramInputId).value.trim();
            if (!val) {
                alert('Please enter a valid URL parameter');
                return;
            }
            const path = basePath + encodeURIComponent(val) + suffix;
            makeRequest(method, path);
        }

        function testRequestPathWithBody(method, basePath, paramInputId, suffix = '', bodyTextareaId) {
            const val = document.getElementById(paramInputId).value.trim();
            if (!val) {
                alert('Please enter a valid URL parameter');
                return;
            }
            const path = basePath + encodeURIComponent(val) + suffix;
            
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

        function testRequestQuery(method, basePath, queryInputId, paramName) {
            const val = document.getElementById(queryInputId).value.trim();
            const path = val ? `${basePath}?${paramName}=${encodeURIComponent(val)}` : basePath;
            makeRequest(method, path);
        }

        async function testFileUpload(fileInputId) {
            const input = document.getElementById(fileInputId);
            if (!input.files || input.files.length === 0) {
                alert('Please select a file to upload first');
                return;
            }
            const file = input.files[0];
            const formData = new FormData();
            formData.append('file', file);
            
            const origin = window.location.origin;
            const path = '/api/upload';
            const url = origin + path;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                showConsole(path, response.status, response.statusText, data);
            } catch (err) {
                showConsole(path, 0, 'Connection Refused', { error: err.message });
            }
        }
    </script>
</body>
</html>
    <?php
    exit;
}

// Route Switcher
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
            // Return target path for client to store
            sendResponse([
                'success' => true,
                'path' => $targetPath
            ]);
        } else {
            sendError('Failed to save uploaded file', 500);
        }
        break;

    // Employees REST
    case 'employees':
        if ($requestMethod === 'GET') {
            $stmt = $pdo->query("SELECT * FROM employees");
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO employees (id, name, contact, joiningDate, baseSalary, salaryBasis) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $input['name'],
                $input['contact'] ?? null,
                $input['joiningDate'] ?? null,
                $input['baseSalary'] ?? 0.0,
                $input['salaryBasis'] ?? 'monthly'
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'PUT') {
            if (!$id) sendError('Employee ID required');
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("UPDATE employees SET name = ?, contact = ?, joiningDate = ?, baseSalary = ?, salaryBasis = ? WHERE id = ?");
            $stmt->execute([
                $input['name'],
                $input['contact'] ?? null,
                $input['joiningDate'] ?? null,
                $input['baseSalary'] ?? 0.0,
                $input['salaryBasis'] ?? 'monthly',
                $id
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Employee ID required');
            $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            // Also clean corresponding attendance
            $stmtAtt = $pdo->prepare("DELETE FROM attendance WHERE employeeId = ?");
            $stmtAtt->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    // Attendance REST
    case 'attendance':
        if ($requestMethod === 'GET') {
            $employeeId = $_GET['employeeId'] ?? null;
            if ($employeeId) {
                $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employeeId = ? ORDER BY date DESC");
                $stmt->execute([$employeeId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM attendance ORDER BY date DESC");
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO attendance (id, employeeId, date, status, checkInTime, checkOutTime, amountGiven, paymentDescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
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
            $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = ?");
            $stmt->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    // Ironing Workers REST & Subroutes
    case 'ironing-workers':
        // Handle worker rates: /api/ironing-workers/{id}/rates
        if ($id && $subRoute === 'rates') {
            if ($requestMethod === 'GET') {
                $stmt = $pdo->prepare("SELECT * FROM ironing_rates WHERE workerId = ?");
                $stmt->execute([$id]);
                sendResponse($stmt->fetchAll());
            } elseif ($requestMethod === 'POST') {
                if (!$input) sendError('Invalid JSON input');
                // Use REPLACE to support upsert behavior
                $stmt = $pdo->prepare("REPLACE INTO ironing_rates (id, workerId, clothingType, rate, date) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['id'],
                    $id,
                    $input['clothingType'],
                    $input['rate'],
                    $input['date'] ?? null
                ]);
                sendResponse(['success' => true]);
            }
            break;
        }

        // Standard ironing workers REST
        if ($requestMethod === 'GET') {
            $stmt = $pdo->query("SELECT * FROM ironing_workers");
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO ironing_workers (id, name, contact, joiningDate) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $input['name'],
                $input['contact'] ?? null,
                $input['joiningDate'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Worker ID required');
            $stmt = $pdo->prepare("DELETE FROM ironing_workers WHERE id = ?");
            $stmt->execute([$id]);
            // Clean worker records, rates, and payments
            $pdo->prepare("DELETE FROM ironing_rates WHERE workerId = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM ironing_records WHERE workerId = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM ironing_payments WHERE workerId = ?")->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    // Ironing Records
    case 'ironing-records':
        if ($requestMethod === 'GET') {
            $workerId = $_GET['workerId'] ?? null;
            if ($workerId) {
                $stmt = $pdo->prepare("SELECT * FROM ironing_records WHERE workerId = ? ORDER BY date DESC");
                $stmt->execute([$workerId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM ironing_records ORDER BY date DESC");
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO ironing_records (id, workerId, date, clothesCount, totalWage, createdAt) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $input['workerId'],
                $input['date'],
                is_array($input['clothesCount']) ? json_encode($input['clothesCount']) : $input['clothesCount'],
                $input['totalWage'],
                $input['createdAt'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Record ID required');
            $stmt = $pdo->prepare("DELETE FROM ironing_records WHERE id = ?");
            $stmt->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    // Ironing Payments
    case 'ironing-payments':
        if ($requestMethod === 'GET') {
            $workerId = $_GET['workerId'] ?? null;
            if ($workerId) {
                $stmt = $pdo->prepare("SELECT * FROM ironing_payments WHERE workerId = ? ORDER BY date DESC");
                $stmt->execute([$workerId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM ironing_payments ORDER BY date DESC");
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO ironing_payments (id, workerId, date, amount, description, createdAt) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
                $input['workerId'],
                $input['date'],
                $input['amount'],
                $input['description'] ?? null,
                $input['createdAt'] ?? null
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Payment ID required');
            $stmt = $pdo->prepare("DELETE FROM ironing_payments WHERE id = ?");
            $stmt->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    // Appliances Hub
    case 'appliances':
        if ($requestMethod === 'GET') {
            $stmt = $pdo->query("SELECT * FROM appliances ORDER BY createdAt DESC");
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO appliances (id, name, type, brand, serialNumber, warrantyStart, warrantyEnd, invoicePath, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
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
            $stmt = $pdo->prepare("UPDATE appliances SET name = ?, type = ?, brand = ?, serialNumber = ?, warrantyStart = ?, warrantyEnd = ?, invoicePath = ?, createdAt = ? WHERE id = ?");
            $stmt->execute([
                $input['name'],
                $input['type'] ?? null,
                $input['brand'] ?? null,
                $input['serialNumber'] ?? null,
                $input['warrantyStart'] ?? null,
                $input['warrantyEnd'] ?? null,
                $input['invoicePath'] ?? null,
                $input['createdAt'] ?? null,
                $id
            ]);
            sendResponse(['success' => true]);
        } elseif ($requestMethod === 'DELETE') {
            if (!$id) sendError('Appliance ID required');
            $stmt = $pdo->prepare("DELETE FROM appliances WHERE id = ?");
            $stmt->execute([$id]);
            // Clean service records for this appliance
            $pdo->prepare("DELETE FROM service_records WHERE applianceId = ?")->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    // Service Records REST
    case 'service-records':
        if ($requestMethod === 'GET') {
            $applianceId = $_GET['applianceId'] ?? null;
            if ($applianceId) {
                $stmt = $pdo->prepare("SELECT * FROM service_records WHERE applianceId = ? ORDER BY serviceDate DESC");
                $stmt->execute([$applianceId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM service_records ORDER BY serviceDate DESC");
            }
            sendResponse($stmt->fetchAll());
        } elseif ($requestMethod === 'POST') {
            if (!$input) sendError('Invalid JSON input');
            $stmt = $pdo->prepare("INSERT INTO service_records (id, applianceId, serviceDate, price, remarks, billPath, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['id'],
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
            $stmt = $pdo->prepare("DELETE FROM service_records WHERE id = ?");
            $stmt->execute([$id]);
            sendResponse(['success' => true]);
        }
        break;

    default:
        sendError('Endpoint not found: ' . $route, 404);
}
