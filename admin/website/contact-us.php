<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Alumni Cavite State University</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="asset/css/button-up-css/up_buttonss.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --cvsu-primary-green: #006400;
            --cvsu-text-light: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            line-height: 1.6;
        }

        .TER-main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .TER-content-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin: 20px;
        }

        .TER-header-block {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #006400;
        }

        .TER-main-title {
            color: #006400;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .contact-content {
            display: flex;
            gap: 2rem;
        }

        .map-container {
            flex: 1;
        }

        .map-container iframe {
            width: 100%;
            height: 450px;
            border: 0;
            border-radius: 8px;
        }

        .contact-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-details h2 {
            color: #006400;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 0.5rem;
        }

        .contact-details p {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .contact-details i {
            margin-right: 10px;
            color: #006400;
            width: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .contact-content {
                flex-direction: column;
            }

            .map-container iframe {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="TER-main-container">
        <div class="TER-content-wrapper">
            <div class="TER-header-block">
                <h1 class="TER-main-title">Contact Us</h1>
            </div>

            <div class="contact-content">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d966.9946522105737!2d120.87997327221373!3d14.196030900000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd81e7a0fe7653%3A0xc55eb1a0b24cd16b!2sCavite%20State%20University%20-%20Main%20Campus!5e0!3m2!1sen!2sph!4v1731636674114!5m2!1sen!2sph" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="contact-details">
                    <h2>Location & Contact Information</h2>
                    <p><i class="fas fa-map-marker-alt"></i> Cavite State University Main Campus</p>
                    <p><i class="fas fa-map-marker-alt"></i> Indang-Trece Martires Road 4122 Indang</p>
                    <p><i class="fas fa-envelope"></i> alumniaffairs@cvsu.edu.ph</p>
                    <p><i class="fas fa-clock"></i> Service Hours: Monday to Friday (8:30 AM - 5:30 PM)</p>
                    <p><i class="fas fa-clock"></i> Saturday (8:30 AM - 12:30 PM)</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>