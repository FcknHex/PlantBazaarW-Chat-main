<?php
include '../conn.php';
// Get the plant ID from the request
$plantId = $_GET['plantid'];

// Query the product table to fetch the data
$query = "SELECT * FROM product WHERE plantid = '$plantId'";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result && mysqli_num_rows($result) > 0) {
  $productData = mysqli_fetch_assoc($result);
  echo json_encode($productData);
} else {
  echo json_encode(array('error' => 'Product not found'));
}

// Close the database connection
mysqli_close($conn);
?>