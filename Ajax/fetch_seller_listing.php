<?php
include '../conn.php';
session_start();

if (isset($_GET['sellerId'])) {
    $sellerId = $_GET['sellerId'];

    // Fetch seller's listings along with seller email
    $listingsQuery = "SELECT p.*, u.email as seller_email FROM product p 
                      JOIN sellers s ON p.added_by = s.user_id 
                      JOIN users u ON s.user_id = u.id 
                      WHERE p.added_by = ?";
    $listingsStmt = $conn->prepare($listingsQuery);
    $listingsStmt->bind_param("i", $sellerId);
    $listingsStmt->execute();
    $listingsResult = $listingsStmt->get_result();

    $listings = [];
    if ($listingsResult->num_rows > 0) {
        while ($listing = $listingsResult->fetch_assoc()) {
            $listings[] = $listing; // Store each listing in the array
        }
        echo json_encode($listings);
    } else {
        echo json_encode(['error' => 'No listings available for this seller.']);
    }
} else {
    echo json_encode(['error' => 'No seller ID provided.']);
}
?>