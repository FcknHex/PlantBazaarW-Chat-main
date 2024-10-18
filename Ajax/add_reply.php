<?php
include "../conn.php";

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the request
$message = $_POST['message'];
$sender_id = $_POST['sender_id'];
$receiver_id = $_POST['receiver_id'];
$reply_to = $_POST['reply_to'] ?? null; // Optional reply_to parameter

// Prepare the SQL query
$query = "INSERT INTO messages (sender_id, receiver_id, message, reply_to) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);

// Check if prepare failed
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}

// Bind parameters
$stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $reply_to);
$stmt->execute();

// Check for errors
if ($stmt->error) {
    echo "Error: " . htmlspecialchars($stmt->error);
} else {
    echo "Message sent successfully!";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
