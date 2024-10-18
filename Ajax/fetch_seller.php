<?php
include '../conn.php';
session_start();

if (isset($_GET['sellerId'])) {
    $sellerId = $_GET['sellerId'];
    
    // Fetch seller's profile data
    $sellerQuery = "SELECT u.firstname, u.lastname, u.email, u.proflepicture, u.address, s.ratings 
                    FROM users u 
                    JOIN sellers s ON u.id = s.user_id 
                    WHERE s.seller_id = ?";
    $sellerStmt = $conn->prepare($sellerQuery);
    $sellerStmt->bind_param("i", $sellerId);
    $sellerStmt->execute();
    $sellerResult = $sellerStmt->get_result();
    $sellerData = $sellerResult->fetch_assoc();

    if ($sellerData) {
        echo json_encode($sellerData);
    } else {
        echo json_encode(['error' => 'Seller not found.']);
    }
} else {
    echo json_encode(['error' => 'No seller ID provided.']);
}
?>