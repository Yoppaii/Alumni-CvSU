<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Network</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .cvsu-event-hero {
            color: white;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;  
            overflow: hidden; 
            min-height: 300px;
            height: 500px;
        }

        .cvsu-event-hero h2 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }

        .cvsu-event-hero p {
            font-size: 16px;
            margin-bottom: 20px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .cvsu-event-hero .cvsu-cta-btn {
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

        .cvsu-event-hero .cvsu-cta-btn:hover {
            background: #92940e;
            transform: translateX(-50%) translateY(-2px);
        }

        .cvsu-event-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 500px;
            background-image: url('asset/images/res.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            filter: brightness(0.4);
            z-index: 1;
        }

        @media (max-width: 768px) {
            .cvsu-event-hero {
                padding: 60px 15px;
                min-height: 250px;
            }
            .cvsu-event-hero h2 {
                font-size: 28px;
            }
            .cvsu-event-hero p {
                font-size: 15px;
                max-width: 100%;
                padding: 0 10px;
            }
            .cvsu-event-hero .cvsu-cta-btn {
                padding: 10px 20px;
                font-size: 15px;
                bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .cvsu-event-hero {
                padding: 50px 10px;
                min-height: 220px;
            }
            .cvsu-event-hero h2 {
                font-size: 24px;
            }
            .cvsu-event-hero p {
                font-size: 14px;
                line-height: 1.5;
            }
            .cvsu-event-hero .cvsu-cta-btn {
                padding: 8px 18px;
                font-size: 14px;
            }
        }

        section {
            padding: 40px;
            background: #f9f9f9;
            text-align: center;
        }
    </style>
</head>
<body>
    <section class="cvsu-event-hero">
        <h2>CvSU Campus Satellite Network</h2>
        <p>Explore our diverse network of Cavite State University campuses, each offering a wealth of unique programs and opportunities for academic excellence. From cutting-edge research facilities to specialized courses tailored to meet the needs of various industries, our campuses provide a rich learning environment. Whether you're interested in agriculture, engineering, business, or the arts, you’ll find a program that aligns with your career goals. Join us in shaping the future and enhancing your educational journey across the province!</p>
        <a href="#services" class="cvsu-cta-btn" id="scroll-btn">View Campuses</a>
    </section>

    <section id="services">

    </section>

    <script>
    document.getElementById('scroll-btn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        
        const targetId = this.getAttribute('href'); // Get the target section ID
        const targetElement = document.querySelector(targetId); // Select the target element

        // Scroll to the target element smoothly
        targetElement.scrollIntoView({
            behavior: 'smooth' // Enable smooth scrolling
        });
    });
    </script>

    <?php include('components/network/network.php'); ?>

</body>
</html>