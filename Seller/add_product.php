<?php
// Include the database connection file
include '../conn.php';
session_start();

$email = $_SESSION['email'];


// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the form data
  $plantname = $_POST['plantname'];
  $plantcolor = $_POST['plantcolor'];
  $plantsize = $_POST['plantsize'];
  $plantdetails = $_POST['plantdetails'];
  $price = $_POST['price'];
  $plantcategories = $_POST['plantcategories'];
  $location = $_POST['location'];
  $region = $_POST['region'];
  $province = $_POST['province'];
  $city = $_POST['city'];
  $barangay = $_POST['barangay'];
  $street = $_POST['street'];
  
  $img1 = $_FILES['img1']['name'];
  $img2 = $_FILES['img2']['name'];
  $img3 = $_FILES['img3']['name'];

  // Check if the image fields are empty
  if (empty($img1)) {
    $img1 = 'default-image.jpg';
  }
  if (empty($img2)) {
    $img2 = 'default-image.jpg';
  }
  if (empty($img3)) {
    $img3 = 'default-image.jpg';
  }

  // Upload the images
  $target_dir = "../Products/" . $email . "/";
  $target_file1 = $target_dir . basename($_FILES["img1"]["name"]);
  $target_file2 = $target_dir . basename($_FILES["img2"]["name"]);
  $target_file3 = $target_dir . basename($_FILES["img3"]["name"]);

  move_uploaded_file($_FILES["img1"]["tmp_name"], $target_file1);
  move_uploaded_file($_FILES["img2"]["tmp_name"], $target_file2);
  move_uploaded_file($_FILES["img3"]["tmp_name"], $target_file3);

  // Retrieve the seller ID from the sellers table
  $query = "SELECT seller_id FROM sellers WHERE user_id = (SELECT id FROM users WHERE email = '$email')";
  $result = mysqli_query($conn, $query);
  $seller_id = mysqli_fetch_assoc($result)['seller_id'];

  // Insert the data into the database
  $query = "INSERT INTO product (added_by, plantname, plantcolor, plantsize, details, price, plantcategories, region, province, city, barangay, street, img1, img2, img3) VALUES ('$seller_id', '$plantname', '$plantcolor', '$plantsize', '$plantdetails', '$price', '$plantcategories', '$region', '$province', '$city', '$barangay', '$street', '$img1', '$img2', '$img3')";
  $result = mysqli_query($conn, $query);

  // Output JSON data
  if (!$result) {
    echo json_encode(array('error' => 'Error: Unable to add product.'));
  } else {
    echo json_encode(array('success' => 'Product added successfully.'));
  }
}
?>