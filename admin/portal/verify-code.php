<?php
require_once 'main_db.php';
$response = ['status' => 'error', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['otp']) && count($_POST['otp']) === 6) {
        $otp = implode('', $_POST['otp']);
        if (isset($_COOKIE['otp']) && $otp === $_COOKIE['otp']) {
            $email = $_COOKIE['email'];
            $password = $_COOKIE['password'];
            // Start a transaction to ensure data consistency
            $mysqli->begin_transaction();
            try {
                // Insert into users table
                $stmt = $mysqli->prepare("INSERT INTO `users`(`email`, `password`) VALUES (?, ?)");
                $stmt->bind_param("ss", $email, $password);
                if ($stmt->execute()) {
                    $userId = $mysqli->insert_id; // Get the newly inserted user ID

                    // Check if user profile data exists in cookies with default values if not set
                    $first_name = isset($_COOKIE['firstName']) ? $_COOKIE['firstName'] : '';
                    $last_name = isset($_COOKIE['lastName']) ? $_COOKIE['lastName'] : '';
                    $middle_name = isset($_COOKIE['middleName']) ? $_COOKIE['middleName'] : '';
                    $position = isset($_COOKIE['position']) ? $_COOKIE['position'] : '';
                    $address = isset($_COOKIE['address']) ? $_COOKIE['address'] : '';
                    $telephone = isset($_COOKIE['telephone']) ? $_COOKIE['telephone'] : '';
                    $phone_number = isset($_COOKIE['phoneNumber']) ? $_COOKIE['phoneNumber'] : '';
                    $user_status = isset($_COOKIE['userType']) ? $_COOKIE['userType'] : '';
                    $verified = 1;

                    // For Alumni users, get the alumni ID card number
                    $alumni_id_card_no = '';
                    if ($user_status === 'Alumni') {
                        $alumni_id_card_no = isset($_COOKIE['alumniIdCardNo']) ? $_COOKIE['alumniIdCardNo'] : '';
                    }

                    // Insert into user table with alumni_id_card_no if applicable
                    if ($user_status === 'Alumni') {
                        $sql = "INSERT INTO `user`(`user_id`, `alumni_id_card_no`, `first_name`, `last_name`, `middle_name`, `position`, `address`, `telephone`, `phone_number`, `user_status`, `verified`) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("isssssssssi", $userId, $alumni_id_card_no, $first_name, $last_name, $middle_name, $position, $address, $telephone, $phone_number, $user_status, $verified);

                        $update_sql = "UPDATE `alumni` SET `verify` = 'used' WHERE `alumni_id_card_no` = ?";
                        $update_stmt = $mysqli->prepare($update_sql);
                        $update_stmt->bind_param("s", $alumni_id_card_no);
                        $update_stmt->execute();
                    } else {
                        // For Guest and other user types, don't include alumni_id_card_no
                        $sql = "INSERT INTO `user`(`user_id`, `first_name`, `last_name`, `middle_name`, `position`, `address`, `telephone`, `phone_number`, `user_status`, `verified`) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("issssssssi", $userId, $first_name, $last_name, $middle_name, $position, $address, $telephone, $phone_number, $user_status, $verified);
                    }

                    $stmt->execute();

                    $mysqli->commit();

                    // Clear all cookies
                    setcookie('otp', '', time() - 3600, '/');
                    setcookie('email', '', time() - 3600, '/');
                    setcookie('password', '', time() - 3600, '/');
                    setcookie('firstName', '', time() - 3600, '/');
                    setcookie('lastName', '', time() - 3600, '/');
                    setcookie('middleName', '', time() - 3600, '/');
                    setcookie('position', '', time() - 3600, '/');
                    setcookie('address', '', time() - 3600, '/');
                    setcookie('telephone', '', time() - 3600, '/');
                    setcookie('phoneNumber', '', time() - 3600, '/');
                    setcookie('userType', '', time() - 3600, '/');

                    // Clear Alumni specific cookie
                    if ($user_status === 'Alumni') {
                        setcookie('alumniIdCardNo', '', time() - 3600, '/');
                    }

                    $response['status'] = 'success';
                    $response['message'] = 'Successfully registered. Redirecting to the homepage.';
                } else {
                    throw new Exception('Failed to register user.');
                }
            } catch (Exception $e) {
                $mysqli->rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid OTP.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'OTP must be 6 digits long.';
    }
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - CvSU</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --login-primary: #006400;
            --login-secondary: #008000;
            --login-accent: #90EE90;
            --login-text-light: #ffffff;
            --login-text-dark: #333333;
            --login-gray-light: #f5f5f5;
            --login-border-color: rgba(0, 0, 0, 0.1);
            --login-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --login-shadow-md: 0 2px 5px rgba(0, 0, 0, 0.1);
            --login-transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

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

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

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

        .container {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            background: var(--login-text-light);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--login-shadow-md);
        }



        .verify-form-container {
            padding: 40px;
            background: var(--login-text-light);
        }

        .top-title {
            font-size: 1.75rem;
            color: var(--login-text-dark);
            margin-bottom: 8px;
            text-align: center;
            font-weight: 600;
        }

        .description {
            color: #666;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .verification-code-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 25px;
        }

        .code-input {
            width: 45px;
            height: 45px;
            border: 1px solid var(--login-border-color);
            border-radius: 6px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            transition: var(--login-transition);
        }

        .code-input:focus {
            outline: none;
            border-color: var(--login-primary);
            box-shadow: 0 0 0 3px rgba(0, 100, 0, 0.1);
        }

        .submit-button {
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
            margin-bottom: 15px;
        }

        .submit-button:hover {
            background: var(--login-secondary);
        }

        .timer {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .resend-code {
            display: block;
            text-align: center;
            color: var(--login-primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--login-transition);
        }

        .resend-code:hover {
            color: var(--login-secondary);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            background: var(--login-text-light);
            border-radius: 6px;
            box-shadow: var(--login-shadow-sm);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            animation: slideIn 0.3s ease-out;
            z-index: 1000;
            border-left: 4px solid;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
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

        @media (max-width: 768px) {
            .container {
                max-width: 400px;
                grid-template-columns: 1fr;
            }



            .verify-form-container {
                padding: 30px;
            }
        }

        @media (max-width: 480px) {
            .container {
                max-width: 100%;
            }

            .verify-form-container {
                padding: 20px;
            }

            .verification-code-container {
                gap: 5px;
            }

            .code-input {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>

<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="container">
        <div class="verify-form-container">
            <div class="top-title">Enter Verification Code</div>
            <p class="description">
                We've sent a verification code to your email address.<br>
                Please enter the code below.
            </p>
            <form id="otpForm" action="verify-code.php" method="POST">
                <div class="verification-code-container">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" name="otp[]" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" name="otp[]" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" name="otp[]" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" name="otp[]" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" name="otp[]" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" name="otp[]" required>
                </div>
                <button type="submit" class="submit-button">Verify Code</button>
                <div class="timer">Code expires in: <span id="countdown">05:00</span></div>
                <a href="#" class="resend-code" id="resendCode">Resend Code</a>
            </form>
        </div>
    </div>
    <script>
        const inputs = document.querySelectorAll('.code-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value) {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });
        });

        function startTimer(duration, display) {
            let timer = duration,
                minutes, seconds;
            let countdown = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                display.textContent = minutes + ":" + seconds;
                if (--timer < 0) {
                    clearInterval(countdown);
                    display.textContent = "00:00";
                    document.getElementById('resendCode').style.display = 'inline-block';
                }
            }, 1000);
        }
        window.onload = function() {
            let fiveMinutes = 60 * 5,
                display = document.querySelector('#countdown');
            startTimer(fiveMinutes, display);
        };

        document.getElementById('otpForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'flex';

            const formData = new FormData(this);

            fetch('?Cavite-State-University=verify', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const notificationContainer = document.getElementById('notificationContainer');
                    const notification = document.createElement('div');
                    notification.classList.add('notification');

                    if (data.status === 'success') {
                        notification.style.borderLeftColor = '#4CAF50';
                        notification.innerHTML = data.message;
                        notificationContainer.appendChild(notification);

                        setTimeout(() => {
                            window.location.href = '?Cavite-State-University=login';
                        }, 3000);
                    } else {
                        loadingOverlay.style.display = 'none';
                        notification.style.borderLeftColor = '#f44336';
                        notification.innerHTML = data.message;
                        notificationContainer.appendChild(notification);

                        setTimeout(() => {
                            notification.style.animation = 'fadeOut 5s forwards';
                        }, 5000);

                        setTimeout(() => {
                            notification.remove();
                        }, 5500);
                    }
                })
                .catch(error => {
                    loadingOverlay.style.display = 'none';
                    const notificationContainer = document.getElementById('notificationContainer');
                    const notification = document.createElement('div');
                    notification.classList.add('notification');
                    notification.style.borderLeftColor = '#f44336';
                    notification.innerHTML = 'An error occurred. Please try again.';
                    notificationContainer.appendChild(notification);

                    setTimeout(() => {
                        notification.style.animation = 'fadeOut 5s forwards';
                    }, 5000);

                    setTimeout(() => {
                        notification.remove();
                    }, 5500);
                });
        });
    </script>
</body>

</html>