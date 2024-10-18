<?php
include '../conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $sellerId = $_POST['sellerId'];
    $rating = $_POST['rating'];

    // Check if the user has already rated this seller
    $checkRatingQuery = "SELECT * FROM ratings WHERE user_id = ? AND seller_id = ?";
    $checkRatingStmt = $conn->prepare($checkRatingQuery);
    $checkRatingStmt->bind_param("ii", $userId, $sellerId);
    $checkRatingStmt->execute();
    $ratingResult = $checkRatingStmt->get_result();

    if ($ratingResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already rated this seller.']);
    } else {
        // Insert rating into database
        $insertRatingQuery = "INSERT INTO ratings (user_id, seller_id, rating) VALUES (?, ?, ?)";
        $insertRatingStmt = $conn->prepare($insertRatingQuery);
        $insertRatingStmt->bind_param("iii", $userId, $sellerId, $rating);
        $insertRatingStmt->execute();
        echo json_encode(['success' => true, 'message' => 'Thank you for your rating!']);
    }

    $checkRatingStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
}
?>