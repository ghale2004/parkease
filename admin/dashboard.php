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

// Fetch parking spots
$parkingStmt = $db->query("SELECT * FROM parking_spots");
$parkingSpots = $parkingStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user count
$userStmt = $db->query("SELECT COUNT(*) as user_count FROM users");
$userCount = $userStmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// Fetch booking count
$bookingStmt = $db->query("SELECT COUNT(*) as booking_count FROM bookings");
$bookingCount = $bookingStmt->fetch(PDO::FETCH_ASSOC)['booking_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - ParkEase Nepal</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    /* Admin Navigation Styles */
    .admin-nav {
        background-color: #333;
        padding: 1rem 0;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }

    .logo h1 {
        color: #fff;
        font-size: 1.5rem;
        margin: 0;
    }

    .nav-links {
        display: flex;
        align-items: center;
    }

    .nav-links a {
        color: #fff;
        text-decoration: none;
        margin: 0 15px;
        padding: 8px 15px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .nav-links a:hover {
        background-color: #444;
    }

    .admin-logout {
        background-color: #ffd700;
        color: #333 !important;
        padding: 8px 15px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .admin-logout:hover {
        background-color: #e6c200;
    }

    /* Rest of your existing dashboard styles */
    .admin-dashboard {
        padding: 20px;
        max-width: 1200px;
        margin: 80px auto 0;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .stat-card h3 {
        color: #666;
        margin-bottom: 10px;
    }

    .stat-card p {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .parking-management {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #ffd700;
        color: #333;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #e6c200;
    }

    td a {
        display: inline-block;
        padding: 5px 10px;
        margin: 0 5px;
        background-color: #ffd700;
        color: #333;
        text-decoration: none;
        border-radius: 3px;
        font-size: 14px;
    }

    td a:hover {
        background-color: #e6c200;
    }

    td a[href*="delete"] {
        background-color: #ff4444;
        color: white;
    }

    td a[href*="delete"]:hover {
        background-color: #cc0000;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
    /* Your existing styles remain the same */
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="logo">
                <h1>ParkEase Nepal - Admin</h1>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Main Site</a>
                <!-- <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a> -->
                <!-- <a href="manage-users.php"><i class="fas fa-users"></i> Users</a> -->
                <!-- <a href="manage-parking.php"><i class="fas fa-parking"></i> Parking</a> -->
                <!-- <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a> -->
                <a href="logout.php" class="admin-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <!-- <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>../index.php" class="admin-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div> -->
        </div>
    </nav>

    <!-- Rest of your dashboard content remains the same -->
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Parking Spots</h3>
                <p><?php echo count($parkingSpots); ?></p>
            </div>
            <div class="stat-card">
                <a href= "view_users.php" style="text-decoration: none; color: inherit;"
>                <h3>Total Users</h3>
                <p><?php echo $userCount; ?></p>
                </a>
            </div>
            <div class="stat-card">
            <a href = "total_booking.php" style = "text-decoration: none; color: inherit;"
                <h3>Total Bookings</h3>
                <p><?php echo $bookingCount; ?></p> </a>
            </div>
        </div>
        <div class="parking-management">
            <h2>Parking Spots Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Total Spots</th>
                        <th>Price/Hour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($parkingSpots as $spot): ?>
                    <tr>
                        <td><?php echo $spot['id']; ?></td>
                        <td><?php echo $spot['name']; ?></td>
                        <td><?php echo $spot['location']; ?></td>
                        <td><?php echo $spot['total_spots']; ?></td>
                        <td>Rs. <?php echo $spot['price_per_hour']; ?></td>
                        <td>
                            <a href="edit_parking_spot.php?id=<?php echo $spot['id']; ?>">Edit</a>
                            <a href="delete_parking_spot.php?id=<?php echo $spot['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="add_parking_spot.php" class="btn">Add New Parking Spot</a>
        </div>

        <!-- <div class="user-management">
            <h2>User Management</h2>
            // User management functionality can be added here 
        </div> -->

        <!-- Rest of your existing dashboard content -->
    </div>
</body>
</html>