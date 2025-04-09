<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event</title>
</head>
    <style>
        .ev-hero {
            color: white;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;  
            overflow: hidden; 
            min-height: 300px;
            height: 500px;
        }

        .ev-hero h2 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }

        .ev-hero p {
            font-size: 16px;
            margin-bottom: 20px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .ev-hero .cta-btn {
            padding: 12px 25px;
            background: gold;
            color: black;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 40px;
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            border: 2px solid transparent;
        }

        .ev-hero .cta-btn:hover {
            background: #92940e;
            transform: translateX(-50%) translateY(-2px);
        }

        .ev-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 500px;
            background-image: url('asset/images/bground.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            filter: brightness(0.4);
            z-index: 1;
        }

        @media (max-width: 768px) {
            .ev-hero {
                padding: 60px 15px;
                min-height: 250px;
            }

            .ev-hero h2 {
                font-size: 28px;
            }

            .ev-hero p {
                font-size: 15px;
                max-width: 100%;
                padding: 0 10px;
            }

            .ev-hero .cta-btn {
                padding: 10px 20px;
                font-size: 15px;
                bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .ev-hero {
                padding: 50px 10px;
                min-height: 220px;
            }

            .ev-hero h2 {
                font-size: 24px;
            }

            .ev-hero p {
                font-size: 14px;
                line-height: 1.5;
            }

            .ev-hero .cta-btn {
                padding: 8px 18px;
                font-size: 14px;
            }
        }
    </style>
<body>
    <section class="ev-hero">
        <h2>CvSU Alumni Events & Activities</h2>
        <p>Stay connected with alumni gatherings, workshops, and special events at Cavite State University. Join us to network with fellow graduates and share experiences. Don’t miss out on opportunities to engage and grow within our vibrant community!</p>
        <a href="#services" class="cta-btn">View Events</a>
    </section>

    <?php include('components/events/events.php'); ?>

</body>
</html>