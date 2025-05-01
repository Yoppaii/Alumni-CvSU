<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #10b981;
            --primary-hover: #059669;
            --primary-light: #d1fae5;
            --secondary-color: #64748b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --danger-color: #ef4444;
            --success-color: #22c55e;
            --radius-md: 0.5rem;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --transition: all 0.3s ease;
        }

        /* Dark mode variables */
        [data-theme="dark"] {
            --primary-color: #10b981;
            --primary-hover: #059669;
            --primary-light: rgba(16, 185, 129, 0.2);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --bg-primary: #1e293b;
            --bg-secondary: #0f172a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
        }

        .logo-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .logo-section p {
            color: var(--text-secondary);
        }

        .register-form-container {
            background-color: var(--bg-primary);
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.625rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .terms-group {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: start;
            gap: 0.75rem;
        }

        .terms-group input[type="checkbox"] {
            margin-top: 0.25rem;
            accent-color: var(--primary-color);
        }

        .terms-group label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .terms-group a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .terms-group a:hover {
            color: var(--primary-hover);
        }

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background-color: var(--primary-hover);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            color: var(--primary-hover);
        }

        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius-md);
        }

        .theme-toggle:hover {
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .error-message {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
        }

        .error .error-message {
            display: block;
        }

        .error input {
            border-color: var(--danger-color);
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .password-strength.weak { color: var(--danger-color); }
        .password-strength.medium { color: #eab308; }
        .password-strength.strong { color: var(--success-color); }
    </style>
</head>
<body>
    <button class="theme-toggle" aria-label="Toggle theme">
        <i class="fas fa-sun"></i>
    </button>

    <div class="register-container">
        <div class="logo-section">
            <img src="/Alumni-CvSU/asset/images/1.png" alt="Logo">
            <h2>Create an Account</h2>
            <p>Fill in the details to get started</p>
        </div>

        <div class="register-form-container">
            <form id="registerForm" onsubmit="return handleSubmit(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" required placeholder="Enter your first name">
                        <div class="error-message">First name is required</div>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required placeholder="Enter your last name">
                        <div class="error-message">Last name is required</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                    <div class="error-message">Please enter a valid email address</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" required 
                                   placeholder="Enter your password" onkeyup="checkPasswordStrength(this.value)">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength"></div>
                        <div class="error-message">Password must be at least 8 characters</div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="password-field">
                            <input type="password" id="confirmPassword" name="confirmPassword" required 
                                   placeholder="Confirm your password">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="error-message">Passwords do not match</div>
                    </div>
                </div>

                <div class="terms-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="submit-btn">Create Account</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="#">Sign in</a>
            </div>
        </div>
    </div>

    <script>
        // Theme management
        const themeToggle = document.querySelector('.theme-toggle');
        const themeIcon = themeToggle.querySelector('i');
        let currentTheme = localStorage.getItem('theme') || 'light';

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            themeIcon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }

        applyTheme(currentTheme);

        themeToggle.addEventListener('click', () => {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', currentTheme);
            applyTheme(currentTheme);
        });

        // Password visibility toggle
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleBtn = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleBtn.className = 'fas fa-eye';
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthIndicator = document.querySelector('.password-strength');
            const weak = /[a-zA-Z]/.test(password);
            const medium = /[a-zA-Z]/.test(password) && /[0-9]/.test(password);
            const strong = /[a-zA-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password);

            if (password.length === 0) {
                strengthIndicator.textContent = '';
                strengthIndicator.className = 'password-strength';
            } else if (strong && password.length >= 8) {
                strengthIndicator.textContent = 'Strong password';
                strengthIndicator.className = 'password-strength strong';
            } else if (medium && password.length >= 6) {
                strengthIndicator.textContent = 'Medium strength password';
                strengthIndicator.className = 'password-strength medium';
            } else {
                strengthIndicator.textContent = 'Weak password';
                strengthIndicator.className = 'password-strength weak';
            }
        }

        // Replace the existing handleSubmit function with this updated version
        async function handleSubmit(event) {
            event.preventDefault();
            
            const form = event.target;
            const firstName = form.firstName.value;
            const lastName = form.lastName.value;
            const email = form.email.value;
            const password = form.password.value;
            const confirmPassword = form.confirmPassword.value;
            const terms = form.terms.checked;

            // Reset previous errors
            form.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });

            let hasError = false;

            // Validate first name
            if (firstName.trim().length < 2) {
                form.querySelector('#firstName').parentElement.classList.add('error');
                hasError = true;
            }

            // Validate last name
            if (lastName.trim().length < 2) {
                form.querySelector('#lastName').parentElement.classList.add('error');
                hasError = true;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                form.querySelector('#email').parentElement.classList.add('error');
                hasError = true;
            }

            // Validate password
            if (password.length < 8) {
                form.querySelector('#password').parentElement.classList.add('error');
                hasError = true;
            }

            // Validate password confirmation
            if (password !== confirmPassword) {
                form.querySelector('#confirmPassword').parentElement.classList.add('error');
                hasError = true;
            }

            // Validate terms
            if (!terms) {
                alert('Please accept the Terms of Service and Privacy Policy');
                hasError = true;
            }

            if (hasError) {
                return false;
            }

            try {
                const formData = new FormData();
                formData.append('firstName', firstName);
                formData.append('lastName', lastName);
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('admin-function/register_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message);
                    form.reset();
                    // Optionally redirect to login page
                    // window.location.href = 'login.html';
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('An error occurred during registration. Please try again.');
                console.error('Registration error:', error);
            }

            return false;
        }
        // Reset password strength indicator when form is reset
        document.getElementById('registerForm').addEventListener('reset', () => {
            document.querySelector('.password-strength').textContent = '';
            document.querySelector('.password-strength').className = 'password-strength';
        });

        // Prevent spaces in password fields
        document.getElementById('password').addEventListener('keypress', (e) => {
            if (e.key === ' ') {
                e.preventDefault();
            }
        });

        document.getElementById('confirmPassword').addEventListener('keypress', (e) => {
            if (e.key === ' ') {
                e.preventDefault();
            }
        });

        // Real-time password match validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const errorElement = this.parentElement.parentElement.querySelector('.error-message');
            
            if (confirmPassword.length > 0) {
                if (password !== confirmPassword) {
                    errorElement.style.display = 'block';
                    errorElement.textContent = 'Passwords do not match';
                } else {
                    errorElement.style.display = 'none';
                }
            } else {
                errorElement.style.display = 'none';
            }
        });
    </script>
</body>
</html>