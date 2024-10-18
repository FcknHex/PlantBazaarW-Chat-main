<?php
include '../conn.php';
session_start();
$email = $_SESSION['email'];

// Get the user's ID from the database
$sql = "SELECT id FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $userId = $row['id'];
    }
} else {
    echo "0 results";
}

// Example code to handle the form submission and save the uploaded files
$target_dir = "../sellerApplicants/" . $email . "/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the uploaded files
    $validIdFile = $_FILES['validId']['name'];
    $selfieWithValidIdFile = $_FILES['selfieWithValidId']['name'];

    // Change the name of the files with their email and append "_validId" and "_selfieValidId"
    $validIdFileName = $email . "_validId." . pathinfo($validIdFile, PATHINFO_EXTENSION);
    $selfieValidIdFileName = $email . "_selfieValidId." . pathinfo($selfieWithValidIdFile, PATHINFO_EXTENSION);

    // Save the uploaded files to the desired directory
    $target_file1 = $target_dir . $validIdFileName;
    $target_file2 = $target_dir . $selfieValidIdFileName;

    move_uploaded_file($_FILES["validId"]["tmp_name"], $target_file1);
    move_uploaded_file($_FILES["selfieWithValidId"]["tmp_name"], $target_file2);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO seller_applicant (user_id, validId, selfieValidId) VALUES ('$userId', '$validIdFileName', '$selfieValidIdFileName')";

    if ($conn->query($sql) === true) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>