<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <link rel="icon" href="/RS/CvSU/bg/res1.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Roboto', 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #388e3c, #ffffff);
            color: #2c6b2f;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }

        .container {
            width: 90%;
            max-width: 600px;
            padding: 20px;
            box-sizing: border-box;
        }

        .top-title {
            font-size: clamp(24px, 5vw, 32px);
            font-weight: 500;
            color: rgb(255, 255, 255);
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .description {
            color: white;
            font-size: clamp(14px, 2.5vw, 16px);
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .verification-code-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 30px;
        }

        .code-input {
            width: 60px;
            height: 70px;
            font-size: 24px;
            text-align: center;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .code-input:focus {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            outline: none;
            transform: translateY(-2px);
        }

        .submit-button {
            background-color: #fff;
            color: #388e3c;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            padding: 16px;
            font-size: clamp(16px, 2.5vw, 18px);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .submit-button:hover {
            background-color: #f8f8f8;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            transform: translateY(-2px);
        }

        .resend-code {
            color: green;
            font-size: clamp(14px, 2.5vw, 16px);
            margin-top: 20px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .resend-code:hover {
            text-decoration: underline;
        }

        .timer {
            color: green;
            font-size: clamp(14px, 2.5vw, 16px);
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }
            
            .code-input {
                width: 50px;
                height: 60px;
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 100%;
                padding: 10px;
            }
            
            .code-input {
                width: 40px;
                height: 50px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-title">Enter Verification Code</div>
        <p class="description">
            We've sent a verification code to your email address.<br>
            Please enter the code below.
        </p>
        <form action="verify-code.php" method="POST">
            <div class="verification-code-container">
                <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
            </div>
            <button type="submit" class="submit-button">Verify Code</button>
            <div class="timer">Code expires in: <span id="countdown">05:00</span></div>
            <a href="#" class="resend-code" id="resendCode">Resend Code</a>
        </form>
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
            let timer = duration, minutes, seconds;
            let countdown = setInterval(function () {
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

        window.onload = function () {
            let fiveMinutes = 60 * 5,
                display = document.querySelector('#countdown');
            startTimer(fiveMinutes, display);
        };
    </script>
</body>
</html>