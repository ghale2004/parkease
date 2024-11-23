<?php
session_start();
require_once('../config/database.php');

// Check admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Initialize variables
$spot = null;
$error = '';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = "No parking spot ID provided.";
}

// Fetch parking spot details
if (empty($error)) {
    try {
        $stmt = $db->prepare("SELECT * FROM parking_spots WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spot) {
            $error = "Parking spot not found.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error)) {
    try {
        $name = $_POST['name'];
        $location = $_POST['location'];
        $totalSpots = $_POST['total_spots'];
        $pricePerHour = $_POST['price_per_hour'];
        $description = $_POST['description'];

        // Validate that new total spots is not less than booked spots
        $checkStmt = $db->prepare("SELECT COUNT(*) as booked_spots FROM bookings WHERE parking_spot_id = ? AND status = 'confirmed'");
        $checkStmt->execute([$_GET['id']]);
        $bookedSpots = $checkStmt->fetch(PDO::FETCH_ASSOC)['booked_spots'];

        if ($totalSpots < $bookedSpots) {
            $error = "Total spots cannot be less than current bookings.";
        } else {
            // Update parking spot
            $updateStmt = $db->prepare("UPDATE parking_spots SET 
                name = ?, 
                location = ?, 
                total_spots = ?, 
                available_spots = total_spots - ?, 
                price_per_hour = ?, 
                description = ? 
                WHERE id = ?");
            
            if ($updateStmt->execute([
                $name, 
                $location, 
                $totalSpots, 
                $bookedSpots, 
                $pricePerHour, 
                $description, 
                $_GET['id']
            ])) {
                $_SESSION['message'] = "Parking spot updated successfully!";
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Failed to update parking spot.";
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Parking Spot - ParkEase Nepal</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Parking Spot</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($spot): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Parking Spot Name</label>
                    <input type="text" name="name" 
                           value="<?php echo htmlspecialchars($spot['name']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" 
                           value="<?php echo htmlspecialchars($spot['location']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Total Spots</label>
                    <input type="number" name="total_spots" 
                           value="<?php echo intval($spot['total_spots']); ?>" 
                           required min="1">
                </div>

                <div class="form-group">
                    <label>Price per Hour (Rs)</label>
                    <input type="number" name="price_per_hour" 
                           value="<?php echo number_format($spot['price_per_hour'], 2, '.', ''); ?>" 
                           required step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?php 
                        echo htmlspecialchars($spot['description']); 
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label>Current Available Spots</label>
                    <input type="text" 
                           value="<?php echo intval($spot['available_spots']); ?>" 
                           readonly>
                </div>

                <button type="submit" class="btn">Update Parking Spot</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>