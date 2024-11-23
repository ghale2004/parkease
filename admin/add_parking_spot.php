<?php
session_start();
require_once('../config/database.php');

// Check admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $name = $_POST['name'];
    $location = $_POST['location'];
    $totalSpots = $_POST['total_spots'];
    $availableSpots = $_POST['total_spots']; // Initially, available spots equals total spots
    $pricePerHour = $_POST['price_per_hour'];
    $rating = 0; // Default rating
    $reviewsCount = 0; // Default reviews count

    $stmt = $db->prepare("INSERT INTO parking_spots (name, location, total_spots, available_spots, price_per_hour, rating, reviews_count) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$name, $location, $totalSpots, $availableSpots, $pricePerHour, $rating, $reviewsCount])) {
        $_SESSION['message'] = "Parking spot added successfully!";
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Failed to add parking spot.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Parking Spot - ParkEase Nepal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
        }

        input, textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #c62828;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Parking Spot</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($_SESSION['message'])) {
            echo "<p class='success'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        } ?>
        <form action="" method="POST">
            <div class="form-group">
                <label>Parking Spot Name</label>
                <input type="text" name="name" required placeholder="Enter parking spot name">
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" required placeholder="Enter location">
            </div>
            <div class="form-group">
                <label>Total Spots</label>
                <input type="number" name="total_spots" required min="1" placeholder="Enter total number of spots">
            </div>
            <div class="form-group">
                <label>Price per Hour (Rs)</label>
                <input type="number" name="price_per_hour" required min="0" step="0.01" placeholder="Enter price per hour">
            </div>
            <button type="submit">Add Parking Spot</button>
        </form>
    </div>
</body>
</html>