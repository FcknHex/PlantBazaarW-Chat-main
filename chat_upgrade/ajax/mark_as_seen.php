<?php
include "../conn.php";
session_start();

$sender_id = $_SESSION['user_id']; // The logged-in user
$recipient_id = $_POST['recipient_id'];

$stmt = $conn->prepare("UPDATE messages SET status = 1 WHERE sender_id = ? AND receiver_id = ? AND status = 0");
$stmt->bind_param("ii", $recipient_id, $sender_id);
$stmt->execute();

echo "Messages marked as seen";
?>
