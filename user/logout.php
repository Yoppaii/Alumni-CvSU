<?php
session_start();
require_once '../main_db.php';

$query = "UPDATE users SET session_token = NULL WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

session_unset(); 
session_destroy();  

header("Location: ../index"); 
exit;
?>
