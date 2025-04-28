<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
</head>
<style>
    :root {
        --primary-green: #2e7d32;
        --hover-green: #1b5e20;
        --light-green: #e8f5e9;
        --gray-light: #f5f5f5;
        --text-dark: #333;
        --gold: #ffd700;
        --gold-hover: #92940e;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 5px 15px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
        --border-radius-sm: 8px;
        --border-radius-md: 10px;
        --border-radius-lg: 25px;
        --spacing-xs: 0.5rem;
        --spacing-sm: 1rem;
        --spacing-md: 1.5rem;
        --spacing-lg: 2rem;
        --font-size-sm: 0.85rem;
        --font-size-base: 1rem;
        --font-size-md: 1.2rem;
        --font-size-lg: 1.5rem;
        --font-size-xl: 2rem;
    }

    /* Common Container Styles */
    .max-width-container {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding: 0 var(--spacing-sm);
    }

    /* Event Hero Section */
    .services-section {
        background-color: var(--gray-light);
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;

    }

    .cvsu-event-hero {
        color: white;
        padding: 80px 20px;
        text-align: center;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
        min-height: 300px;
        height: 500px;
        margin-bottom: var(--spacing-lg);
    }

    .cvsu-event-hero h2 {
        font-size: var(--font-size-xl);
        margin-bottom: 15px;
        font-weight: 700;
        position: relative;
        z-index: 2;
    }

    .cvsu-event-hero p {
        font-size: var(--font-size-base);
        margin-bottom: 20px;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
        position: relative;
        z-index: 2;
    }

    .cvsu-event-hero .cvsu-cta-btn {
        padding: 12px 25px;
        background: var(--gold);
        color: var(--text-dark);
        border-radius: var(--border-radius-lg);
        font-size: var(--font-size-base);
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        margin-top: 40px;
        position: absolute;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        border: 2px solid transparent;
    }

    .cvsu-event-hero .cvsu-cta-btn:hover {
        background: var(--gold-hover);
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
        background-image: url('asset/images/5.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        filter: brightness(0.4);
        z-index: 1;
    }

    /* Organization.php styles */
    .section-titles {
        display: flex;
        justify-content: space-between;
        margin: var(--spacing-lg) auto var(--spacing-sm);
        padding: 0 var(--spacing-sm);
    }

    .section-titles h2 {
        color: var(--primary-green);
        font-size: var(--font-size-xl);
        text-align: center;
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-xs);
        border-bottom: 3px solid var(--primary-green);
        display: block;
        width: 100%;
    }

    .organization-wrapper {
        display: flex;
        gap: var(--spacing-md);
        margin: var(--spacing-sm) auto;
        padding: 0 var(--spacing-sm);
    }

    .organization-container {
        display: block;
        flex: 1;
        background: white;
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-md);
        padding: 5rem;
        animation: fadeIn 0.6s ease-out;
    }

    .content-wrapper {
        display: flex;
        gap: var(--spacing-md);
        align-items: center;
    }

    .text {
        flex: 1;
    }

    .text p {
        color: var(--text-dark);
        font-size: var(--font-size-sm);
        line-height: 1.5;
        margin: 0;
    }

    .text ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .text ul li {
        color: var(--text-dark);
        font-size: var(--font-size-lg);
        line-height: 1.5;
        margin-bottom: 0.6rem;
        padding-left: 2rem;
        position: relative;
    }

    .text ul li::before {
        content: '→';
        color: var(--primary-green);
        position: absolute;
        left: 0;
    }

    /* Info.php styles */
    .info-area {
        margin: var(--spacing-lg) auto;
        padding: 0 var(--spacing-md);
    }

    .info-heading {
        color: var(--primary-green);
        font-size: var(--font-size-xl);
        text-align: center;
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-xs);
        border-bottom: 3px solid var(--primary-green);
    }

    .info-top-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .info-portrait-card {
        background: white;
        padding: var(--spacing-md);
        border-radius: var(--border-radius-sm);
        box-shadow: var(--shadow-sm);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--light-green);
        transition: var(--transition);
    }

    .info-portrait-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .info-portrait-title {
        color: var(--primary-green);
        font-size: var(--font-size-lg);
        margin-bottom: var(--spacing-sm);
        padding-bottom: var(--spacing-xs);
        border-bottom: 2px solid var(--light-green);
        text-align: center;
    }

    .info-portrait-text {
        color: var(--text-dark);
        line-height: 1.6;
        margin-bottom: var(--spacing-sm);
        flex-grow: 1;
    }

    .info-section {
        margin-bottom: 2.5rem;
        background: white;
        padding: var(--spacing-md);
        border-radius: var(--border-radius-sm);
        box-shadow: var(--shadow-sm);
    }

    .info-section-title {
        color: var(--primary-green);
        font-size: var(--font-size-lg);
        margin-bottom: var(--spacing-sm);
        padding-bottom: var(--spacing-xs);
        border-bottom: 2px solid var(--light-green);
    }

    .info-text {
        color: var(--text-dark);
        line-height: 1.6;
        margin-bottom: var(--spacing-sm);
    }

    .info-blockquote {
        margin: var(--spacing-sm) 0;
        padding: var(--spacing-sm) var(--spacing-md);
        background-color: rgba(46, 125, 50, 0.05);
        border-left: 4px solid var(--primary-green);
        font-style: italic;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .info-lang-label {
        color: var(--primary-green);
        display: block;
        margin-top: var(--spacing-sm);
        font-weight: bold;
    }

    .info-values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-md);
        margin-top: var(--spacing-md);
    }

    .info-value-card {
        background: white;
        padding: var(--spacing-md);
        border-radius: var(--border-radius-sm);
        text-align: center;
        transition: var(--transition);
        border: 1px solid var(--light-green);
    }

    .info-value-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .info-value-icon {
        color: var(--primary-green);
        font-size: 2rem;
        margin-bottom: var(--spacing-sm);
    }

    .info-value-title {
        color: var(--primary-green);
        font-size: var(--font-size-md);
        margin-bottom: var(--spacing-xs);
    }

    .info-value-text {
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    .info-quality-section {
        background-color: var(--primary-green);
        color: white;
    }

    .info-quality-section .info-section-title {
        color: white;
        border-bottom-color: rgba(255, 255, 255, 0.2);
    }

    .info-quality-section .info-text {
        color: white;
        margin-bottom: 0;
    }

    /* Responsive styles */
    @media (max-width: 992px) {
        .info-top-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .organization-wrapper {
            flex-direction: column;
        }

        .section-titles {
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .section-titles h2 {
            flex-basis: auto;
        }

        .content-wrapper {
            flex-direction: column;
        }

        .info-heading {
            font-size: 1.75rem;
        }

        .info-top-grid {
            grid-template-columns: 1fr;
        }

        .info-section,
        .info-portrait-card {
            padding: var(--spacing-sm);
        }

        .info-section-title,
        .info-portrait-title {
            font-size: 1.25rem;
        }

        .info-values-grid {
            grid-template-columns: 1fr;
        }

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
            bottom: 40px;
        }

        .organization-container {
            padding: 2rem;
        }

        .text ul li {
            font-size: 1.25rem;
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
            bottom: 30px;
        }

        .organization-container {
            padding: 1.5rem;
        }

        .text ul li {
            font-size: 1.1rem;
        }
    }

    /* Print-friendly styles */
    @media print {
        .info-area {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .info-section,
        .info-portrait-card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .cvsu-event-hero {
            display: none;
            /* Hide background images for print */
        }
    }

    /* Add animation for fadeIn effect */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<body>
    <section class="cvsu-event-hero">
        <h2>About</h2>
        <p>Explore our information that offers visitors insight into the organization or individual behind the site, including its mission, values, and goals.</p>
        <a href="#services" class="cvsu-cta-btn" id="scroll-btn">View About</a>
    </section>

    <section id="services" class="services-section">

        <?php include('components/about/organization.php'); ?>

        <?php include('components/about/info.php'); ?>


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

</body>

</html>