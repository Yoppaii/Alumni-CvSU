<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News and Features</title>
</head>
    <style>
        .news-hero {
            color: white;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;  
            overflow: hidden; 
            min-height: 300px;
            height: 500px;
        }

        .news-hero h2 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }

        .news-hero p {
            font-size: 16px;
            margin-bottom: 20px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .news-hero .news-btn {
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

        .news-hero .news-btn:hover {
            background: #92940e;
            transform: translateX(-50%) translateY(-2px);
        }

        .news-hero::after {
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .news-hero {
                padding: 60px 15px;
                min-height: 250px;
            }

            .news-hero h2 {
                font-size: 28px;
            }

            .news-hero p {
                font-size: 15px;
                max-width: 100%;
                padding: 0 10px;
            }

            .news-hero .news-btn {
                padding: 10px 20px;
                font-size: 15px;
                bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .news-hero {
                padding: 50px 10px;
                min-height: 220px;
            }

            .news-hero h2 {
                font-size: 24px;
            }

            .news-hero p {
                font-size: 14px;
                line-height: 1.5;
            }

            .news-hero .news-btn {
                padding: 8px 18px;
                font-size: 14px;
            }
        }
    </style>
<body>
    <section class="news-hero">
        <h2>Latest News and Features</h2>
        <p>Stay updated with the latest happenings, alumni success stories, and university developments at Cavite State University.</p>
        <!--<a href="#news" class="news-btn">View All News</a>-->
    </section>
    
    <?php include('components/news/news.php'); ?>

</body>
</html>