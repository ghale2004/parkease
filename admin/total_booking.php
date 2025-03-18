<?php
session_start();

require_once('auth.php');
require_once('../config/database.php');
checkAdminAuth();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT b.*, u.user_name FROM bookings b 
                    JOIN users u ON b.user_id = u.id 
                    ORDER BY b.created_at DESC");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bookings - ParkEase Nepal</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-dashboard {
            padding: 20px;
            max-width: 1200px;
            margin: 80px auto 0;
        }

        .booking-table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .booking-table th,
        .booking-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .booking-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }

        .booking-table tr:hover {
            background-color: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-confirmed {
            background-color: #4CAF50;
            color: white;
        }

        .status-pending {
            background-color: #FFA500;
            color: white;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ffd700;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #e6c200;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="logo">
                <h1>ParkEase Nepal - Admin</h1>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Main Site</a>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="logout.php" class="admin-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-dashboard">
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <div class="booking-table-container">
            <h2>Booking Management</h2>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Parking Name</th>
                        <th>Spot ID</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['parking_spot_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($booking['end_time']); ?></td>
                        <td>Rs. <?php echo htmlspecialchars($booking['total_price']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>