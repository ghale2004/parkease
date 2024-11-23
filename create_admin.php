<?php
require_once('config/database.php');

// Admin credentials
$admin_name = "Admin User";
$admin_email = "ghalepaat@gmail.com";
$admin_password = "ghale0110";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Hash the password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Prepare the insert statement - changed 'name' to 'user_name' to match database structure
    $stmt = $db->prepare("INSERT INTO admins (user_name, email, password) VALUES (:user_name, :email, :password)");
    
    // Execute with parameters - updated parameter name to match
    $result = $stmt->execute([
        'user_name' => $admin_name,
        'email' => $admin_email,
        'password' => $hashed_password
    ]);
    
    if ($result) {
        echo "Admin created successfully!";
    } else {
        echo "Failed to create admin.";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>