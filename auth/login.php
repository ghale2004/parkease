<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    $database = new Database();
    $db = $database->getConnection();
    
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $password = $data['password'];
    
    // First check if it's an admin
    $adminStmt = $db->prepare("SELECT id, user_name, email, password, role FROM admins WHERE email = ?");
    $adminStmt->execute([$email]);
    $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        // Admin login
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['user_name'];
        $_SESSION['user_email'] = $admin['email'];
        $_SESSION['user_role'] = $admin['role'];
        
        $response['success'] = true;
        $response['message'] = 'Admin login successful';
    } else {
        // Check regular user if admin login fails
        $userStmt = $db->prepare("SELECT id, user_name, email, password, role FROM users WHERE email = ?");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Regular user login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            $response['success'] = true;
            $response['message'] = 'Login successful';
        } else {
            $response['message'] = 'Invalid email or password';
        }
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);