<?php
session_start();
include "../conn.php"; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['user_id'], $_POST['message'], $_POST['seller_email'])) {
        $user_id = $_SESSION['user_id'];
        $seller_email = mysqli_real_escape_string($conn, $_POST['seller_email']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);

        // Fetch the seller ID based on the email
        $query = "SELECT id FROM users WHERE email = '$seller_email'";
        $result = mysqli_query($conn, $query);
        $seller = mysqli_fetch_assoc($result);
        
        if ($seller) {
            $seller_id = $seller['id'];

            // Insert the message into the database
            $insertQuery = "INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param('iis', $user_id, $seller_id, $message);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]); // Respond success
            } else {
                echo json_encode(['success' => false, 'error' => 'Message not sent.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Seller not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    }
}

$conn->close();
?>
