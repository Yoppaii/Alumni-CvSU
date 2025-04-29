<?php
require_once 'main_db.php';

// Fetch all campuses
$query = "SELECT * FROM campuses ORDER BY name ASC";
$result = $mysqli->query($query);
?>

<div class="campus-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <a href="<?= htmlspecialchars($row['url']) ?>" class="campus-item">
            <img src="asset/images/campuses/<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
        </a>
    <?php endwhile; ?>
</div>


<style>
    :root {
        --primary-green: #2e7d32;
        --hover-green: #1b5e20;
        --light-green: #e8f5e9;
        --gray-light: #f5f5f5;
        --text-dark: #333;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    .campus-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .campus-item {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        text-decoration: none;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .campus-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .campus-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 3px solid var(--primary-green);
    }

    .campus-item h3 {
        color: var(--text-dark);
        padding: 1rem;
        margin: 0;
        font-size: 1.1rem;
        text-align: center;
        background: white;
        transition: var(--transition);
    }

    .campus-item:hover h3 {
        color: var(--primary-green);
    }

    /* Animation */
    .campus-item {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .campus-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            padding: 1rem;
            gap: 1rem;
        }

        .campus-item img {
            height: 180px;
        }

        .campus-item h3 {
            font-size: 1rem;
            padding: 0.8rem;
        }
    }

    /* For smaller screens */
    @media (max-width: 480px) {
        .campus-grid {
            grid-template-columns: 1fr;
        }

        .campus-item img {
            height: 200px;
        }
    }

    /* Optional: Add stagger effect to grid items */
    .campus-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .campus-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .campus-item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .campus-item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .campus-item:nth-child(5) {
        animation-delay: 0.5s;
    }

    .campus-item:nth-child(6) {
        animation-delay: 0.6s;
    }

    .campus-item:nth-child(7) {
        animation-delay: 0.7s;
    }

    .campus-item:nth-child(8) {
        animation-delay: 0.8s;
    }

    .campus-item:nth-child(9) {
        animation-delay: 0.9s;
    }
</style>