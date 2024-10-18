<?php
session_start();
include "../conn.php";

// Ensure seller_email is set and not empty
if (isset($_GET['seller_email']) && !empty($_GET['seller_email'])) {
    $recipient_email = mysqli_real_escape_string($conn, $_GET['seller_email']); // Get seller email from query
    $sender_id = $_SESSION['user_id']; // The logged-in user's ID

    // Query to get recipient's ID based on email
    $recipient_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($recipient_query);
    $stmt->bind_param('s', $recipient_email);
    $stmt->execute();
    $recipient_result = $stmt->get_result();

    if ($recipient_result->num_rows > 0) {
        $recipient_data = $recipient_result->fetch_assoc();
        $recipient_id = $recipient_data['id'];

        // Query to get messages between the sender and recipient
        $sql = "SELECT * FROM messages WHERE 
                (sender_id = ? AND receiver_id = ?) 
                OR (sender_id = ? AND receiver_id = ?)
                ORDER BY timestamp ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiii', $sender_id, $recipient_id, $recipient_id, $sender_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            // Display the messages
            while ($row = $result->fetch_assoc()) {
                $message = htmlspecialchars($row['message']); // Escape output
                $image = $row['file_path']; // The path to the image file (if any)
                $reply_to = $row['reply_to']; // Check if this message is a reply
                $message_id = $row['id']; // Get the message ID for the anchor link

                // Determine if the message was sent or received
                $message_class = ($row['sender_id'] == $sender_id) ? 'sent' : 'received';

                // Start the message div (includes the original message and reply)
                echo "<div class='message-item $message_class' id='message-$message_id'>"; // Add an ID for scrolling
                echo "<div class='message-content'>";

                // Display the original message first (sent message)
                echo "<div class='original-message'>";
                echo "<p class='message-text'>$message</p>";

                // Display the image if it exists
                if (!empty($image)) {
                    echo "<div class='message-image'>";
                    echo "<img src='uploads/$image' alt='Image' class='message-image' />";
                    echo "</div>";
                }
                echo "</div>"; // Close original-message

                // If replying to another message, fetch and display the reply message below
                if ($reply_to) {
                    // Query to get the original message being replied to
                    $reply_sql = "SELECT message, file_path FROM messages WHERE id = ?";
                    $reply_stmt = $conn->prepare($reply_sql);
                    $reply_stmt->bind_param('i', $reply_to);
                    $reply_stmt->execute();
                    $reply_result = $reply_stmt->get_result();
                    $reply_message = $reply_result->fetch_assoc();

                    if ($reply_message) {
                        $reply_text = htmlspecialchars($reply_message['message']); // Escape output
                        $reply_image = $reply_message['file_path'];

                        // Display the reply message as a clickable link
                        echo "<div class='replied-message' style='position: relative;'>"; // Style this as a reply box
                        echo "<a href='#message-$reply_to' class='reply-link'><p class='replied-text'><strong>Replying to:</strong> $reply_text</p>";

                        // Display the image if it exists
                        if (!empty($reply_image)) {
                            echo "<img src='uploads/$reply_image' alt='Image' class='replied-image' />";
                        }

                        echo "</a></div>"; // Close reply-link and replied-message
                    }
                    $reply_stmt->close();
                }

                // Ellipsis button for reply, delete, etc.
                echo "<div class='message-options'>";
                echo "<span class='ellipsis'>...</span>";
                echo "<div class='options-menu' style='display: none;'>";
                echo "<button class='reply-btn' data-message-id='{$row['id']}'>Reply</button>";
                echo "<button class='delete-btn' data-message-id='{$row['id']}'>Delete</button>";
                echo "</div>"; // Close options-menu
                echo "</div>"; // Close message-options

                echo "</div>"; // Close message-content
                echo "</div>"; // Close message-item
            }

        } else {
            echo "Error loading messages: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Recipient not found.";
    }

} else {
    echo "Recipient email is missing.";
}

$conn->close();
?>
<link rel="stylesheet" href="style.css">
