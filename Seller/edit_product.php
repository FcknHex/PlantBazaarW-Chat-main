<?php
// Include the database connection file
include '../conn.php';
session_start();


// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the form data
  $plantid = $_POST['plantid'];
  $plantname = $_POST['editplantname'];
  $price = $_POST['editPrice'];
  $plantdetails = $_POST['editplantdetails'];
  $plantcategories = $_POST['editPlantcategories'];
  $plantsize = $_POST['editPlantSize'];
  $location = $_POST['editplantlocation'];
  $region = $_POST['editregion'];
  $province = $_POST['editprovince'];
  $city = $_POST['editcity'];
  $barangay = $_POST['editbarangay'];
  $street = $_POST['editstreet'];
  
  // Retrieve the existing image names from the database
  $query = "SELECT img1, img2, img3 FROM product WHERE plantid = '$plantid'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  $existingImg1 = $row['img1'];
  $existingImg2 = $row['img2'];
  $existingImg3 = $row['img3'];
  // Check if the image fields are empty
    if (empty($_FILES['img1']['name'])) {
        $img1 = $existingImg1; // retain the existing image
    } else {
        // upload and process the new image
        $img1 = $_FILES['img1']['name'];
    }

    if (empty($_FILES['img2']['name'])) {
        $img2 = $existingImg2; // retain the existing image
    } else {
        // upload and process the new image
        $img2 = $_FILES['img2']['name'];
    }

    if (empty($_FILES['img3']['name'])) {
        $img3 = $existingImg3; // retain the existing image
    } else {
        // upload and process the new image
        $img3 = $_FILES['img3']['name'];
    }

  // Check if the plant ID is valid
  $query = "SELECT * FROM product WHERE plantid = '$plantid'";
  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) == 0) {
    echo "Error: Plant ID is invalid.";
    exit;
  }

  // Update the plant data
  $query = "UPDATE product SET plantname = '$plantname', price = '$price',details = '$plantdetails', plantcategories = '$plantcategories', plantsize = '$plantsize', plantcolor = '$plantcolor', region = '$region', province = '$province', city = '$city', barangay = '$barangay', street = '$street', location = '$location', img1 = '$img1', img2 = '$img2', img3 = '$img3' WHERE plantid = '$plantid'";
  $result = mysqli_query($conn, $query);
  if (!$result) {
    echo "Error: Unable to update plant data.";
    exit;
  }
//   Upload images
if (!empty($img1)) {
    $target_dir = "../Products/".$_SESSION['email']. "/";
    $target_file = $target_dir . basename($_FILES["img1"]["name"]);
    move_uploaded_file($_FILES["img1"]["tmp_name"], $target_file);
}

if (!empty($img2)) {
    $target_dir = "../Products/".$_SESSION['email']. "/";
    $target_file = $target_dir . basename($_FILES["img2"]["name"]);
    move_uploaded_file($_FILES["img2"]["tmp_name"], $target_file);
}

if (!empty($img3)) {
    $target_dir = "../Products/".$_SESSION['email']. "/";
    $target_file = $target_dir . basename($_FILES["img3"]["name"]);
    move_uploaded_file($_FILES["img3"]["tmp_name"], $target_file);
}


  echo "Plant data updated successfully.";
  exit;
}

 
?>