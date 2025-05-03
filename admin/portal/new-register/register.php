<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CvSU</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

        :root {
            --login-primary: #006400;
            --login-secondary: #008000;
            --login-accent: #90EE90;
            --login-text-light: #ffffff;
            --login-text-dark: #333333;
            --login-gray-light: #f5f5f5;
            --login-border-color: rgba(0, 0, 0, 0.1);
            --login-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --login-shadow-md: 0 4px 8px rgba(0, 0, 0, 0.1);
            --login-transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        /* Loading Overlay */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--login-text-light);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-content {
            text-align: center;
        }

        .loading-text {
            margin-top: 15px;
            color: var(--login-text-light);
            font-size: 14px;
            font-weight: 500;
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Animations */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes pulse {
            0% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.6;
            }
        }

        .loading-overlay-show {
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        .loading-overlay-hide {
            animation: fadeOut 0.3s ease-in-out forwards;
        }

        /* Body and Background */
        body {
            background: url('asset/images/bahay.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            filter: blur(15px);
            z-index: -1;
        }

        /* Container */
        .register-container {
            width: 100%;
            max-width: 500px;
            background: var(--login-text-light);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--login-shadow-md);
        }

        .register-form-container {
            padding: 40px;
            background: var(--login-text-light);
        }

        /* Headers */
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            font-size: 1.75rem;
            color: var(--login-text-dark);
            margin-bottom: 8px;
        }

        .register-header p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Input Groups */
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--login-text-dark);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 12px 35px 12px 35px;
            border: 1px solid var(--login-border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--login-transition);
        }

        .input-group .input-icon-left {
            position: absolute;
            left: 12px;
            top: 38px;
            color: #666;
            transition: var(--login-transition);
        }

        .input-group .password-toggle {
            position: absolute;
            right: 12px;
            top: 38px;
            color: #666;
            cursor: pointer;
            transition: var(--login-transition);
        }

        .input-group .password-toggle:hover {
            color: var(--login-primary);
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--login-primary);
            box-shadow: 0 0 0 3px rgba(0, 100, 0, 0.1);
        }

        .input-group input:focus+.input-icon-left {
            color: var(--login-primary);
        }

        /* Radio Group */
        .radio-group {
            margin-bottom: 20px;
        }

        .radio-group label {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            cursor: pointer;
        }

        .radio-group input[type="radio"] {
            margin-right: 8px;
        }

        /* Buttons */
        .form-button {
            width: 100%;
            padding: 12px;
            background: var(--login-primary);
            color: var(--login-text-light);
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--login-transition);
            margin-top: 15px;
        }

        .form-button:hover {
            background: var(--login-secondary);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        /* Links */
        .login-link,
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }

        .login-link a,
        .register-link a {
            color: var(--login-primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--login-transition);
        }

        .login-link a:hover,
        .register-link a:hover {
            color: var(--login-secondary);
            text-decoration: underline;
        }

        .register-link {
            margin-top: 15px;
            font-size: 0.85rem;
        }

        .view-booking-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            animation: viewBookingSlideDown 0.3s ease-out;
        }

        .view-booking-toast-success {
            border-left: 4px solid var(--login-primary);
        }

        .view-booking-toast-success .view-booking-toast-icon i {
            color: var(--login-primary);
        }

        .view-booking-toast-message {
            flex-grow: 1;
            font-size: 14px;
            color: var(--login-text-dark);
        }

        .view-booking-toast-error {
            flex-grow: 1;
            font-size: 14px;
            color: var(--login-text-dark);
            border-left: 4px solid var(--login-primary);

        }

        .view-booking-toast-error .view-booking-toast-icon i {
            color: var(--login-primary);
        }


        .view-booking-toast-close {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--login-text-light);
            cursor: pointer;
            padding: 4px;
        }

        .view-booking-toast-close:hover {
            color: var(--login-text-dark);
        }

        @keyframes viewBookingSlideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Legal Links */
        .legal-links {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--login-border-color);
            color: #666;
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .legal-links a {
            color: var(--login-primary);
            text-decoration: none;
            transition: var(--login-transition);
        }

        .legal-links a:hover {
            color: var(--login-secondary);
            text-decoration: underline;
        }

        /* Step Indicators */
        .step-indicators {
            display: flex;
            justify-content: center;
            padding-top: 30px;
        }

        .step {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #f5f5f5;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 15px;
            position: relative;
            font-weight: 600;
            transition: var(--login-transition);
        }

        .step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 30px;
            height: 2px;
            background-color: #f5f5f5;
            transform: translateY(-50%);
        }

        .step:last-child::after {
            display: none;
        }

        .step.active {
            background-color: var(--login-primary);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .step.completed {
            background-color: var(--login-secondary);
            color: white;
        }

        .step.completed::after,
        .step.active::after {
            background-color: var(--login-secondary);
        }

        /* Form Steps */
        .form-step {
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }

        .form-step h2 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--login-text-dark);
            font-size: 1.5rem;
        }

        .form-step p {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 0.9rem;

        }

        /* Navigation Buttons */
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .back-button {
            background-color: #f5f5f5;
            color: #555;
            border: 1px solid var(--login-border-color);
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--login-transition);
        }

        .back-button:hover {
            background-color: #e9e9e9;
        }

        .next-button {
            background-color: var(--login-primary);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--login-transition);
        }

        .next-button:hover {
            background-color: var(--login-secondary);
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .register-container {
                max-width: 100%;
            }

            .register-form-container {
                padding: 20px;
            }

            .notification {
                left: 20px;
                right: 20px;
                text-align: center;
                justify-content: center;
            }

            .legal-links {
                font-size: 0.7rem;
                padding: 0 10px;
            }
        }

        .selection-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .selection-card {
            flex: 1;
            padding: 25px 20px;
            border-radius: 10px;
            border: 2px solid var(--login-border-color);
            text-align: center;
            cursor: pointer;
            transition: var(--login-transition);
            position: relative;
            overflow: hidden;
        }

        .selection-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .selection-card.selected {
            border-color: var(--login-primary);
            background-color: rgba(0, 100, 0, 0.05);
        }

        .selection-card .card-icon {
            font-size: 2.5rem;
            color: var(--login-primary);
            margin-bottom: 15px;
        }

        .selection-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--login-text-dark);
        }

        .selection-card p {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.4;
        }

        .selection-card .checkmark {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background-color: var(--login-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
        }

        .selection-card.selected .checkmark {
            opacity: 1;
            transform: scale(1);
        }

        .info-badge {
            display: inline-block;
            background-color: #f8f9fa;
            border-left: 3px solid var(--login-primary);
            padding: 12px 15px;
            margin-top: 15px;
            border-radius: 4px;
            text-align: left;
            font-size: 0.85rem;
            color: #666;
        }

        .info-badge i {
            color: var(--login-primary);
            margin-right: 8px;
        }

        /* User Type Description */
        .user-type-description {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--login-primary);
        }

        .user-type-description h4 {
            display: flex;
            align-items: center;
            font-size: 1rem;
            margin-bottom: 8px;
            color: var(--login-text-dark);
        }

        .user-type-description h4 i {
            margin-right: 8px;
            color: var(--login-primary);
        }

        .user-type-description p {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }

        .user-type-description ul {
            margin: 8px 0;
            padding-left: 25px;
        }

        .user-type-description li {
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 4px;
        }

        .benefit-tag {
            display: inline-block;
            background-color: #e8f5e9;
            color: #2e7d32;
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* Info Box Styling */
        .type-info-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--login-primary);
            padding: 15px;
            margin: 20px 0;
            transition: var(--login-transition);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .type-info-box .info-content {
            display: flex;
            align-items: flex-start;
        }

        .type-info-box i.fas {
            color: var(--login-primary);
            font-size: 1.2rem;
            margin-right: 12px;
            margin-top: 2px;
        }

        .type-info-box p {
            color: #555;
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.5;
        }

        /* Animation for showing and hiding info boxes */
        .type-info-box {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease, margin 0.4s ease;
            margin: 0;
        }

        .type-info-box:not([style*="display: none"]) {
            max-height: 200px;
            opacity: 1;
            margin: 20px 0;
        }

        /* Different color themes for different user types */
        #alumniInfoBox {
            border-left-color: var(--login-primary);
            /* Blue theme for alumni */
        }

        #alumniInfoBox i.fas {
            color: var(--login-primary);
        }

        #guestInfoBox {
            border-left-color: var(--login-primary);
            /* Orange theme for guests */
        }

        #guestInfoBox i.fas {
            color: var(--login-primary);
        }
    </style>
