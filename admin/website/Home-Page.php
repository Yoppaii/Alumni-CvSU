<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Cavite State University</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

</head>
<style>
    .herodaw {
        color: white;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        min-height: 300px;
        height: 500px;
    }

    .herodaw h2 {
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
        position: relative;
        z-index: 2;
    }

    .herodaw p {
        font-size: 16px;
        margin-bottom: 20px;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
        position: relative;
        z-index: 2;
    }

    .herodaw .cta-btn {
        padding: 12px 25px;
        background: #006400;
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

    .herodaw .cta-btn:hover {
        background: #004d00;
        transform: translateX(-50%) translateY(-2px);
    }

    .herodaw::after {
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
        filter: brightness(0.4);
        z-index: 1;

    }

    @media (max-width: 768px) {
        .herodaw {
            padding: 60px 15px;
            min-height: 250px;
        }

        .herodaw h2 {
            font-size: 28px;
        }

        .herodaw p {
            font-size: 15px;
            max-width: 100%;
            padding: 0 10px;
        }

        .herodaw .cta-btn {
            padding: 10px 20px;
            font-size: 15px;
            bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .herodaw {
            padding: 50px 10px;
            min-height: 220px;
        }

        .herodaw h2 {
            font-size: 24px;
        }

<<<<<<< HEAD
        .herodaw::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 700px;
            background-image: url('asset/images/bground.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.4); 
            z-index: 1;
            
=======
        .herodaw p {
            font-size: 14px;
            line-height: 1.5;
>>>>>>> upstream/main
        }

        .herodaw .cta-btn {
            padding: 8px 18px;
            font-size: 14px;
        }
    }
}
</style>

<body>
    <section class="herodaw">
        <h2>Welcome to Office of Alumni Affairs</h2>
        <p>Your easy solution for booking meeting rooms, events, and conferences at Cavite State University.</p>
        <a href="#services" class="cta-btn">Explore Our Services</a>
    </section>

    <?php include('components/home/announcement.php'); ?>
    <?php include('components/home/features.php'); ?>
</body>

</html>