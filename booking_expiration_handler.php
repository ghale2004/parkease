<?php

 ob_start(); // Start output buffering
include 'includes/header.php';
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
   exit();
}

// Get user details for pre-filling the form
$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "nbezprep_parkease", "sujit0110", "nbezprep_parkease");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT user_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    if (empty($subject)) {
        $errors[] = "Subject is required";
    }

    if (empty($message)) {
        $errors[] = "Message is required";
    }

    // Verify email matches logged-in user
    if ($email !== $user['email']) {
        $errors[] = "Email must match your registered email address";
    }

    // If no errors, process the form
    if (empty($errors)) {
        $sql = "INSERT INTO contact_requests (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $name, $email, $subject, $message);

        if ($stmt->execute()) {
            echo '<div class="message success-message">Thank you for your message. We will get back to you soon!</div>';
            $_POST = array();
        } else {
            echo '<div class="message error-message">Error: Could not save your request. Please try again later.</div>';
        }
        $stmt->close();
    } else {
        echo '<div class="message error-message">' . implode('<br>', $errors) . '</div>';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact ParkEase - Get in Touch</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }

        /* Contact Container */
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 40px;
            display: flex;
            gap: 40px;
        }

        /* Contact Form Styles */
        .contact-form {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex: 2;
        }

        .contact-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #ffd700;
            color: #333;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #ffcc00;
        }

        /* Contact Info Styles */
        .contact-info {
            background-color: #333;
            color: #fff;
            padding: 40px;
            border-radius: 10px;
            flex: 1;
        }

        .contact-info h2 {
            color: #ffd700;
            margin-bottom: 20px;
        }

        .contact-details {
            margin-bottom: 30px;
        }

        .contact-details p {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .contact-details i {
            margin-right: 15px;
            color: #ffd700;
            font-size: 1.2rem;
            width: 30px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: #fff;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #ffd700;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .contact-container {
                flex-direction: column;
                gap: 20px;
            }
        }

        /* Success/Error Message Styles */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
    </style>
</head>

<body>

    

    <div class="contact-container">
        <!-- Contact Form -->
        <div class="contact-form">
            <h2>Contact ParkEase</h2>
              <?php if (isset($_SESSION['user_id'])): ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($user['name']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user['email']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required
                               value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            <?php else: ?>
                <div class="message error-message">
                    Please <a href="login.php">login</a> to submit a contact request.
                </div>
            <?php endif; ?>
            
        </div>

        <!-- Contact Information -->
        <div class="contact-info">
            <h2>Contact Information</h2>
            <div class="contact-details">
                <p><i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal</p>
                <p><i class="fas fa-phone"></i> +977 9827110969</p>
                <p><i class="fas fa-envelope"></i> support@parkease.com.np</p>
                <p><i class="fas fa-clock"></i> Mon-Fri: 9 AM - 6 PM</p>
            </div>

            <h2>Follow Us</h2>
            <div class="social-links">
                <a href="https://www.facebook.com/ " target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://x.com/home" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://np.linkedin.com/" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>

</html>