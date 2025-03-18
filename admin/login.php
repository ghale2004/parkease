<?php
// admin/login.php
session_start();
require_once('../config/database.php');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function for logging errors
function logError($message) {
    error_log("[ADMIN LOGIN] " . $message);
}

// Validate Content-Type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') === false) {
    http_response_code(415);
    echo json_encode([
        'success' => false,
        'message' => 'Unsupported Content-Type. Must be application/json'
    ]);
    exit;
}

// Capture raw input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data',
        'error' => json_last_error_msg()
    ]);
    exit;
}

// Validate required fields
if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email format'
        ]);
        exit;
    }

    $password = $data['password'];
    
    // Check admin credentials in the admins table
    $stmt = $db->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin || !password_verify($password, $admin['password'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid admin credentials'
        ]);
        exit;
    }

    // Successful admin login
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_name'] = $admin['name'] ?? 'Admin';
    $_SESSION['user_role'] = 'admin';  // Set role as admin

    echo json_encode([
        'success' => true,
        'message' => 'Admin login successful',
        'redirect' => '/index.php'
        // 'redirect' => '../demopopup/admin/dashboard.php'
    ]);

} catch (PDOException $e) {
    logError("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error'
    ]);
} catch (Exception $e) {
    logError("Unexpected Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected error occurred'
    ]);
}