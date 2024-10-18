<?php
include "../conn.php";
session_start();

$sender_id = $_GET['sender_id']; // Get sender ID from the request
$receiver_id = $_GET['receiver_id']; // Get receiver ID from the request

// Prepare the SQL query with a LEFT JOIN to fetch replies
$query = "SELECT m1.*, m2.messageContent AS reply_to_message, m2.messageFiles AS reply_image 
          FROM chats m1 
          LEFT JOIN chats m2 ON m1.messageReplyTo = m2.messageId  
          WHERE (m1.senderId = ? AND m1.receiverId = ?) 
             OR (m1.senderId = ? AND m1.receiverId = ?) 
          ORDER BY m1.messageDate ASC";

$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query returned results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine message class based on sender
        $messageClass = ($row['senderId'] == $sender_id) ? 'sent' : 'received';
        echo '<div class="message ' . $messageClass . '" data-id="' . htmlspecialchars($row['messageId']) . '">';

        // Display the message text
        echo '<span class="message-text">' . htmlspecialchars($row['messageContent']) . '</span>';
        echo '<br>';
        echo '<span class="reply-message">' . htmlspecialchars($row['messageDate']) . '</span>';

        // Display file if it exists
        if (!empty($row['messageFiles'])) {
            echo '<br><img src="Ajax/' . htmlspecialchars($row['messageFiles']) . '" class="thumbnail" onclick="openFullScreen(this)" style="cursor: pointer; width: 100px; height: auto;">';
            echo '<button class="reply-button" data-reply-message="' . htmlspecialchars($row['messageContent']) . '" data-reply-image="' . htmlspecialchars($row['messageFiles']) . '"><i class="fas fa-reply"></i></button>';
        } else {
            echo '<button class="reply-button" data-reply-message="' . htmlspecialchars($row['messageContent']) . '"><i class="fas fa-reply"></i></button>';
        }

        // Display the original message being replied to (if applicable)
        if (!empty($row['reply_to_message'])) {
            echo '<div class="reply-message">Replying to: ' . htmlspecialchars($row['reply_to_message']);
            
            // If replying to a message that has an image
            if (!empty($row['reply_image'])) {
                echo '<img src="Ajax/' . htmlspecialchars($row['reply_image']) . '" class="reply-image" style="cursor: pointer; width: 50px; height: auto;">';
            }
            echo '</div>'; // Close reply-message div
        }

        echo '</div>'; // Close message div
    }
} else {
    echo '<div class="no-messages">No messages found.</div>';
}

// Close statement and connection
$stmt->close();
$conn->close();

?>
