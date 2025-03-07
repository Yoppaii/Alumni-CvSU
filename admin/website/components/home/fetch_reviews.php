<?php
require '../main_db.php'; 

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

$feedback_per_page = 5;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$offset = ($page - 1) * $feedback_per_page;

$sql_feedback = "SELECT id, user_email, room_rating, staff_rating, cleanliness_rating, comment, created_at FROM feedback ORDER BY created_at DESC LIMIT $feedback_per_page OFFSET $offset";
$stmt_feedback = $mysqli->query($sql_feedback);

if (!$stmt_feedback) {
    die("Query failed: " . $mysqli->error);  
}

$feedbacks = $stmt_feedback->fetch_all(MYSQLI_ASSOC); 

$sql_replies = "SELECT feedback_id, admin_name, reply, is_admin_reply, created_at FROM feedback_replies WHERE 1";
$stmt_replies = $mysqli->query($sql_replies);

if (!$stmt_replies) {
    die("Query failed: " . $mysqli->error);  
}

$replies = $stmt_replies->fetch_all(MYSQLI_ASSOC); 

$replies_by_feedback = [];
foreach ($replies as $reply) {
    $replies_by_feedback[$reply['feedback_id']][] = $reply;
}

function mask_email($email) {
    list($name, $domain) = explode('@', $email);
    $masked_name = substr($name, 0, 1) . str_repeat('*', strlen($name) - 2) . substr($name, -1);
    return $masked_name . '@' . $domain;
}

function display_stars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';  
    }
    return $stars;
}
?>
<section class="reviews-section">
    <h2>What Our Alumni Are Saying</h2>
    <div class="reviews-container">
        <?php foreach ($feedbacks as $feedback): ?>
        <div class="review-item">
            <div class="email"><?php echo htmlspecialchars(mask_email($feedback['user_email'])); ?></div>
            <div class="ratings">
                <div class="rating"><strong>Room Rating:</strong> <?php echo display_stars($feedback['room_rating']); ?></div>
                <div class="rating"><strong>Staff Rating:</strong> <?php echo display_stars($feedback['staff_rating']); ?></div>
                <div class="rating"><strong>Cleanliness Rating:</strong> <?php echo display_stars($feedback['cleanliness_rating']); ?></div>
            </div>
            <p class="comment">"<?php echo htmlspecialchars($feedback['comment']); ?>"</p>

            <?php if (isset($replies_by_feedback[$feedback['id']])): ?>
                <div class="reply-section">
                    <?php 
                    $first_reply = reset($replies_by_feedback[$feedback['id']]);
                    ?>
                    <div class="reply">
                        <strong><?php echo htmlspecialchars($first_reply['admin_name']); ?>:</strong>
                        <p><?php echo htmlspecialchars($first_reply['reply']); ?></p>
                        <div class="reply-date">
                            Replied to: <?php echo htmlspecialchars(mask_email($feedback['user_email'])); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="posted-date">Posted on: <?php echo date("F j, Y", strtotime($feedback['created_at'])); ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php 
    $sql_count = "SELECT COUNT(*) AS total FROM feedback";
    $result = $mysqli->query($sql_count);
    $total_feedbacks = $result->fetch_assoc()['total'];

    $next_page = $page + 1;
    if ($feedbacks && $total_feedbacks > $page * $feedback_per_page): ?>
        <button id="load-more" class="show-more-btn" data-next-page="<?php echo $next_page; ?>">See More Feedback</button>
    <?php endif; ?>
</section>

<script>
    document.getElementById('load-more')?.addEventListener('click', function() {
        var nextPage = this.getAttribute('data-next-page');
        var url = window.location.href.split('?')[0] + '?page=' + nextPage;
        window.location.href = url;  
    });
</script>

<style>
.reviews-section {
    background-color: #ffffff;
    padding: 70px 30px;
    text-align: center;
}

.reviews-section h2 {
    font-size: 36px;
    color: #388e3c;
    margin-bottom: 30px;
    font-weight: 700;
}

.reviews-container {
    max-width: 1200px;
    margin: 0 auto;
}

.review-item {
    background: #f1f8e9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: center;
    margin-bottom: 20px;
}

.review-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}

.email, .ratings {
    text-align: left;
}

.email {
    font-weight: bold;
    color: #66bb6a;
    margin-bottom: 10px;
}

.ratings {
    margin-bottom: 15px;
    font-size: 14px;
    color: #388e3c;
}

.rating {
    margin-bottom: 5px;
}

.comment {
    font-size: 16px;
    color: #388e3c;
    margin-bottom: 15px;
}

.posted-date {
    font-size: 12px;
    color: #81c784;
    margin-top: 20px;
}

.reply-section {
    background: #e8f5e9;
    padding: 15px;
    margin-top: 20px;
    border-radius: 10px;
}

.reply-date {
    font-size: 12px;
    color: #81c784;
}

.show-more-btn {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #388e3c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.show-more-btn:hover {
    background-color: #66bb6a;
}
</style>
