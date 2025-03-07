<?php
session_start();
require('main_db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $created_at = date('Y-m-d H:i:s');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        $check_query = "SELECT * FROM it_support WHERE email = '$email'";
        $result = mysqli_query($con, $check_query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Email already exists";
        } else {
            $insert_query = "INSERT INTO it_support (email, password, role, created_at) 
                           VALUES ('$email', '$password', '$role', '$created_at')";
            
            if (mysqli_query($con, $insert_query)) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Register</h2>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" onsubmit="return validateForm()">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                    <select name="role" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Select Role</option>
                        <option value="it_support">IT Support</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200">
                    Register
                </button>

                <div class="mt-4 text-center">
                    <a href="login.php" class="text-blue-500 hover:text-blue-600">Already have an account? Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;
            
            if (password != confirm_password) {
                alert("Passwords do not match!");
                return false;
            }
            
            if (password.length < 8) {
                alert("Password must be at least 8 characters long!");
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>