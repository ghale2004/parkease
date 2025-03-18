
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase Nepal - Find Your Parking Space</title>
    <link rel="icon" type="image/x-icon" href="/about-picture/park.svg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <!-- <nav>
        <div class="nav-container">
            <div class="logo">
                <h1>ParkEase Nepal</h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            </div>
        </div>
    </nav> -->


    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-content">
          <!-- <img src="car.jpg" alt="car image"> -->
            <h1>Find Your Perfect Parking Space</h1>
            <p>Save time and money by booking your parking spot in advance</p>
            <div class="search-container">
                <form action="search.php" method="GET">
                    <input type="text" name="location" placeholder="Enter location (e.g., Thamel, New Road)">
                    <button type="submit">Search Parking</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Nearby Parking Section -->
    <section class="nearby-parking">
    <h2>Nearby Parking Spaces</h2>
    <div class="parking-grid">
        <div class="parking-card">
            <img src="https://parklio.com/assets/img/blog/100003/the-best-solution-for-parking-protection_1657283968214.jpg" alt="Parking Lot">
            <div class="parking-info">
                <h3>Thamel Parking Complex</h3>
                <p><i class="fas fa-map-marker-alt"></i> 0.5 km away</p>
                <p><i class="fas fa-car"></i> 15 spots available</p>
                <p><i class="fas fa-star"></i> 4.5/5 (120 reviews)</p>
                <p class="price">Rs. 60/hour</p>
                <button onclick="bookParking(1, 'Thamel')" class="book-btn">Book Now</button>
            </div>
        </div>
        <!-- More parking cards... -->
    </div>
</section>

    <!-- How It Works -->
    <section class="how-it-works">
        <h2>How It Works</h2>
        <div class="steps-container">
            <div class="step">
            <a href = 'search.php' class = "deco" >
                <i class="fas fa-search"></i>  </a>
                <h3>Search</h3>
                <p>Find parking spots near your destination</p>
               
            </div>
            <div class="step">
            <a href = 'search.php' class = "deco">
                <i class="fas fa-calendar-check"></i>  </a>
                <h3>Book</h3>
                <p>Reserve your spot in advance</p>
            </div>
            <div class="step">
            <a href = 'my-bookings.php'>
                <i class="fas fa-parking"></i></a>
                <h3>Park</h3>
                <p>Show your booking code and park safely</p>
            </div>
        </div>
    </section>
            
    <!-- Footer -->

    <?php include 'includes/footer.php'; ?>

    <script>
function bookParking(parkingId, location) {
    window.location.href = `search.php?location=${encodeURIComponent(location)}`;
}
</script>




   




 
</body>
</html>