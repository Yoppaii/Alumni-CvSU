<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment</title>
</head>
<style>
    .car-hero {
        color: white;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        min-height: 300px;
        height: 500px;
    }

    .car-hero h2 {
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
        position: relative;
        z-index: 2;
    }

    .car-hero p {
        font-size: 16px;
        margin-bottom: 20px;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
        position: relative;
        z-index: 2;
    }

    .car-hero .car-btn {
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

    .car-hero .car-btn:hover {
        background: #92940e;
        transform: translateX(-50%) translateY(-2px);
    }

    .car-hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        height: 500px;
        background-image: url('asset/images/bg1.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        filter: brightness(0.4);
        z-index: 1;
    }

    @media (max-width: 768px) {
        .car-hero {
            padding: 60px 15px;
            min-height: 250px;
        }

        .car-hero h2 {
            font-size: 28px;
        }

        .car-hero p {
            font-size: 15px;
            max-width: 100%;
            padding: 0 10px;
        }

        .car-hero .car-btn {
            padding: 10px 20px;
            font-size: 15px;
            bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .car-hero {
            padding: 50px 10px;
            min-height: 220px;
        }

        .car-hero h2 {
            font-size: 24px;
        }

        .car-hero p {
            font-size: 14px;
            line-height: 1.5;
        }

        .car-hero .car-btn {
            padding: 8px 18px;
            font-size: 14px;
        }
    }
</style>

<body>
    <section class="car-hero">
        <h2>Career Opportunities & Job Portal</h2>
        <p>Discover exciting career opportunities and job listings available for CvSU alumni. Access valuable professional development resources to enhance your skills. Stay connected and take the next step in your career journey!</p>
        <a href="#services" class="car-btn" id="scroll-btn">Browse Jobs</a>
    </section>

    <section id="services">


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


        <?php include('components/career/employment-data.php'); ?>

</body>

</html>