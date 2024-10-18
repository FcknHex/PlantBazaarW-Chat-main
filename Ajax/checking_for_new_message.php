<?php
session_start();
include '../conn.php'; // Make sure to include your database connection file

$lastCheckedTime = $_SESSION['last_checked_time'] ?? 0;

// Check for new messages
$sql = "SELECT COUNT(*) AS count FROM messages WHERE created_at > ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lastCheckedTime);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$newMessageCount = $row['count'];
if ($newMessageCount > 0) {
    echo json_encode(array('hasNewMessages' => true, 'newMessageCount' => $newMessageCount));
} else {
    echo json_encode(array('hasNewMessages' => false));
}
?>
