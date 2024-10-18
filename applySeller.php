<?php
include 'conn.php';
session_start();

// Check if a user is logged in
$isLoggedIn = isset($_SESSION['email']) && !empty($_SESSION['email']);
$profilePic = ''; // Placeholder for the profile picture
$isSeller = false; // Flag to check if the user is a seller
$isSellerApplicant = false;

if ($isLoggedIn) {
    $email = $_SESSION['email'];

    // Query to get the profile picture from the database
    $query = "SELECT id, proflePicture, firstname, lastname FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $profilePic = $user['proflePicture'];  // Assuming you store the path to the profile picture
        $userId = $user['id'];
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
    }

    // If no profile picture is available, use a default image
    if (empty($profilePic)) {
        $profilePic = 'ProfilePictures/Default-Profile-Picture.png';  // Path to a default profile picture
    }

    // Query to check if the user is a seller
    $sellerQuery = "SELECT seller_id FROM sellers WHERE user_id = '$userId'";
    $sellerResult = mysqli_query($conn, $sellerQuery);

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $isSeller = true; // User is a seller
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <!-- <script src="antiInspect.js"></script> -->
    <title>Plant-Bazaar</title>
</head>
<body>
    <?php 
     $sellerApplicantQuery = "SELECT * FROM seller_applicant WHERE user_id = '$userId'";
     $sellerApplicantResult = mysqli_query($conn, $sellerApplicantQuery);
     
     if ($sellerApplicantResult && mysqli_num_rows($sellerApplicantResult) > 0) {
         $isSellerApplicant = true; // User is a seller applicant
         echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: 'You have already applied as a seller! ',
                     text: 'Wait for the admin to approve your application',
                 })
                 
                 setTimeout(function() {
                     window.location.href = 'index.php';
                 }, 3000);
         </script>";
         exit; // Stop further execution
     }
    ?>
    <?php include 'nav.php'; ?>

    <div class="container-seller">
        <h2>Apply as a Seller</h2>
    <div class="form-container">
        <form id="applySellerForm" enctype="multipart/form-data">

            <div class="id-upload">
                <img id="imagePreview" src="ProfilePictures/Default-Profile-Picture.png" alt="">
                <label for="validId">Upload Valid ID (e.g. Driver's License, Passport):</label>
                <label for="validId" class="upload-button">Upload Valid Id</label>
                <input type="file" id="validId" name="validId" accept="image/*" required>
            </div>
            <div class="selfieId-upload">
                <img id="imagePreview1" src="ProfilePictures/Default-Profile-Picture.png" alt="">
                <label for="selfieWithValidId">Upload Selfie with Valid ID:</label>
                <label for="selfieWithValidId" class="upload-button">Upload Selfie With Valid Id</label>
                <input type="file" id="selfieWithValidId" name="selfieWithValidId" accept="image/*" required>
            </div>
            <div class="form-submit">
            <div class="terms-and-conditions">
                <input type="checkbox" id="termsAndConditions" name="termsAndConditions" required>
                <label for="termsAndConditions">I agree to the terms and conditions</label>
            </div>
            <button type="submit" id="applyButton">Apply</button>
            </div>
        </form>
    </div>
    </div>
<script>
document.getElementById('validId').addEventListener('change', function() {
    const file = this.files[0];
    if (!file.type.startsWith('image/')) {
        Swal.fire({
        title: "The Internet?",
        text: "That thing is still around?",
        icon: "question"
        });
        this.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById('imagePreview').src = event.target.result;
        document.getElementById('imagePreview').style.border = "none";
    };
    reader.readAsDataURL(file);
});
document.getElementById('selfieWithValidId').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById('imagePreview1').src = event.target.result;
        document.getElementById('imagePreview1').style.border = "none";
    };
    reader.readAsDataURL(file);
});

$(document).ready(function() {
    $('#applySellerForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData object from the form

        $.ajax({
            url: 'Ajax/apply_as_seller.php', // URL to the PHP script that will handle the form submission
            type: 'POST', // POST request
            data: formData,
            contentType: false, // Tell jQuery not to set contentType
            processData: false, // Tell jQuery not to process the data
            success: function(response) {
                console.log(response);
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Successfully Applied",
                    showConfirmButton: true,
                    timer: 3000
                })
                setTimeout(function() {
                    window.location.href = "index.php";
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
            }
        });
    });
});
</script>
</body>
</html>