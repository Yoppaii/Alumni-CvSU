<?php
require_once 'main_db.php';

$categoryQuery = "SELECT DISTINCT category FROM news ORDER BY category";
$categoryResult = $mysqli->query($categoryQuery);
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category'];
}

$query = "SELECT n.*, 
    (SELECT COUNT(*) FROM news_likes WHERE news_id = n.id) as like_count,
    (SELECT COUNT(*) > 0 FROM news_likes WHERE news_id = n.id AND user_id = ?) as user_liked
    FROM news n 
    ORDER BY date DESC LIMIT 3";
    
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest News & Features - CvSU Alumni</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .news-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        #Announce {
            color: var(--cvsu-primary-green);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--cvsu-light-green);
        }

        .news-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .news-item:hover {
            transform: translateY(-5px);
        }

        .news-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .news-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .news-category {
            color: #006837;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .news-title {
            color: #333;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .news-excerpt {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }

        .news-meta {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            color: #888;
            font-size: 0.85rem;
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: auto;
        }

        .news-date-read {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .news-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .news-read-more {
            color: #006837;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s ease;
        }

        .news-read-more:hover {
            color: #005229;
        }

        .news-social {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.5rem;
            border-top: 1px solid #f5f5f5;
        }

        .news-social-left {
            display: flex;
            gap: 1.25rem;
        }

        .social-icon {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .social-icon:hover {
            color: #006837;
        }

        .social-icon i {
            font-size: 1rem;
        }

        .social-icon span {
            font-size: 0.85rem;
        }

        .social-icon.share {
            color: #006837;
        }

        .social-icon.liked {
            color: #e74c3c;
        }

        .social-icon.liked i {
            animation: likeEffect 0.4s ease;
        }

        @keyframes likeEffect {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .section-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .view-all-btn {
            display: block;
            width: fit-content;
            margin: 2rem auto;
            padding: 0.75rem 2rem;
            background-color: #006837;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .view-all-btn:hover {
            background-color: #005229;
        }

        @media (max-width: 992px) {
            .news-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .news-grid {
                grid-template-columns: 1fr;
            }

            .news-content {
                padding: 1rem;
            }
        }

        .like-loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="news-container">
        <div class="section-header">
            <h2 id="Announce">
                    <i class="fas fa-newspaper"></i>
                    Latest News and Features
                </h2>
        </div>

        <div class="news-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <article class="news-item">
                    <?php if ($row['image_path']): ?>
                        <img src="asset/uploads/<?php echo htmlspecialchars($row['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>" 
                             class="news-image">
                    <?php endif; ?>
                    <div class="news-content">
                        <span class="news-category"><?php echo htmlspecialchars($row['category']); ?></span>
                        <h3 class="news-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="news-excerpt"><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="news-meta">
                            <div class="news-date-read">
                                <span class="news-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('F d, Y', strtotime($row['date'])); ?>
                                </span>
                                <a href="?pages=news-details&id=<?php echo $row['id']; ?>" class="news-read-more">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="news-social">
                                <div class="news-social-left">
                                    <div class="social-icon <?php echo $row['user_liked'] ? 'liked' : ''; ?>" 
                                         onclick="handleLike(this, <?php echo $row['id']; ?>)" 
                                         data-news-id="<?php echo $row['id']; ?>">
                                        <i class="<?php echo $row['user_liked'] ? 'fas' : 'far'; ?> fa-heart"></i>
                                        <span><?php echo $row['like_count']; ?></span>
                                    </div>
                                    <a href="?pages=news-details&id=<?php echo $row['id']; ?>#comments" class="social-icon">
                                        <i class="far fa-comment"></i>
                                        <span>45</span>
                                    </a>
                                </div>
                                <div class="social-icon share" onclick="shareNews(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>')">
                                    <i class="fas fa-share-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <a href="?pages=all-news" class="view-all-btn">View All News</a>
    </div>

    <script>
        function handleLike(element, newsId) {
            element.classList.add('like-loading');
            
            fetch('news/handle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `news_id=${newsId}&action=toggle`
            })
            .then(response => response.json())
            .then(data => {
                element.classList.remove('like-loading');
                
                if (data.success) {
                    const icon = element.querySelector('i');
                    const countSpan = element.querySelector('span');
                    
                    if (data.liked) {
                        element.classList.add('liked');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        element.classList.remove('liked');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                    
                    countSpan.textContent = data.likeCount;
                } else {
                    if (data.message === 'User not logged in') {
                        alert('Please log in to like this article');
                        window.location.href = 'login.php';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                element.classList.remove('like-loading');
                alert('An error occurred. Please try again later.');
            });
        }

        function shareNews(newsId, title) {
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: window.location.origin + window.location.pathname + '?pages=news-details&id=' + newsId
                })
                .catch(console.error);
            } else {
                const url = window.location.origin + window.location.pathname + '?pages=news-details&id=' + newsId;
                navigator.clipboard.writeText(url)
                    .then(() => alert('Link copied to clipboard!'))
                    .catch(console.error);
            }
        }
    </script>
</body>
</html>