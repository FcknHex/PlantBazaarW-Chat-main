<?php
session_start();
include '../conn.php'; // Include your connection file

if (isset($_GET['id'])) {
    $sellerId = $_GET['id'];

    // Fetch seller info
    $query = "SELECT s.*, u.email, u.phoneNumber, u.address FROM sellers s JOIN users u ON s.user_id = u.id WHERE s.seller_id = $sellerId";
    $result = mysqli_query($conn, $query);
    $seller = mysqli_fetch_assoc($result);

    if ($seller) {
        echo json_encode($seller); // Send back seller data in JSON format
    } else {
        echo json_encode(['error' => 'Seller not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
