<?php
// Include database connection
require_once '../../main_db.php';

// Initialize variables
$message = "";
$success = false;

// Process form submission
if (isset($_POST['register'])) {
    try {
        // Get current date and time
        $currentDateTime = date('Y-m-d H:i:s');

        // Hash the password - using fixed 'admin' password
        $password_hash = password_hash('admin', PASSWORD_DEFAULT);

        // Prepare SQL statement
        $stmt = $mysqli->prepare("INSERT INTO `admin_users`
                              (`admin_id`, `username`, `first_name`, `last_name`, `email`, 
                               `password_hash`, `role`, `is_active`, `last_login`, 
                               `created_at`, `updated_at`) 
                              VALUES 
                              (NULL, ?, ?, ?, ?, 
                               ?, ?, ?, NULL, 
                               ?, ?)");

        // Set is_active to 1 (true)
        $is_active = 1;

        // Bind parameters
        $stmt->bind_param(
            "sssssssss",
            $_POST['username'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $password_hash,
            $_POST['role'],
            $is_active,
            $currentDateTime,
            $currentDateTime
        );

        // Execute the statement
        $stmt->execute();

        // Check if the insertion was successful
        if ($stmt->affected_rows > 0) {
            $message = "Admin user created successfully!";
            $success = true;
        } else {
            $message = "Error: Admin user could not be created.";
        }

        // Close statement
        $stmt->close();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>Register Admin User</h2>

    <?php if (!empty($message)): ?>
        <div class="<?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="admin" required>
        </div>

        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="bahayngalumni.reservations@gmail.com" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>

        <button type="submit" name="register">Register Admin</button>
    </form>
</body>

</html>