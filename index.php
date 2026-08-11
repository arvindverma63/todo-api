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
