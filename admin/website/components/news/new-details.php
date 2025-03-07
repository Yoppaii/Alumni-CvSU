<?php
require_once 'main_db.php';

$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM news WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

$other_query = "SELECT id, title, image_path, date 
               FROM news 
               WHERE id != ? 
               ORDER BY date DESC 
               LIMIT 3";
$stmt = $mysqli->prepare($other_query);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$other_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news['title']); ?> - CvSU Alumni</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .det-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .det-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .det-main-article {
            border-right: 1px solid #eee;
            padding-right: 2rem;
        }

        .det-back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #006837;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: color 0.3s ease;
        }

        .det-back-button:hover {
            color: #005229;
        }

        .det-header {
            text-align: left;
            margin-bottom: 2rem;
        }

        .det-category {
            color: var(--cvsu-primary-green);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .det-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .det-meta {
            color: #666;
            font-size: 0.95rem;
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .det-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .det-featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .det-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 3rem;
        }

        /* Other Articles Section */
        .det-other-articles {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding-top: 0.5rem;
        }

        .det-article-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            position: relative;
        }

        .det-article-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .det-article-item:hover .det-article-title {
            color: var(--cvsu-primary-green);
        }

        .det-article-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .det-article-content {
            padding: 1rem 1.25rem;
            background: white;
        }

        .det-article-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--cvsu-text-dark);
            margin-bottom: 0.75rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s ease;
        }

        .det-article-date {
            font-size: 0.8rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .det-article-date i {
            font-size: 0.85rem;
            color: var(--cvsu-primary-green);
        }

        .det-error-message {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 992px) {
            .det-layout {
                grid-template-columns: 1fr;
            }

            .det-main-article {
                border-right: none;
                padding-right: 0;
                border-bottom: 1px solid #eee;
                padding-bottom: 2rem;
            }

            .det-other-articles {
                flex-direction: row;
                overflow-x: auto;
                padding: 0.5rem 0.5rem 1.5rem 0.5rem;
                margin: 0 -0.5rem;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
            }

            .det-article-item {
                min-width: 280px;
                scroll-snap-align: start;
            }

            .det-article-image {
                height: 160px;
            }
        }

        @media (max-width: 768px) {
            .det-container {
                margin: 1rem auto;
                padding: 0 15px;
            }

            .det-title {
                font-size: 1.75rem;
            }

            .det-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .det-other-articles {
                grid-template-columns: repeat(3, 260px);
            }
        }
    </style>
</head>
<body>
    <div class="det-container">
        <a href="?pages=news-features" class="det-back-button">
            <i class="fas fa-arrow-left"></i> Back to News
        </a>

        <?php if ($news): ?>
            <div class="det-layout">
                <div class="det-main-article">
                    <header class="det-header">
                        <span class="det-category"><?php echo htmlspecialchars($news['category']); ?></span>
                        <h1 class="det-title"><?php echo htmlspecialchars($news['title']); ?></h1>
                        <div class="det-meta">
                            <span>
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo date('F d, Y', strtotime($news['date'])); ?>
                            </span>
                            <span>
                                <i class="fas fa-user"></i>
                                CvSU Alumni Association
                            </span>
                        </div>
                    </header>

                    <?php if ($news['image_path']): ?>
                        <img src="asset/uploads/<?php echo htmlspecialchars($news['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($news['title']); ?>" 
                             class="det-featured-image">
                    <?php endif; ?>

                    <div class="det-content">
                        <?php echo nl2br(htmlspecialchars($news['description'])); ?>
                    </div>
                </div>

                <?php if ($other_result->num_rows > 0): ?>
                    <div class="det-other-articles">
                        <?php while ($article = $other_result->fetch_assoc()): ?>
                            <a href="?pages=news-details&id=<?php echo $article['id']; ?>" class="det-article-item">
                                <?php if ($article['image_path']): ?>
                                    <img src="asset/uploads/<?php echo htmlspecialchars($article['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                         class="det-article-image">
                                <?php endif; ?>
                                <div class="det-article-content">
                                    <h3 class="det-article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                    <span class="det-article-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('M d, Y', strtotime($article['date'])); ?>
                                    </span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="det-error-message">
                <h2>Article Not Found</h2>
                <p>The requested article could not be found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>