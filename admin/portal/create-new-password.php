<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CvSU</title>
    <link rel="icon" href="/RS/CvSU/bg/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="/RS/portal/css/create-new-password.css">
</head>
<body>
    <div class="container">
        <div class="top-title">Reset Password</div>
        <p class="description">Create a new password for your account</p>

        <form id="resetPasswordForm" action="update-password.php" method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <input type="password" id="newPassword" name="newPassword" required placeholder=" ">
                <label for="newPassword">New Password</label>
                <div class="error-message" id="passwordError"></div>
            </div>

            <div class="input-group">
                <input type="password" id="confirmPassword" name="confirmPassword" required placeholder=" ">
                <label for="confirmPassword">Confirm New Password</label>
                <div class="error-message" id="confirmPasswordError"></div>
            </div>

            <div class="requirements">
                Password Requirements:
                <ul>
                    <li id="lengthReq" class="requirement-unmet">At least 8 characters long</li>
                    <li id="upperReq" class="requirement-unmet">Contains uppercase letter</li>
                    <li id="lowerReq" class="requirement-unmet">Contains lowercase letter</li>
                    <li id="numberReq" class="requirement-unmet">Contains number</li>
                    <li id="specialReq" class="requirement-unmet">Contains special character</li>
                </ul>
            </div>

            <button type="submit" class="reset-button">Reset Password</button>
        </form>
    </div>

    <script>
        const password = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');

        function checkPassword(password) {
            const requirements = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*]/.test(password)
            };

            document.getElementById('lengthReq').className = requirements.length ? 'requirement-met' : 'requirement-unmet';
            document.getElementById('upperReq').className = requirements.upper ? 'requirement-met' : 'requirement-unmet';
            document.getElementById('lowerReq').className = requirements.lower ? 'requirement-met' : 'requirement-unmet';
            document.getElementById('numberReq').className = requirements.number ? 'requirement-met' : 'requirement-unmet';
            document.getElementById('specialReq').className = requirements.special ? 'requirement-met' : 'requirement-unmet';

            return Object.values(requirements).every(Boolean);
        }

        password.addEventListener('input', function() {
            checkPassword(this.value);
        });

        confirmPassword.addEventListener('input', function() {
            if (this.value !== password.value) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                document.getElementById('confirmPasswordError').style.display = 'block';
            } else {
                document.getElementById('confirmPasswordError').style.display = 'none';
            }
        });

        function validateForm() {
            let isValid = true;
            
            if (!checkPassword(password.value)) {
                document.getElementById('passwordError').textContent = 'Password does not meet requirements';
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            }

            if (password.value !== confirmPassword.value) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                document.getElementById('confirmPasswordError').style.display = 'block';
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>