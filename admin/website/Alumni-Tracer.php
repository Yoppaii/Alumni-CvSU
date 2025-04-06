<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracer</title>
</head>
    <style>
        .ats-hero {
            color: white;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;  
            overflow: hidden; 
            min-height: 300px;
            height: 500px;
        }

        .ats-hero h2 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }

        .ats-hero p {
            font-size: 16px;
            margin-bottom: 20px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .ats-hero .cta-btn {
            padding: 12px 25px;
            background:#00b100;
            color: white;
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

        .ats-hero .cta-btn:hover {
            background: #004d00;
            transform: translateX(-50%) translateY(-2px);
        }

        .ats-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 500px;
            background-image: url('asset/images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(4px) brightness(0.5); 
            z-index: 1;
        }

        @media (max-width: 768px) {
            .ats-hero {
                padding: 60px 15px;
                min-height: 250px;
            }

            .ats-hero h2 {
                font-size: 28px;
            }

            .ats-hero p {
                font-size: 15px;
                max-width: 100%;
                padding: 0 10px;
            }

            .ats-hero .cta-btn {
                padding: 10px 20px;
                font-size: 15px;
                bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .ats-hero {
                padding: 50px 10px;
                min-height: 220px;
            }

            .ats-hero h2 {
                font-size: 24px;
            }

            .ats-hero p {
                font-size: 14px;
                line-height: 1.5;
            }

            .ats-hero .cta-btn {
                padding: 8px 18px;
                font-size: 14px;
            }
        }
    </style>
<body>
    <section class="ats-hero">
        <h2>Welcome to Alumni Statistics</h2>
        <p>The alumni data results can be viewed by querying the database to display relevant metrics, such as graduation year, employment status, and field of expertise.</p>
        <a href="#services" class="cta-btn">Explore Alumni Statistics</a>
    </section>

    <?php include('components/alumni_tracer/alumni-track-tracer.php'); ?>

</body>
</html>