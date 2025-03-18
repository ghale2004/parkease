<?php
//session_start(); // Start session at the beginning
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "nbezprep_parkease", "sujit0110", "nbezprep_parkease");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parking spot details
$parking_id = isset($_GET['parking_id']) ? intval($_GET['parking_id']) : 0;

// Add error checking for prepare statement
$stmt = $conn->prepare("SELECT * FROM parking_spots WHERE id = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $parking_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    die("Error executing statement: " . $stmt->error);
}
$parking_spot = $result->fetch_assoc();
$stmt->close();

// Get user details - Add error checking here too
$stmt = $conn->prepare("SELECT user_name FROM users WHERE id = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    die("Error executing statement: " . $stmt->error);
}
$user_result = $result->fetch_assoc();
$user_name = $user_result['user_name'];
$stmt->close();

// ADD THE NEW BOOKING HANDLER CODE HERE - AFTER DATABASE QUERIES AND BEFORE HTML
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $user_id = $_SESSION['user_id'];
    $parking_spot_id = $parking_id;
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_price = $_POST['total_price'];
    
    // Basic validation
    if (empty($start_time) || empty($end_time) || empty($total_price)) {
        $error = "All fields are required";
    } else {
        // Check if the parking spot is available for the selected time period
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM bookings 
            WHERE parking_spot_id = ? 
            AND status IN ('pending', 'confirmed')
            AND ((start_time BETWEEN ? AND ?) 
            OR (end_time BETWEEN ? AND ?))
        ");
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("issss", $parking_spot_id, $start_time, $end_time, $start_time, $end_time);
        $stmt->execute();
        $conflict_result = $stmt->get_result();
        $conflict = $conflict_result->fetch_assoc();
        
        if ($conflict['count'] > 0) {
            $error = "This time slot is already booked. Please choose another time.";
        } else {
            // Insert the booking
            $stmt = $conn->prepare("
                INSERT INTO bookings (user_id, parking_spot_id, user_name, name, start_time, end_time, total_price, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?,'confirmed', NOW())
            ");
            
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            
            $stmt->bind_param("iissssd", 
                $user_id, 
                $parking_spot_id, 
                $user_name, 
                $parking_spot['name'], 
                $start_time, 
                $end_time, 
                $total_price
            );
            
            if ($stmt->execute()) {
                // Update available spots
                $update_stmt = $conn->prepare("
                    UPDATE parking_spots 
                    SET available_spots = available_spots - 1 
                    WHERE id = ? AND available_spots > 0
                ");
                
                if ($update_stmt === false) {
                    die("Error preparing statement: " . $conn->error);
                }
                
                $update_stmt->bind_param("i", $parking_spot_id);
                $update_stmt->execute();
                
                // Show success message
                echo '<div class="success-message">Booking confirmed successfully! Your parking spot has been reserved.</div>';
                // Optionally redirect to a booking confirmation page
                // header("Location: booking-confirmation.php?booking_id=" . $conn->insert_id);
                // exit();
            } else {
                $error = "Error creating booking: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Parking - ParkEase Nepal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Your existing styles here */
        
        /* ADD THIS NEW CSS FOR ERROR MESSAGES */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
        }
        
        .main-content {
            padding-top: 80px; /* Add padding to prevent navbar overlay */
        }
        
        .booking-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
        }

        /* Rest of your existing styles remain unchanged */
        .parking-details {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .booking-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: grid;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .time-slots {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .price-summary {
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .price-detail {
            display: flex;
            justify-content: space-between;
            margin: 0.5rem 0;
        }

        .total-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #007bff;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #ddd;
        }

        .confirm-btn {
            width: 100%;
            padding: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .confirm-btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .booking-container {
                margin: 1rem;
                padding: 1rem;
            }

            .time-slots {
                grid-template-columns: 1fr;
            }
        }
        /* Rest of your existing styles */
    </style>
</head>
<body>
<div class="main-content">
    <div class="booking-container">
        <?php 
        // Display error message if any
        if (isset($error)) {
            echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
        }
        ?>
        
        <?php if ($parking_spot): ?>
            <div class="parking-details">
                <h2><?php echo htmlspecialchars($parking_spot['name']); ?></h2>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($parking_spot['location']); ?></p>
                <p><i class="fas fa-car"></i> <?php echo htmlspecialchars($parking_spot['available_spots']); ?> spots available</p>
                <p><i class="fas fa-money-bill"></i> Rs. <?php echo number_format($parking_spot['price_per_hour'], 2); ?>/hour</p>
            </div>

            <form class="booking-form" method="POST" action="" id="bookingForm">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <div class="time-slots">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="datetime-local" id="start_time" name="start_time" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="datetime-local" id="end_time" name="end_time" required>
                    </div>
                </div>

                <div class="price-summary">
                    <h3>Price Summary</h3>
                    <div class="price-detail">
                        <span>Parking Rate</span>
                        <span>Rs. <?php echo number_format($parking_spot['price_per_hour'], 2); ?>/hour</span>
                    </div>
                    <div class="price-detail">
                        <span>Duration</span>
                        <span id="duration">0 hours</span>
                    </div>
                    <div class="price-detail total-price">
                        <span>Total Price</span>
                        <span id="totalPrice">Rs. 0.00</span>
                    </div>
                </div>

                <input type="hidden" name="total_price" id="totalPriceInput">
                <button type="submit" class="confirm-btn">Confirm Booking</button>
            </form>

            <script>
                function updatePrice() {
                    const startTime = new Date(document.getElementById('start_time').value);
                    const endTime = new Date(document.getElementById('end_time').value);
                    const pricePerHour = <?php echo $parking_spot['price_per_hour']; ?>;

                    if (startTime && endTime && startTime < endTime) {
                        const duration = (endTime - startTime) / (1000 * 60 * 60); // Convert to hours
                        const totalPrice = duration * pricePerHour;

                        document.getElementById('duration').textContent = duration.toFixed(1) + ' hours';
                        document.getElementById('totalPrice').textContent = 'Rs. ' + totalPrice.toFixed(2);
                        document.getElementById('totalPriceInput').value = totalPrice.toFixed(2);
                    }
                }

                document.getElementById('start_time').addEventListener('change', updatePrice);
                document.getElementById('end_time').addEventListener('change', updatePrice);
            </script>
        <?php else: ?>
            <div class="no-results">
                <h2>Parking spot not found</h2>
                <p>The requested parking spot could not be found. Please try again.</p>
                <a href="search.php" class="book-btn" style="display: inline-block; margin-top: 1rem;">Back to Search</a>
            </div>
        <?php endif; ?>
    </div>
</div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>