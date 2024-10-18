<?php
include "../conn.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $reply_to = $_POST['reply_to'] ?? null; // Get the reply_to ID if it exists

    // Handle file upload
    $filePath = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $newFileName = uniqid() . '_' . $fileName;
        $uploadFileDir = '../msgUploads/files';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $filePath = $dest_path;
        }
    }

    // Prepare the SQL query to include reply_to
    $query = "INSERT INTO chats (senderId, receiverId, messageContent, messageFiles, messageReplyTo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $sender_id, $receiver_id, $message, $filePath, $reply_to);
    
    // Execute the query
    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>
