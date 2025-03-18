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
                price_per_hour = ? 
                WHERE id = ?");
                
                
            
            if ($updateStmt->execute([
                $name, 
                $location, 
                $totalSpots, 
                $bookedSpots, 
                $pricePerHour,
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
    <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', system-ui, sans-serif;
}

body {
    background: #f0f2f5;
    color: #1a1a1a;
    line-height: 1.6;
}

.container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

h2 {
    color: #2c3e50;
    margin-bottom: 2rem;
    font-size: 2rem;
    font-weight: 600;
    text-align: center;
    position: relative;
    padding-bottom: 1rem;
}

h2:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: #3498db;
    border-radius: 2px;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-weight: 500;
    font-size: 0.95rem;
}

input, textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8fafc;
}

input:focus, textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
    background: white;
}

input[readonly] {
    background: #edf2f7;
    border-color: #cbd5e0;
    cursor: not-allowed;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

button.btn {
    background: #3498db;
    color: white;
    width: 100%;
    margin-bottom: 1rem;
}

button.btn:hover {
    background: #2980b9;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
    text-align: center;
    display: block;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.error-message {
    background: #fee2e2;
    color: #dc2626;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #dc2626;
    font-size: 0.95rem;
}

@media (min-width: 640px) {
    .form-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .form-group:last-of-type {
        grid-template-columns: 1fr;
    }
}
</style>
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

                <!-- <div class="form-group">
                     <label>Description</label>
                     <textarea name="description"><?php 
                         echo htmlspecialchars($spot['description']); 
                     ?></textarea>
                 </div> -->

                <!-- <div class="form-group">
                    <label>Current Available Spots</label>
                    <input type="text" 
                           value="<?php echo intval($spot['available_spots']); ?>" 
                           readonly>
                </div> -->

                <button type="submit" class="btn">Update Parking Spot</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>