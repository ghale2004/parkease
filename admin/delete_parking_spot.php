<?php
session_start();
require_once('../config/database.php');

// Check admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid parking spot ID.";
    header('Location: dashboard.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    // First check if the parking spot exists and if it has any active bookings
    $checkBookings = $db->prepare("SELECT COUNT(*) FROM bookings WHERE parking_spot_id = ? AND status = 'confirmed'");
    $checkBookings->execute([$_GET['id']]);
    $activeBookings = $checkBookings->fetchColumn();

    if ($activeBookings > 0) {
        $_SESSION['error'] = "Cannot delete parking spot. There are active bookings for this location.";
        header('Location: dashboard.php');
        exit();
    }

    // Check if the parking spot exists
    $checkSpot = $db->prepare("SELECT id FROM parking_spots WHERE id = ?");
    $checkSpot->execute([$_GET['id']]);
    
    if (!$checkSpot->fetch()) {
        $_SESSION['error'] = "Parking spot not found.";
        header('Location: dashboard.php');
        exit();
    }

    // Proceed with deletion
    $stmt = $db->prepare("DELETE FROM parking_spots WHERE id = ?");
    
    if ($stmt->execute([$_GET['id']])) {
        $_SESSION['success'] = "Parking spot deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete parking spot.";
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: Unable to delete parking spot.";
    // You might want to log $e->getMessage() for debugging
}

// Redirect back to dashboard
header('Location: dashboard.php');
exit();
?>