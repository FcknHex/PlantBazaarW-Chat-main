<?php
session_start();
$current_user_id = $_SESSION['user_id'];
include "../conn.php";

// Get the latest message in each conversation involving the current user
$sql = "SELECT m1.*, m1.status, unseen_count.unseen_messages 
        FROM messages m1
        INNER JOIN (
            SELECT 
                CASE 
                    WHEN sender_id = $current_user_id THEN receiver_id 
                    ELSE sender_id 
                END AS chat_user,
                MAX(timestamp) AS latest_message
            FROM messages
            WHERE sender_id = $current_user_id OR receiver_id = $current_user_id
            GROUP BY chat_user
        ) m2 ON (m1.sender_id = $current_user_id AND m1.receiver_id = m2.chat_user 
                 OR m1.receiver_id = $current_user_id AND m1.sender_id = m2.chat_user) 
                 AND m1.timestamp = m2.latest_message
        LEFT JOIN (
            SELECT 
                CASE 
                    WHEN sender_id = $current_user_id THEN receiver_id 
                    ELSE sender_id 
                END AS chat_user,
                COUNT(*) AS unseen_messages
            FROM messages
            WHERE status = 0 
            AND receiver_id = $current_user_id
            GROUP BY chat_user
        ) unseen_count ON unseen_count.chat_user = m2.chat_user
        ORDER BY m1.timestamp DESC";


$result = mysqli_query($conn, $sql);
$userMessages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $isNewMessage = $row['status'] == 0;
    $unseenCount = isset($row['unseen_messages']) ? $row['unseen_messages'] : 0;

    // Determine the other user's ID (chat partner)
    $chatUserId = ($row['sender_id'] == $current_user_id) ? $row['receiver_id'] : $row['sender_id'];
    

    $messageText = $isNewMessage 
        ?  "New Message (" . $unseenCount . "): " . htmlspecialchars($row['message'])
        : htmlspecialchars($row['message']);
    
    $userMessages[] = [
        'user_id' => $chatUserId, // Use chat partner's ID here
        'message' => $messageText,
        'timestamp' => date("h:i a", strtotime($row['timestamp'])),
        'status' => $row['status'],
        'sender_id' => $row['sender_id'], // Add sender_id for identification
        'recipient_id' => $row['receiver_id'] // Add receiver_id for identification
    ];
}

echo json_encode($userMessages);
?>
