<?php
session_start();
require_once('auth.php');
require_once('../config/database.php');
checkAdminAuth();

$database = new Database();
$db = $database->getConnection();

// Handle Delete Operation
if(isset($_POST['delete']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $db->prepare("DELETE FROM parking_spots WHERE id = ?");
    try {
        $stmt->execute([$id]);
        $message = "Parking spot deleted successfully";
    } catch(PDOException $e) {
        $error = "Failed to delete parking spot";
    }
}

// Fetch Parking Spots
$stmt = $db->query("SELECT * FROM parking_spots ORDER BY id ASC");
$spots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking Spots Management - ParkEase Nepal</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .spots-container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
        }
        .spots-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .spots-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #333;
        }
        .spots-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .spots-table tr:hover {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
        }
        .btn-edit {
            background: #ffd700;
            color: #333;
        }
        .btn-delete {
            background: #ff4444;
            color: white;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background: #4CAF50;
            color: white;
        }
        .error {
            background: #ff4444;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 15% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
        }
        .rating-stars {
            color: #ffd700;
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

    <div class="spots-container">
        <?php if(isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h2>Parking Spots Management</h2>
        <table class="spots-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Total Spots</th>
                    <th>Available Spots</th>
                    <th>Price/Hour</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($spots as $spot): ?>
                <tr>
                    <td><?php echo htmlspecialchars($spot['id']); ?></td>
                    <td><?php echo htmlspecialchars($spot['name']); ?></td>
                    <td><?php echo htmlspecialchars($spot['location']); ?></td>
                    <td><?php echo htmlspecialchars($spot['total_spots']); ?></td>
                    <td><?php echo htmlspecialchars($spot['available_spots']); ?></td>
                    <td>Rs. <?php echo htmlspecialchars($spot['price_per_hour']); ?></td>
                    <td>
                        <div class="rating-stars">
                            <?php 
                            $rating = $spot['rating'];
                            for($i = 0; $i < 5; $i++) {
                                if($i < $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $spot['id']; ?>">
                            <button type="button" class="btn btn-edit" 
                                    onclick="window.location.href='edit_parking_spot.php?id=<?php echo $spot['id']; ?>'">
                                Edit
                            </button>
                            <button type="submit" name="delete" class="btn btn-delete" 
                                    onclick="return confirm('Are you sure you want to delete this parking spot?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>