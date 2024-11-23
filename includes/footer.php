
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        footer {
  background-color: #333;
  color: #fff;
  padding: 4rem 20px 1rem;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  height: 150px;
}

.footer-section h3 {
  margin-bottom: 1rem;
}

.footer-section ul {
  list-style: none;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section a {
  color: #fff;
  text-decoration: none;
}

.footer-bottom {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #555;
}

/* Responsive Design */
@media (max-width: 768px) {
  .nav-links {
      display: none;
  }
  
  .hero-content h1 {
      font-size: 2rem;
  }
  
  .search-container input {
      width: 200px;
  }
}
        
    </style>
</head>
<body>

<footer>
<div class="footer-content">
            <div class="footer-section">
                <h3>About ParkEase</h3>
                <p>Making parking easier for everyone in Nepal</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="terms.php">Terms & Conditions</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@parkease.com.np</p>
                <p>Phone: +977-9827110969</p>
                <p>Address: Kathmandu, Nepal</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 ParkEase Nepal. All rights reserved.</p>
        </div>
    </footer>
    
    <?php include 'includes/auth-popup.php'; ?>
    
    <!-- <script src="assets/js/auth.js"></script>
    <script src="assets/js/main.js"></script> -->

    
</body>
</html>

