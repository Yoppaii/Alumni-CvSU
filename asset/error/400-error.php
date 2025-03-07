<?php
http_response_code(400); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 Error - Bad Request</title>
    <?php include('../../asset/OOP/error-link/error-link.php'); ?>
</head>
<body>

    <?php include('../../asset/OOP/error-link/error-nav.php'); ?>

    <div class="error-container">
        <h1><i class="fas fa-exclamation-triangle"></i> 400</h1>
        <p class="error-message">Oops! The request could not be understood by the server.</p>
        <p class="error-suggestion">It seems like there was a bad request. Please try the following:</p>
        <div class="error-suggestion-links">
            <a href="/Alumni-CvSU/index" class="error-home-button"><i class="fas fa-home"></i> Go to Home</a>
            <a href="/contact" class="error-contact-button"><i class="fas fa-phone-alt"></i> Contact Us</a>
        </div>
    </div>

    <?php include('../../asset/OOP/error-link/error-footer.php'); ?>

</body>
</html>