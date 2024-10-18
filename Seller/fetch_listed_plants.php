<?php
include '../conn.php';
session_start();

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$email = $_SESSION['email'];

// Fetch seller ID based on email
$query = "SELECT id, email FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$userId = $user['id'];
$sellerEmail = $user['email'];

// Fetch products listed by this seller
$productQuery = "SELECT *, (SELECT email FROM sellers WHERE user_id = users.id) AS seller_email FROM product JOIN sellers ON product.added_by = sellers.seller_id JOIN users ON sellers.user_id = users.id WHERE users.email = '$email'";
$productResult = mysqli_query($conn, $productQuery);

// Check for errors in the product query
if (!$productResult) {
    die("Query failed: " . mysqli_error($conn));
}

$products = [];
while ($product = mysqli_fetch_assoc($productResult)) {
    $products[] = $product;
}

echo json_encode($products);
?>