<?php
include '../conn.php';
// Get the plant ID from the request
session_start();
$plantId = $_GET['plantid'];

$email = $_SESSION['email'];

// Check if the connection is successful
if (!$conn) {
  die('Connection failed: ' . mysqli_connect_error());
}

// Get the listing data from the database
$sql = "SELECT * FROM product WHERE plantid = '$plantId'";
$result = mysqli_query($conn, $sql);
$listingData = mysqli_fetch_assoc($result);

// Upload the listing to product_archive
$sql = "INSERT INTO product_archive (archiveID, postedBy, postPlantName, price, plantcategories, plantSize, location, img1, img2, img3) VALUES ('$listingData[plantid]', '$email', '$listingData[plantname]', '$listingData[price]', '$listingData[plantcategories]', '$listingData[plantSize]', '$listingData[location]', '$listingData[img1]', '$listingData[img2]', '$listingData[img3]')";
$result = mysqli_query($conn, $sql);

// Move images to Products_Archive folder
$archiveFolder = '../Products_Archive/' . $email . '/';
if (!is_dir($archiveFolder)) {
  mkdir($archiveFolder, 0777, true);
}

$imageFiles = array($listingData['img1'], $listingData['img2'], $listingData['img3']);
echo "Image files: " . implode(", ", $imageFiles) . "<br>";

foreach ($imageFiles as $imageFile) {
  $originalImagePath = '../Products/' . $email . '/' . $imageFile;
  $archiveImagePath = $archiveFolder . $imageFile;
  echo "Original image path: $originalImagePath<br>";
  echo "Archive image path: $archiveImagePath<br>";

  if (file_exists($originalImagePath)) {
    if (rename($originalImagePath, $archiveImagePath)) {
      echo "Image moved to Products_Archive folder successfully.<br>";
    } else {
      echo "Error moving image: " . error_get_last()['message'] . "<br>";
    }
  } else {
    echo "Error: Image file does not exist.<br>";
  }
}

// Delete the listing from the database
$sql = "DELETE FROM product WHERE plantid = '$plantId'";
$result = mysqli_query($conn, $sql);

// Check if the deletion is successful
if ($result) {
  echo "Listing deleted successfully.";
  header("Location: seller_dashboard.php");
  exit;
} else {
  echo "Error deleting listing.";
}

// Close the database connection
mysqli_close($conn);
?>

