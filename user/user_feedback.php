<?php
require_once('main_db.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: index");
    exit;
}

$user_email = $_SESSION['user_email'];

$query = "SELECT id, user_email, room_rating, staff_rating, cleanliness_rating, comment, created_at FROM feedback WHERE user_email = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $user_email);  
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching feedback: " . $mysqli->error);
}

function getStarRating($rating) {
    $fullStars = floor($rating); 
    $halfStar = ($rating - $fullStars) >= 0.5 ? true : false; 
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); 
    
    $stars = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="fas fa-star"></i>';
    }
    if ($halfStar) {
        $stars .= '<i class="fas fa-star-half-alt"></i>'; 
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="far fa-star"></i>'; 
    }
    
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Recent Feedback</title>
    <link rel="stylesheet" href="main_lel.css">
    <style>
        .feedback-history-container {
            max-width: 960px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback-history-container h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #3C8D40;
            margin-bottom: 20px;
            text-align: center;
        }

        .feedback-item {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; 
        }

        .feedback-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .feedback-header strong {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .feedback-header .feedback-date {
            font-size: 1rem;
            color: #888;
        }

        .feedback-body p {
            font-size: 1rem;
            margin: 5px 0;
        }

        .feedback-body strong {
            color: #3C8D40;
        }

        .star-rating i {
            color: #FFD700; 
        }

        .feedback-replies {
            margin-top: 20px;
        }

        .reply-item {
            margin-bottom: 15px;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .reply-header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .reply-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .reply-header strong {
            font-size: 0.9rem;
            color: #3C8D40;
        }

        .reply-comment {
            margin-top: 8px;
            font-size: 1rem;
        }

        .reply-date {
            font-size: 0.85rem;
            color: #888;
            margin-top: 5px;
        }

        .reply-form-container {
            margin-top: 20px;
            display: flex;
            align-items: center;
            background-color: #f7f7f7;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .reply-form-container textarea {
            width: 100%;
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 25px;
            border: 1px solid #ccc;
            resize: none;
            box-sizing: border-box;
            margin-right: 10px;
        }

        .reply-form-container button {
            background-color: #3C8D40;
            color: white;
            font-size: 1.2rem;
            padding: 12px 15px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reply-form-container button:hover {
            background-color: #2c6a2f;
        }

        .reply-form-container button i {
            font-size: 1.5rem;
        }

        .reply-form-container textarea:focus {
            outline: none;
            border-color: #3C8D40;
        }
    </style>
</head>
<body>

<section class="feedback-history-container">
    <h2>Your Recent Feedback</h2>

    <?php
    if ($feedback = $result->fetch_assoc()) : 
    ?>
        <div class="feedback-item">
            <div class="feedback-header">
                <strong><?php echo htmlspecialchars($feedback['user_email']); ?></strong>
            </div>

            <div class="feedback-body">
                <p><strong>Room Rating:</strong> <span class="star-rating"><?php echo getStarRating($feedback['room_rating']); ?></span></p>
                <p><strong>Staff Rating:</strong> <span class="star-rating"><?php echo getStarRating($feedback['staff_rating']); ?></span></p>
                <p><strong>Cleanliness Rating:</strong> <span class="star-rating"><?php echo getStarRating($feedback['cleanliness_rating']); ?></span></p>
                <p><strong>Feedback Comment:</strong> <?php echo htmlspecialchars($feedback['comment']); ?></p>
            </div>

            <?php
            $feedbackId = $feedback['id'];
            $replyQuery = "SELECT reply, created_at, admin_name FROM feedback_replies WHERE feedback_id = $feedbackId ORDER BY created_at DESC";
            $replyResult = $mysqli->query($replyQuery);

            if ($replyResult && $replyResult->num_rows > 0):
            ?>
                <div class="feedback-replies">
                    <h5>Replies:</h5>
                    <?php while ($reply = $replyResult->fetch_assoc()): ?>
                        <div class="reply-item">
                            <div class="reply-header">
                                <?php
                                if ($reply['admin_name'] === $feedback['user_email']) {
                                    $imageSource = "../user/bg/Profile-user.png";
                                } else {
                                    $imageSource = "../user/bg/Profile-user2.png";
                                }
                                ?>
                                <img src="<?php echo $imageSource; ?>" alt="Admin Profile Picture">
                                <strong><?php echo htmlspecialchars($reply['admin_name']); ?></strong>
                            </div>
                            <p class="reply-comment"><?php echo htmlspecialchars($reply['reply']); ?></p>
                            <span class="reply-date"><?php echo date('g:i a', strtotime($reply['created_at'])); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No replies yet.</p>
            <?php endif; ?>

        </div>

        <form method="POST" action="user_reply.php">
            <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
            <div class="reply-form-container">
                <textarea name="reply" rows="3" placeholder="Write your reply..."></textarea>
                <button type="submit">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php

$mysqli->close();
?>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
