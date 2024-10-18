<?php
include "../conn.php";
session_start();

$current_user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Query to fetch users who have conversations with the current user
$stmt = "
    SELECT users.id, users.username, messages.message AS message_preview, messages.timestamp
    FROM users
    JOIN messages ON users.id = messages.sender_id OR users.id = messages.receiver_id
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?)
    GROUP BY users.id
    ORDER BY messages.timestamp DESC
";
$prepared_stmt = $conn->prepare($stmt);
$prepared_stmt->bind_param('ii', $current_user_id, $current_user_id);
$prepared_stmt->execute();
$result = $prepared_stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $username = $row['username'];
    $message_preview = $row['message_preview'] ?: "No messages yet";
    $timestamp = $row['timestamp'] ? date('g:i a', strtotime($row['timestamp'])) : "";

    echo "<div class='user' id='$id' data-username='$username'>
            <h5>$username</h5>
            <div class='message-notification'>
                <p class='message-preview'>$message_preview</p>
                <p class='time-stamp'>$timestamp</p>
            </div>
          </div>";
}

$prepared_stmt->close();
$conn->close();
?>
