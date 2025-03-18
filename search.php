<?php 
// session_start();
include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - ParkEase Nepal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset default margin and padding */
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

        /* Main content wrapper */
        .main-content {
            flex: 1;
            padding: 80px 0 60px; /* Adjust based on your header/footer height */
            width: 100%;
            position: relative;
        }

        .search-results {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .search-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .search-header h1 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .search-form {
            max-width: 600px;
            margin: 0 auto 2rem;
            display: flex;
            gap: 0.5rem;
        }

        .search-form input[type="text"] {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .search-form button {
            padding: 0.8rem 1.5rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            white-space: nowrap;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .parking-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .parking-card:hover {
            transform: translateY(-5px);
        }

        .parking-info {
            padding: 1.5rem;
        }

        .parking-info h3 {
            margin: 0 0 1rem;
            color: #333;
            font-size: 1.25rem;
        }

        .parking-info p {
            margin: 0.5rem 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .parking-info .price {
            font-size: 1.2rem;
            color: #007bff;
            font-weight: bold;
            margin: 1rem 0;
        }

        .book-btn {
            width: 100%;
            padding: 0.8rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 1rem;
        }

        .book-btn:hover {
            background-color: #0056b3;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 60px 0 40px;
            }

            .search-form {
                flex-direction: column;
            }

            .results-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="search-results">
            <div class="search-header">
                <h1>Search Results</h1>
                <form class="search-form" action="search.php" method="GET">
                    <input type="text" name="location" placeholder="Enter location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="results-grid">
                <?php
                // Database connection
                $conn = new mysqli("localhost", "nbezprep_parkease", "sujit0110", "nbezprep_parkease");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                if (isset($_GET['location']) && !empty($_GET['location'])) {
                    $location = '%' . $conn->real_escape_string($_GET['location']) . '%';
                    
                    $sql = "SELECT * FROM parking_spots WHERE location LIKE ? ORDER BY rating DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $location);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <div class="parking-card">
                                <div class="parking-info">
                                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p><i class="fas fa-car"></i> <?php echo htmlspecialchars($row['available_spots']); ?> spots available</p>
                                    <p><i class="fas fa-star"></i> <?php echo number_format($row['rating'], 1); ?>/5 (<?php echo $row['reviews_count']; ?> reviews)</p>
                                    <p class="price">Rs. <?php echo number_format($row['price_per_hour'], 2); ?>/hour</p>
                                    <!-- <button onclick="bookParking(<?php echo $row['id']; ?>)" class="book-btn">Book Now</button>-->

                                    <!-- update -->
                                    <?php
                                    if (isset($_SESSION['user_id'])) {
                                    echo '<button onclick="bookParking(' . $row['id'] . ')" class="book-btn">Book Now</button>';
                                    } else {
                                    echo '<button onclick="openAuthPopup(\'register\')" class="book-btn">Register to Book</button>';
                                    }
                                    ?>
                                    <!-- update -->
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="no-results">No parking spots found in this location. Please try a different search.</div>';
                    }

                    $stmt->close();
                } else {
                    echo '<div class="no-results">Please enter a location to search for parking spots.</div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script>
    /* function bookParking(parkingId) {
        window.location.href = `booking.php?parking_id=${parkingId}`;
    } */

    // update
    
    function bookParking(parkingId) {
    <?php if (isset($_SESSION['user_id'])) { ?>
        window.location.href = `booking.php?parking_id=${parkingId}`;
    <?php } else { ?>
        openAuthPopup('register');
    <?php } ?>
    }



    </script>

    <!-- all is  update -->

    <!-- auth-popup.php -->
<div class="popup-overlay" id="authPopup">
    <div class="popup-container">
        <div class="popup-close" onclick="closeAuthPopup()">×</div>

        <!-- Login Form -->
        <div class="auth-form" id="loginForm">
            <h2>Login</h2>

            <!-- Login Type Toggle -->
            <div class="login-type-toggle">
                <button class="toggle-btn active" id="userLoginBtn" onclick="switchLoginType('user')">User Login</button>
                <button class="toggle-btn" id="adminLoginBtn" onclick="switchLoginType('admin')">Admin Login</button>
            </div>

            <form id="loginFormElement" onsubmit="return validateLoginForm(event)">
                <input type="hidden" id="loginType" name="loginType" value="user">
                <div class="form-group">
                    <input type="email" id="loginEmail" name="email" placeholder="Email" required>
                    <span class="error-message" id="loginEmailError"></span>
                </div>

                <div class="form-group">
                    <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                    <span class="error-message" id="loginPasswordError"></span>
                </div>

                <button type="submit" class="auth-button" id="loginButton">Login</button>
            </form>
            <p id="registerLink">Don't have an account? <a href="#" onclick="toggleAuthForms()">Register</a></p>
        </div>

        <!-- Registration Form -->
        <div class="auth-form" id="registerForm" style="display: none;">
            <h2>Register</h2>
            <form id="registerFormElement" onsubmit="return validateRegisterForm(event)">
                <div class="form-group">
                    <input type="text" id="registerName" name="name" placeholder="Full Name" required>
                    <span class="error-message" id="registerNameError"></span>
                </div>

                <div class="form-group">
                    <input type="email" id="registerEmail" name="email" placeholder="Email" required>
                    <span class="error-message" id="registerEmailError"></span>
                </div>

                <div class="form-group">
                    <input type="tel" id="registerPhone" name="phone" placeholder="Phone Number" required>
                    <span class="error-message" id="registerPhoneError"></span>
                </div>

                <div class="form-group">
                    <input type="password" id="registerPassword" name="password" placeholder="Password" required>
                    <span class="error-message" id="registerPasswordError"></span>
                </div>

                <div class="form-group">
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                    <span class="error-message" id="confirmPasswordError"></span>
                </div>

                <button type="submit" class="auth-button">Register</button>
            </form>
            <p>Already have an account? <a href="#" onclick="toggleAuthForms()">Login</a></p>
        </div>
    </div>
</div>

<style>
    /* Auth Popup Styles */
    .popup-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .popup-container {
        background-color: white;
        padding: 2rem;
        border-radius: 10px;
        position: relative;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .popup-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }

    .auth-form {
        padding: 1rem 0;
    }

    .auth-form h2 {
        text-align: center;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .auth-button {
        width: 100%;
        padding: 12px;
        background-color: #ffd700;
        border: none;
        border-radius: 5px;
        color: #333;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .auth-button:hover {
        background-color: #e6c200;
    }

    .auth-form p {
        text-align: center;
        margin-top: 1rem;
    }

    .auth-form a {
        color: #ffd700;
        text-decoration: none;
    }

    .auth-form a:hover {
        text-decoration: underline;
    }

    /* Login Type Toggle Styles */
    .login-type-toggle {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        gap: 10px;
    }

    .toggle-btn {
        padding: 8px 16px;
        border: 2px solid #ffd700;
        background: transparent;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .toggle-btn.active {
        background: #ffd700;
        color: #333;
    }

    .toggle-btn:hover {
        background: #ffd700;
        color: #333;
    }

    @media (max-width: 480px) {
        .popup-container {
            width: 95%;
            padding: 1.5rem;
        }
    }
</style>

<script>
    // Global variable for login type
    let currentLoginType = 'user';

    // Auth Popup Functions
    function openAuthPopup(type = 'login') {
        document.getElementById('authPopup').style.display = 'flex';
        if (type === 'register') {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        } else {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }
    }

    function closeAuthPopup() {
        document.getElementById('authPopup').style.display = 'none';
    }

    function toggleAuthForms() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        if (loginForm.style.display === 'none') {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
        } else {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
        }
    }

    function switchLoginType(type) {
        currentLoginType = type;
        document.getElementById('loginType').value = type;

        // Update button states
        document.getElementById('userLoginBtn').classList.toggle('active', type === 'user');
        document.getElementById('adminLoginBtn').classList.toggle('active', type === 'admin');

        // Update form elements
        const registerLink = document.getElementById('registerLink');
        const loginButton = document.getElementById('loginButton');

        if (type === 'admin') {
            registerLink.style.display = 'none';
            loginButton.textContent = 'Login as Admin';
        } else {
            registerLink.style.display = 'block';
            loginButton.textContent = 'Login';
        }
    }

    // Form Validation Functions
    function validateLoginForm(event) {
        event.preventDefault();
        let isValid = true;

        // Email validation
        const email = document.getElementById('loginEmail');
        const emailError = document.getElementById('loginEmailError');
        if (!validateEmail(email.value)) {
            emailError.textContent = 'Please enter a valid email address';
            isValid = false;
        } else {
            emailError.textContent = '';
        }

        // Password validation
        const password = document.getElementById('loginPassword');
        const passwordError = document.getElementById('loginPasswordError');
        if (password.value.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
            isValid = false;
        } else {
            passwordError.textContent = '';
        }

        if (isValid) {
            const formData = new FormData(event.target);
            formData.append('loginType', currentLoginType);
            submitLogin(formData);
        }

        return false;
    }

    function validateRegisterForm(event) {
        event.preventDefault();
        let isValid = true;

        // Name validation
        // const name = document.getElementById('registerName');
        // const nameError = document.getElementById('registerNameError');
        // if (name.value.length < 2) {
        //     nameError.textContent = 'Please enter your full name';
        //     isValid = false;
        // } else {
        //     nameError.textContent = '';
        // }

        const name = document.getElementById('registerName');
        const nameError = document.getElementById('registerNameError');
        const nameRegex = /^[A-Za-z\s]+$/; // Allows only letters and spaces

        if (name.value.length < 2) {
            nameError.textContent = 'Please enter your full name';
            isValid = false;
        } else if (!nameRegex.test(name.value)) {
            nameError.textContent = 'Name must contain only letters and spaces';
            isValid = false;
        } else {
            nameError.textContent = '';
        }


        // Email validation
        const email = document.getElementById('registerEmail');
        const emailError = document.getElementById('registerEmailError');
        if (!validateEmail(email.value)) {
            emailError.textContent = 'Please enter a valid email address';
            isValid = false;
        } else {
            emailError.textContent = '';
        }

        // Phone validation
        const phone = document.getElementById('registerPhone');
        const phoneError = document.getElementById('registerPhoneError');
        if (!validatePhone(phone.value)) {
            phoneError.textContent = 'Please enter a valid phone number';
            isValid = false;
        } else {
            phoneError.textContent = '';
        }

        // Password validation
        const password = document.getElementById('registerPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const passwordError = document.getElementById('registerPasswordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        if (password.value.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
            isValid = false;
        } else {
            passwordError.textContent = '';
        }

        if (password.value !== confirmPassword.value) {
            confirmPasswordError.textContent = 'Passwords do not match';
            isValid = false;
        } else {
            confirmPasswordError.textContent = '';
        }

        if (isValid) {
            const formData = new FormData(event.target);
            submitRegistration(formData);
        }

        return false;
    }

    // Helper Functions
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        // const re = /^[0-9]{10}$/; 
        const re = /^(98|97)[0-9]{8}$/;
        return re.test(phone);
    }

    // API Calls

    async function submitLogin(formData) {
        try {
            const loginType = formData.get('loginType'); // user or admin
            const endpoint = loginType === 'admin' ? 'admin/login.php' : 'auth/login.php';

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password')
                }),
            });

            const data = await response.json();
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Login failed. Please try again.');
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred. Please try again.');
        }
    }


    async function submitRegistration(formData) {
        try {
            const response = await fetch('auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    email: formData.get('email'),
                    phone: formData.get('phone'),
                    password: formData.get('password')
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                if (data.success) {
                    alert('Registration successful! Please login.');
                    toggleAuthForms();
                } else {
                    alert(data.message || 'Registration failed. Please try again.');
                }
            } else {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                alert('Unexpected server response.');
            }
        } catch (error) {
            console.error('Registration error:', error);
            alert('An error occurred. Please try again.');
        }
    }
</script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>