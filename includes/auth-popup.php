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
        const re = /^[0-9]{10}$/;
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