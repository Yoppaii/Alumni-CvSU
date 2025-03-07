<?php
require_once 'main_db.php';
$categoryQuery = "SELECT DISTINCT category FROM news ORDER BY category";
$categoryResult = $mysqli->query($categoryQuery);
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category'];
}

$items_per_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($selected_category && $selected_category !== 'all') {
    $query = "SELECT * FROM news WHERE category = ? ORDER BY date DESC LIMIT ? OFFSET ?";
    $count_query = "SELECT COUNT(*) as total FROM news WHERE category = ?";
    
    $stmt = $mysqli->prepare($count_query);
    $stmt->bind_param("s", $selected_category);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_items = $total_result->fetch_assoc()['total'];
    $stmt->close();
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sii", $selected_category, $items_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM news ORDER BY date DESC LIMIT ? OFFSET ?";
    $count_query = "SELECT COUNT(*) as total FROM news";
    
    $total_result = $mysqli->query($count_query);
    $total_items = $total_result->fetch_assoc()['total'];
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $items_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

$total_pages = ceil($total_items / $items_per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All News & Features - CvSU Alumni</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .news-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .news-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .news-title {
            font-size: 2rem;
            color: var(--cvsu-text-dark);
            margin-bottom: 0.5rem;
        }

        .news-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .news-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .news-tab {
            padding: 0.75rem 1.5rem;
            border: none;
            background: none;
            color: #666;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .news-tab.active {
            color: var(--cvsu-primary-green);
            border-bottom-color: var(--cvsu-primary-green);
        }

        .news-tab:hover {
            color: var(--cvsu-primary-green);
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
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

        .news-category-label {
            color: var(--cvsu-primary-green);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .news-item-title {
            color: var(--cvsu-text-dark);
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
            justify-content: space-between;
            align-items: center;
            color: #888;
            font-size: 0.85rem;
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: auto;
        }

        .news-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .news-read-more {
            color: var(--cvsu-primary-green);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s ease;
        }

        .news-read-more:hover {
            color: var(--cvsu-hover-green);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-link {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: var(--cvsu-text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover, .page-link.active {
            background-color: var(--cvsu-primary-green);
            color: white;
            border-color: var(--cvsu-primary-green);
        }

        .page-link.disabled {
            color: #999;
            pointer-events: none;
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
    </style>
</head>
<body>
    <div class="news-container">
        <div class="news-header">
            <h1 class="news-title">News & Features</h1>
            <p class="news-subtitle">Stay updated with all CvSU happenings and announcements</p>
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
                        <span class="news-category-label"><?php echo htmlspecialchars($row['category']); ?></span>
                        <h3 class="news-item-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="news-excerpt"><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="news-meta">
                            <span class="news-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo date('F d, Y', strtotime($row['date'])); ?>
                            </span>
                            <a href="?pages=news-details&id=<?php echo $row['id']; ?>" class="news-read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?category=<?php echo urlencode($selected_category); ?>&page=<?php echo ($page - 1); ?>" 
                       class="page-link">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?category=<?php echo urlencode($selected_category); ?>&page=<?php echo $i; ?>" 
                       class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?category=<?php echo urlencode($selected_category); ?>&page=<?php echo ($page + 1); ?>" 
                       class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>