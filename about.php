<?php
// Start the session at the very top of the file
// session_start();
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About ParkEase - Solving Parking Challenges in Kathmandu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Existing styles from the previous about.php */
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

        /* Remove the redundant navigation styles since we're using header.php */
        
        /* Container Styles */
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 40px; /* Increased top padding to account for fixed nav */
        }

        /* Rest of the styles remain the same as in the previous about.php */
        /* Hero Section */
        .about-hero {
            /* background-color: #333; */
            background-image: url('about-picture/pic1.jpg');
            color: #fff;
            text-align: center;
            padding: 60px 20px;
            margin-bottom: 40px;
        }

        .about-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #ffd700;
        }

        .about-hero p {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        /* Problem and Solution Section */
        .problem-solution {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-bottom: 40px;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .problem-solution img {
            max-width: 400px;
            border-radius: 10px;
        }

        .problem-solution-content {
            flex: 1;
        }

        .problem-solution-content h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Features Section */
        .features {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
        }

        .feature {
            background-color: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            flex: 1;
        }

        .feature i {
            font-size: 3rem;
            color: #ffd700;
            margin-bottom: 20px;
        }

        .feature h3 {
            margin-bottom: 15px;
        }

        /* Mission Section */
        .mission {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 60px 20px;
        }

        .mission h2 {
            color: #ffd700;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .problem-solution {
                flex-direction: column;
            }

            .features {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="about-container">
        <!-- Hero Section -->
        <section class="about-hero">
            <h1>ParkEase Nepal</h1>
            <p>Revolutionizing Parking Solutions in Kathmandu Valley</p>
        </section>

        <!-- Problem and Solution Section -->
        <section class="problem-solution">
            <img src="about-picture/pic3.jpg" alt="Parking Challenges in Kathmandu">
            <div class="problem-solution-content">
                <h2>The Parking Predicament</h2>
                <p>Kathmandu Valley faces an unprecedented parking crisis. With rapidly growing urbanization, limited parking infrastructure, and increasing vehicle ownership, finding a safe and convenient parking spot has become a daily struggle for residents and visitors alike.</p>
                <br>
                <p>ParkEase is not just an app; it's a comprehensive solution designed to transform the way people experience parking in Kathmandu.</p>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="feature">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Real-Time Availability</h3>
                <p>Get instant updates on available parking spaces across Kathmandu, reducing time spent searching for parking.</p>
            </div>
            <div class="feature">
                <i class="fas fa-mobile-alt"></i>
                <h3>Seamless Booking</h3>
                <p>Reserve and pay for your parking spot in advance through our user-friendly mobile application.</p>
            </div>
            <div class="feature">
                <i class="fas fa-lock"></i>
                <h3>Secure Parking</h3>
                <p>Partner with verified parking locations to ensure the safety of your vehicle during your stay.</p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="mission">
            <h2>Our Mission</h2>
            <p>To eliminate parking stress, reduce urban congestion, and provide a smart, efficient parking solution for Kathmandu Valley's residents and visitors.</p>
        </section>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>