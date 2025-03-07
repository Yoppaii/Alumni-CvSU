<?php
require_once('main_db.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumni_id_card_no = $_POST['alumni_id_card_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT * FROM alumni WHERE 
            alumni_id_card_no = ? AND 
            last_name = ? AND 
            first_name = ?";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $alumni_id_card_no, $last_name, $first_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Alumni found, store verification data in session
        $alumni = $result->fetch_assoc();
        $_SESSION['verified_alumni_id'] = $alumni['alumni_id'];
        $_SESSION['alumni_id_card_no'] = $alumni['alumni_id_card_no'];
        
        // Redirect to re-apply-account page
        header("Location: ?section=re-apply-account");
        exit();
    } else {
        $error_message = "Invalid credentials. Please check your information and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Verification</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2d6936;
            --secondary-color: #1e40af;
            --background-color: #f4f6f8;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            padding: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .verification-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
            max-width: auto;
            margin: 20px auto;
        }

        .verification-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .verification-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .verification-header h1 i {
            color: var(--primary-color);
        }

        .verification-section {
            padding: 24px;
        }

        .verification-section p {
            margin-bottom: 20px;
            color: #4B5563;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            font-size: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: #f9fafb;
            transition: border-color 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 105, 54, 0.1);
        }

        .verify-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            width: 100%;
            justify-content: center;
        }

        .verify-btn:hover {
            background-color: #1f5427;
        }

        .error-message {
            color: #dc2626;
            padding: 10px;
            margin-bottom: 16px;
            background-color: #fee2e2;
            border-radius: 6px;
            border: 1px solid #fecaca;
            display: <?php echo empty($error_message) ? 'none' : 'block'; ?>;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .verification-header,
            .verification-section {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <div class="verification-header">
            <h1><i class="fas fa-user-check"></i> Alumni Verification</h1>
        </div>
        <div class="verification-section">
            <p>Please enter your Alumni ID Card Number, Last Name, and First Name to verify your identity.</p>
            
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="alumni_id_card_no">Alumni ID Card Number</label>
                    <input class="form-input" type="text" id="alumni_id_card_no" name="alumni_id_card_no" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input class="form-input" type="text" id="last_name" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name</label>
                    <input class="form-input" type="text" id="first_name" name="first_name" required>
                </div>
                
                <button type="submit" class="verify-btn">
                    <i class="fas fa-check-circle"></i> Verify
                </button>
            </form>
        </div>
    </div>
</body>
</html>