<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // Get JSON input instead of $_POST
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    $database = new Database();
    $db = $database->getConnection();
    
    // Changed 'name' to 'user_name' here
    $user_name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($data['phone'], FILTER_SANITIZE_STRING);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Update validation to use user_name
    if (empty($user_name) || empty($email) || empty($phone)) {
        throw new Exception('All fields are required');
    }

    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $response['message'] = 'Email already registered';
        echo json_encode($response);
        exit;
    }
    
    // Changed 'name' to 'user_name' in the INSERT query
    $stmt = $db->prepare("INSERT INTO users (user_name, email, phone, password) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user_name, $email, $phone, $password])) {
        $response['success'] = true;
        $response['message'] = 'Registration successful';
    } else {
        $response['message'] = 'Registration failed';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);