</head>

<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing...</div>
        </div>
    </div>

    <div id="view-booking-toast" class="view-booking-toast" style="display: none;">
        <div class="view-booking-toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="view-booking-toast-message"></div>
        <button class="view-booking-toast-close">&times;</button>
    </div>
    <div class="register-container">
        <div class="step-indicators">
            <!-- Step indicators - these are dynamically managed based on user type -->
            <div class="step active" data-step="1">1</div>
            <div class="step" data-step="2">2</div>
            <div class="step" data-step="3">3</div>
            <div class="step" data-step="4">4</div>
        </div>

        <form id="registerForm" class="register-form-container">
            <!-- Step 1: User Type Selection -->
            <div class="form-step" id="step1">
                <h2>Registration Type</h2>
                <p>Please select your registration type</p>

                <!-- Selection cards -->
                <div class="selection-cards">
                    <div class="selection-card selected" data-value="Alumni">
                        <div class="checkmark"><i class="fas fa-check"></i></div>
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Alumni</h3>
                        <p>For graduates with an alumni ID card</p>
                        <div class="info-badge">
                            <i class="fas fa-info-circle"></i> Verification required
                        </div>
                    </div>
                    <div class="selection-card" data-value="Guest">
                        <div class="checkmark"><i class="fas fa-check"></i></div>
                        <div class="card-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <h3>Guest</h3>
                        <p>For visitors who want to use the system</p>
                        <div class="info-badge">
                            <i class="fas fa-info-circle"></i> No verification needed
                        </div>
                    </div>
                </div>

                <!-- Hidden radio buttons for backward compatibility -->
                <div class="radio-group" style="display: none;">
                    <label>
                        <input type="radio" name="user_type" value="Alumni" id="alumniRadio" checked> Alumni
                    </label>
                    <label>
                        <input type="radio" name="user_type" value="Guest" id="guestRadio"> Guest
                    </label>
                </div>

                <!-- Info boxes for each type -->
                <div id="alumniInfoBox" class="type-info-box">
                    <div class="info-content">
                        <i class="fas fa-info-circle"></i>
                        <p>As an alumni, you'll need to verify your credentials using your Alumni ID card number.</p>
                    </div>
                </div>

                <!-- <div id="guestInfoBox" class="type-info-box" style="display: none;">
                    <div class="info-content">
                        <i class="fas fa-info-circle"></i>
                        <p>As a guest, you can register without verification but will have limited access to features.</p>
                    </div>
                </div> -->

                <div class="navigation-buttons">
                    <button type="button" id="backToStep1Btn" class="back-button" style="visibility: hidden;">Back</button>
                    <button type="button" id="proceedToStep2Btn" class="next-button">Continue</button>
                </div>
            </div>

            <!-- Step 2: Alumni Verification (Only for Alumni) -->
            <div class="form-step" id="alumniVerificationStep" style="display:none;">
                <h2>Alumni Verification</h2>
                <p>Please enter your alumni details for verification</p>

                <div class="input-group">
                    <label for="alumni_id_card_no">Alumni ID Card Number</label>
                    <i class="fa-solid fa-id-card input-icon-left"></i>
                    <input type="text" id="alumni_id_card_no" name="alumni_id_card_no" placeholder="Enter your Alumni ID" required>
                </div>

                <div class="input-group">
                    <label for="verify_first_name">First Name</label>
                    <i class="fa-solid fa-user input-icon-left"></i>
                    <input type="text" id="verify_first_name" name="verify_first_name" placeholder="Enter your first name" required>
                </div>

                <div class="input-group">
                    <label for="verify_middle_name">Middle Name</label>
                    <i class="fa-solid fa-user input-icon-left"></i>
                    <input type="text" id="verify_middle_name" name="verify_middle_name" placeholder="Enter your middle name">
                </div>

                <div class="input-group">
                    <label for="verify_last_name">Last Name</label>
                    <i class="fa-solid fa-user input-icon-left"></i>
                    <input type="text" id="verify_last_name" name="verify_last_name" placeholder="Enter your last name" required>
                </div>

                <div class="navigation-buttons">
                    <button type="button" id="backToStep1FromVerificationBtn" class="back-button">Back</button>
                    <button type="button" id="verifyAlumniBtn" class="next-button">Verify Alumni Status</button>
                </div>
            </div>

            <!-- Step 3: Personal Information -->
            <div class="form-step" id="step2" style="display:none;">
                <h2>Personal Information</h2>
                <p>Tell us about yourself</p>

                <!-- Common fields for all users -->
                <div class="input-group">
                    <label for="register-firstname">First Name</label>
                    <i class="fa-solid fa-user input-icon-left"></i>
                    <input type="text" id="register-firstname" name="first_name" placeholder="Enter your first name" required>
                </div>

                <div class="input-group">
                    <label for="register-middlename">Middle Name</label>
                    <i class="fa-solid fa-user input-icon-left"></i>
                    <input type="text" id="register-middlename" name="middle_name" placeholder="Enter your middle name">
                </div>

                <div class="input-group">
                    <label for="register-lastname">Last Name</label>
                    <i class="fa-solid fa-user input-icon-left"></i>
                    <input type="text" id="register-lastname" name="last_name" placeholder="Enter your last name" required>
                </div>

                <div class="input-group">
                    <label for="register-address">Complete Address</label>
                    <i class="fa-solid fa-map-marker-alt input-icon-left"></i>
                    <input type="text" id="register-address" name="address" placeholder="Enter your complete address" required>
                </div>

                <div class="input-group">
                    <label for="register-telephone">Telephone Number</label>
                    <i class="fa-solid fa-phone-alt input-icon-left"></i>
                    <input type="tel" id="register-telephone" name="telephone" placeholder="Enter your telephone number">
                </div>

                <div class="input-group">
                    <label for="register-phone">Mobile Phone Number</label>
                    <i class="fa-solid fa-mobile-alt input-icon-left"></i>
                    <input type="tel" id="register-phone" name="phone_number" placeholder="Enter your mobile phone number" required>
                </div>

                <div class="input-group">
                    <label for="register-position">Job Position/Role</label>
                    <i class="fa-solid fa-briefcase input-icon-left"></i>
                    <input type="text" id="register-position" name="position" placeholder="Enter your position" required>
                </div>

                <div class="navigation-buttons">
                    <button type="button" id="backToVerifyBtn" class="back-button">Back</button>
                    <button type="button" id="proceedToStep3Btn" class="next-button">Continue</button>
                </div>
            </div>

            <!-- Step 4: Email & Password -->
            <div class="form-step" id="step3" style="display:none;">
                <h2>Create Account</h2>
                <p>Enter your email and create a password</p>

                <div class="input-group">
                    <label for="register-email">Email Address</label>
                    <i class="fa-solid fa-envelope input-icon-left"></i>
                    <input type="email" id="register-email" name="email" placeholder="your.email@example.com" required>
                </div>

                <div class="input-group">
                    <label for="register-password">Password</label>
                    <i class="fa-solid fa-lock input-icon-left"></i>
                    <input type="password" id="register-password" name="password" placeholder="Create a password" required>
                    <i class="fa-solid fa-eye password-toggle" id="passwordToggle"></i>
                </div>

                <div class="input-group">
                    <label for="register-confirmPassword">Confirm Password</label>
                    <i class="fa-solid fa-lock input-icon-left"></i>
                    <input type="password" id="register-confirmPassword" name="confirm_password" placeholder="Confirm your password" required>
                    <i class="fa-solid fa-eye password-toggle" id="confirmPasswordToggle"></i>
                </div>
                <div class="navigation-buttons">
                    <button type="button" id="backToStep2Btn" class="back-button">Back</button>
                    <button type="button" id="sendCodeBtn" class="next-button">Send Verification Code</button>
                </div>


            </div>
            <div class="login-link">
                Already have an account? <a href="?Cavite-State-University=login">Login here</a>
            </div>
            <div class="legal-links">
                By signing in or creating an account, you agree to our
                <a href="?Cavite-State-University=terms-and-conditions">Terms & Conditions</a> and
                <a href="?Cavite-State-University=privacy-policy">Privacy Policy</a>
            </div>
        </form>


    </div>

    <!-- Include your updated JavaScript file -->
    <script src="/Alumni-CvSU/admin/portal/new-register/register.js"></script>
</body>

</html>