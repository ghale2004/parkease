<?php
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "nbezprep_parkease", "sujit0110", "nbezprep_parkease");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle booking cancellation
if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get the parking spot ID and check if the booking is upcoming
        $check_stmt = $conn->prepare("
            SELECT parking_spot_id, start_time 
            FROM bookings 
            WHERE id = ? AND user_id = ? AND status = 'confirmed'
            AND start_time > NOW()
        ");
        $check_stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($booking = $result->fetch_assoc()) {
            // Update booking status
            $update_stmt = $conn->prepare("
                UPDATE bookings 
                SET status = 'cancelled' 
                WHERE id = ? AND user_id = ?
            ");
            $update_stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
            $update_stmt->execute();
            
            // Increase available spots
            $spot_stmt = $conn->prepare("
                UPDATE parking_spots 
                SET available_spots = available_spots + 1 
                WHERE id = ?
            ");
            $spot_stmt->bind_param("i", $booking['parking_spot_id']);
            $spot_stmt->execute();
            
            $conn->commit();
            $success_message = "Booking cancelled successfully!";
        } else {
            throw new Exception("Invalid booking or booking cannot be cancelled anymore");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}

// Get user's bookings
$stmt = $conn->prepare("
    SELECT 
        b.*, 
        p.location,
        p.price_per_hour
    FROM bookings b
    JOIN parking_spots p ON b.parking_spot_id = p.id
    WHERE b.user_id = ?
    ORDER BY b.start_time DESC
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - ParkEase Nepal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f5f5f5;
        }

        .main-content {
            flex: 1;
            padding: 100px 0 110px; /* Adjust based on your header/footer height */
            width: 100%;
            position: relative;
        }

        
        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .bookings-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .booking-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
        }
        
        .booking-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-completed {
            background-color: #cfe2ff;
            color: #084298;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .booking-details {
            margin: 15px 0;
        }
        
        .booking-details p {
            margin: 8px 0;
            color: #666;
        }
        
        .booking-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
        }
        
        .btn-cancel:hover {
            background-color: #bb2d3b;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }
        
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .bookings-container {
                padding: 10px;
            }
            
            .bookings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="bookings-container">
            <div class="bookings-header">
                <h1>My Bookings</h1>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($bookings)): ?>
                <div class="no-bookings">
                    <h2>No bookings found</h2>
                    <p>You haven't made any parking bookings yet.</p>
                    <a href="search.php" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Book Now</a>
                </div>
            <?php else: ?>
                <div class="bookings-grid">
                    <?php foreach ($bookings as $booking): ?>
                        <?php
                        $start = new DateTime($booking['start_time']);
                        $end = new DateTime($booking['end_time']);
                        $now = new DateTime();
                        
                        // Determine booking status for display
                        $status_class = 'status-active';
                        if ($booking['status'] === 'cancelled') {
                            $status_class = 'status-cancelled';
                        } elseif ($end < $now) {
                            $status_class = 'status-completed';
                        }
                        ?>
                        <div class="booking-card">
                            <span class="booking-status <?php echo $status_class; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                            
                            <div class="booking-details">
                                <h3><?php echo htmlspecialchars($booking['name']); ?></h3>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['location']); ?></p>
                                <p><i class="fas fa-calendar"></i> <?php echo $start->format('M j, Y'); ?></p>
                                <p><i class="fas fa-clock"></i> <?php echo $start->format('g:i A') . ' - ' . $end->format('g:i A'); ?></p>
                                <p><i class="fas fa-money-bill"></i> Rs. <?php echo number_format($booking['total_price'], 2); ?></p>
                            </div>

                            <?php if ($booking['status'] === 'confirmed' && $start > $now): ?>
                                <div class="booking-actions">
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn-cancel">
                                            <i class="fas fa-times"></i> Cancel Booking
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>