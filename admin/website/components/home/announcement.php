<?php
require_once('main_db.php');
$query = "SELECT `id`, `badge`, `title`, `content`, `created_at`, `status` FROM `announcements` WHERE `status` = 1 ORDER BY `created_at` DESC LIMIT 3";
$result = $mysqli->query($query);
?>
<section id="announcement-section">
    <div class="announcement-container">
        <h2 class="announcement-heading">
            <i class="fas fa-bullhorn"></i>
            Important Announcements
        </h2>

        <div class="announcement-grid">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $date = new DateTime($row['created_at']);
                    $formatted_date = $date->format('M d, Y');
                    $truncated_content = mb_substr(strip_tags($row['content']), 0, 150);
                    if (mb_strlen($row['content']) > 150) {
                        $truncated_content .= '...';
                    }

                    // Determine badge style based on content
                    $badge_class = '';
                    $badge_text = htmlspecialchars($row['badge']);
                    $badge_lower = strtolower($badge_text);

                    if (strpos($badge_lower, 'urgent') !== false || strpos($badge_lower, 'important') !== false) {
                        $badge_class = 'urgent';
                    } elseif (strpos($badge_lower, 'new') !== false) {
                        $badge_class = 'new';
                    } elseif (strpos($badge_lower, 'update') !== false) {
                        $badge_class = 'update';
                    }
            ?>
                    <div class="announcement-item" data-id="<?php echo $row['id']; ?>">
                        <div class="announcement-header">
                            <?php if (!empty($badge_text)) : ?>
                                <span class="announcement-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                            <?php endif; ?>
                            <h3 class="announcement-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        </div>
                        <p class="announcement-content"><?php echo htmlspecialchars($truncated_content); ?></p>
                        <div class="announcement-footer">
                            <p class="announcement-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo $formatted_date; ?>
                            </p>
                            <a href="?pages=announcement&id=<?php echo $row['id']; ?>" class="announcement-link">
                                Read more <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="no-announcements"><i class="fas fa-info-circle"></i> No announcements available at this time.</div>';
            }
            ?>
        </div>
 
        <!-- Removed the conditional check to always show the "See all" link -->
        <div class="announcement-actions">
            <a href="?pages=announcement" class="announcement-view-all">
                See all announcements
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</section>

<style>
    /* Enhanced Announcement Section Styles */
    #announcement-section {
        width: 100%;
        background-color: #f8f9fa;
    }

    .announcement-container {
        background-color: #f8f9fa;
    }

    .announcement-heading {
        color: var(--text-primary);
    }


    .announcement-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .announcement-item {
        border-radius: 8px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #eaeaea;
        cursor: pointer;
        opacity: 0;
        margin-bottom: auto;
        /* Start invisible for fade-in */
    }

    .announcement-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .announcement-header {
        padding: 1.25rem 1.25rem 0.25rem;
    }

    .announcement-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        background-color: #e9ecef;
        color: #495057;
        transition: all 0.3s ease;
    }

    .announcement-badge.urgent {
        background-color: #ffe3e3;
        color: #c92a2a;
    }

    .announcement-badge.new {
        background-color: #e3fafc;
        color: #0b7285;
    }

    .announcement-badge.update {
        background-color: #fff3bf;
        color: #e67700;
    }

    .announcement-title {
        font-size: 1.25rem;
        margin: 0 0 0.75rem;
        line-height: 1.4;
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    .announcement-item:hover .announcement-title {
        color: var(--primary-dark);
    }

    .announcement-content {
        padding: 0 1.25rem 1rem;
        margin: 0;
        color: #495057;
        line-height: 1.6;
        flex-grow: 1;
    }

    .announcement-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        border-top: 1px solid #eaeaea;
        transition: background-color 0.3s ease;
    }

    .announcement-item:hover .announcement-footer {
        background-color: #f0f4ff;
    }

    .announcement-date {
        margin: 0;
        font-size: 0.85rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .announcement-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--primay-dark);
        transition: all 0.3s ease;
        position: relative;
    }

    .announcement-link:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--primay-dark);
        transition: width 0.3s ease;
    }

    .announcement-link:hover:after {
        width: 100%;
        color: var(--primay-dark);

    }

    .announcement-link:hover {
        color: var(--primary-dark);
    }

    .announcement-view-all {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .announcement-view-all:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(13, 110, 253, 0.1);
        transition: all 0.3s ease;
        z-index: -1;
    }

    .announcement-view-all:hover:before {
        left: 0;
    }

    .announcement-view-all:hover {
        background-color: var(--primary-color);
        color: var(--white);
    }

    .announcement-view-all i {
        transition: transform 0.3s ease;
    }

    .announcement-view-all:hover i {
        transform: translateX(3px);
    }

    .announcement-actions {
        display: flex;
        justify-content: center;
        /* margin-top: 1rem; */
    }

    .no-announcements {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        font-size: 1.1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .announcement-grid {
            grid-template-columns: 1fr;
        }

        .announcement-item {
            max-width: 100%;
        }
    }

    /* Simple fade-in animation keyframes */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .announcement-item.animate {
        animation: fadeIn 1s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
    }

    /* Staggered animation delays */
    .announcement-item:nth-child(1).animate {
        animation-delay: 0.1s;
    }

    .announcement-item:nth-child(2).animate {
        animation-delay: 0.3s;
    }

    .announcement-item:nth-child(3).animate {
        animation-delay: 0.5s;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Detect when elements enter viewport using Intersection Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                    // Once animated, no need to observe anymore
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all announcement items
        const announcementItems = document.querySelectorAll('.announcement-item');
        announcementItems.forEach(item => {
            observer.observe(item);
        });

        // Click event for the announcement items
        announcementItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Only redirect if the click wasn't on the "Read more" link
                if (!e.target.closest('.announcement-link')) {
                    const id = this.getAttribute('data-id');

                    // Add a smooth fade-out before redirecting
                    this.style.transition = 'opacity 0.3s ease';
                    this.style.opacity = '0';

                    setTimeout(() => {
                        window.location.href = `?pages=announcement&id=${id}`;
                    }, 300);
                }
            });
        });

        // Add hover effect for "Read more" links
        const readMoreLinks = document.querySelectorAll('.announcement-link');
        readMoreLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i.fa-arrow-right');
                if (icon) {
                    icon.style.transition = 'transform 0.3s ease';
                    icon.style.transform = 'translateX(4px)';
                }
            });

            link.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i.fa-arrow-right');
                if (icon) {
                    icon.style.transform = 'translateX(0)';
                }
            });
        });

        // Add hover effect for "See all announcements" link
        const viewAllLink = document.querySelector('.announcement-view-all');
        if (viewAllLink) {
            viewAllLink.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i.fa-chevron-right');
                if (icon) {
                    icon.style.transition = 'transform 0.3s ease';
                    icon.style.transform = 'translateX(4px)';
                }
            });

            viewAllLink.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i.fa-chevron-right');
                if (icon) {
                    icon.style.transform = 'translateX(0)';
                }
            });
        }
    });
</script>