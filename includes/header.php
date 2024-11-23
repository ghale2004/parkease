<?php
// includes/header.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase Nepal - Find Your Parking Space</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <style>
    /* Navigation Styles */
    nav {
        background-color: #333;
        padding: 1rem 0;
        width: 100%;
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
    }

    .nav-links {
        display: flex;
        align-items: center;
    }

    .nav-links a,
    .user-profile-menu .user-greeting {
        color: #fff;
        text-decoration: none;
        margin: 0 15px;
        transition: color 0.3s;
        display: flex;
        align-items: center;
    }

    .nav-links a:hover,
    .user-profile-menu .user-greeting:hover {
        color: #ffd700;
    }

    /* User Profile Styles */
    .user-profile-menu {
        position: relative;
        display: flex;
        align-items: center;
    }

    .user-greeting {
        cursor: pointer;
        display: flex;
        align-items: center;
        color: #fff !important;
    }

    .user-greeting i {
        color: #fff;
        margin-right: 8px;
    }

    .user-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #444;
        min-width: 200px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-radius: 5px;
        padding: 10px;
        z-index: 1000;
        margin-top: 10px;
    }

    .user-dropdown.show {
        display: block;
    }

    .user-dropdown a {
        display: flex;
        align-items: center;
        padding: 10px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .user-dropdown a:hover {
        background-color: #555;
    }

    .user-dropdown a i {
        margin-right: 10px;
    }

    .admin-badge {
        background-color: #ffd700;
        color: #333;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.8em;
        margin-left: 8px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .nav-links,
        .user-profile-menu {
            flex-direction: column;
            align-items: center;
        }

        .nav-links a,
        .user-profile-menu .user-greeting {
            margin: 10px 0;
        }

        .user-dropdown {
            position: static;
            width: 100%;
        }
    }
    </style>

   <script>
   document.addEventListener('DOMContentLoaded', function() {
       const userGreeting = document.querySelector('.user-greeting');
       const userDropdown = document.querySelector('.user-dropdown');

       // Toggle dropdown on user greeting click
       userGreeting.addEventListener('click', function(e) {
           e.stopPropagation();
           userDropdown.classList.toggle('show');
       });

       // Close dropdown if clicked outside
       document.addEventListener('click', function(e) {
           if (userGreeting && !userGreeting.contains(e.target)) {
               userDropdown.classList.remove('show');
           }
       });
   });
   </script>
</head>
<body>
<nav>
    <div class="nav-container">
        <div class="logo">
            <h1>ParkEase Nepal</h1>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>

            <?php if(isset($_SESSION['user_name'])): ?>
                <div class="user-profile-menu">
                    <div class="user-greeting">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <span class="admin-badge">Admin</span>
                        <?php endif; ?>
                        <i class="fas fa-caret-down" style="margin-left: 5px;"></i>
                    </div>
                    <div class="user-dropdown">
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <!-- Admin Menu Items -->
                            <a href="admin/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Admin Dashboard       
                            </a>
                            <a href="admin/manage-users.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                            <a href="admin/manage-parking.php">
                                <i class="fas fa-parking"></i> Manage Parking
                            </a>
                        <?php else: ?>
                            <!-- Regular User Menu Items -->
                            <a href="bookings.php">
                                <i class="fas fa-calendar-alt"></i> My Bookings
                            </a>
                        <?php endif; ?>
                        <!-- Common Menu Items for Both User Types -->
                        <a href="profile.php">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <a href="auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="#" onclick="openAuthPopup('login')" class="login-btn">Login</a>
                <a href="#" onclick="openAuthPopup('register')" class="register-btn">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
</body>
</html>