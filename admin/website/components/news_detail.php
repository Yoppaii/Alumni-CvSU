<?php 
require('../main_db.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $newsId = $_GET['id'];

    $stmt = $mysqli->prepare("SELECT title, description, author, date FROM news_posts WHERE id = ?");
    $stmt->bind_param("i", $newsId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $news = $result->fetch_assoc();
    } else {
        echo "<p>News post not found.</p>";
        exit;
    }

    $stmt->close();
} else {
    echo "<p>Invalid news ID.</p>";
    exit;
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News and Features</title>
    <link rel="icon" href="bg/res1.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="design/hero.css">
    <link rel="stylesheet" href="design/index_wat.css">
    <style>
        .overlay {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2em;
            color: #388e3c;
        }

        .meta {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 20px;
        }

        .content {
            font-size: 1.1em;
            color: #333;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #388e3c;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-link:hover {
            background: #2e7d32;
        }

        .description img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <img src="bg/res1.png" alt="Logo">
            <h1>Alumni Cavite State University</h1>
        </div>
        <div class="hamburger" id="hamburger">&#9776;</div>
        <div class="nav-btns">
            <a href="#" class="openLogin">LOG IN</a>
            <a href="#" class="openRegister">REGISTER</a>
        </div>
    </nav>
    <div class="top-menu" id="topMenu">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="online_services.php">Online Services</a></li>
            <li><a href="#">Alumni Tracer</a></li>
            <li><a href="#">Employment</a></li>
            <li><a href="event.php">Events</a></li>
            <li><a href="news_features.php">News and Features</a></li>
            <li><a href="#">OAA Publications</a></li>
            <li><a href="#">Satellite Campus</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="#" class="openLogin">LOG IN</a></li>
            <li><a href="#" class="openRegister">REGISTER</a></li>
        </ul>
    </div>

    <div id="loginOverlay" class="overlay">
        <?php include('login.php'); ?>
    </div>

    <div id="registerOverlay" class="overlay">
        <?php include('register.php'); ?>
    </div>

    <title><?php echo htmlspecialchars($news['title']); ?></title>

    <div class="container">
        <h1><?php echo htmlspecialchars($news['title']); ?></h1>
        <p class="meta">
            By <?php echo htmlspecialchars($news['author']); ?> | 
            <?php echo date("F j, Y, g:i a", strtotime($news['date'])); ?>
        </p>
        
        <div class="content">
            <?php 
            $description = str_replace('uploads/', '../.cache/uploads/', $news['description']);
            echo $description;
            ?>
        </div>

        <a href="news_features.php" class="back-link">Back to News</a>
    </div>


    <footer>
        <h2>Follow Us</h2>
        <div class="social-links">
            <a href="https://www.facebook.com/@cvsuoaa" target="_blank" class="fab fa-facebook"></a>
            <a href="https://twitter.com/AlumniCvSU" target="_blank" class="fab fa-twitter"></a>
            <a href="https://www.instagram.com/AlumniCvSU" target="_blank" class="fab fa-instagram"></a>
        </div>
        <div class="footer-content">
            <p>&copy; 2024 Alumni Cavite State University. All rights reserved.</p>
        </div>
    </footer>  

    <script src="functions/index_burger.js"></script>
    <script src="functions/log_reg.js"></script>
    <script src="functions/switch_form.js"></script>
</body>
</html>
