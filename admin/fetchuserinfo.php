<?php
session_start();
include '../conn.php'; // Include your connection file

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'User ID is missing']);
    exit();
}

$userId = intval($_GET['id']);

// Fetch user information based on ID
$query = "SELECT email, gender, phoneNumber, address, password FROM users WHERE id = $userId";
$result = mysqli_query($conn, $query);

if ($result) {
    $userInfo = mysqli_fetch_assoc($result);
    if ($userInfo) {
        echo json_encode($userInfo);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'Database query failed']);
}

mysqli_close($conn); // Close the database connection
?>
