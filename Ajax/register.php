<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $firstname = ucfirst(strtolower(trim($_POST['firstname'])));
    $lastname = ucfirst(strtolower(trim($_POST['lastname'])));
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
    $phonenumber = filter_var($_POST['phonenumber'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $profilePicture = '';

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email address';
        exit;
    }

    // Validate password (at least 8 characters, including numbers and special characters)
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z\d]/', $password)) {
        echo 'Password must be at least 8 characters long and include at least one number and one special character.';
        exit;
    }

    // Check if a profile picture is uploaded
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === 0) {
        $targetDir = "../ProfilePictures/";
        
        // Sanitize email and generate unique file name using the email and original file extension
        $sanitizedEmail = preg_replace('/[^a-zA-Z0-9_-]/', '_', $email); // Replace non-alphanumeric characters with '_'
        $imageFileType = strtolower(pathinfo($_FILES["profilePicture"]["name"], PATHINFO_EXTENSION));
        $targetFile = $targetDir . $sanitizedEmail . "." . $imageFileType;

        // Validate file type
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedFileTypes)) {
            // Create directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Upload file with the sanitized email as the file name
            if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) {
                $profilePicture = $sanitizedEmail . "." . $imageFileType;
            } else {
                echo 'File upload error';
                exit;
            }
        } else {
            echo 'Invalid file type';
            exit;
        }
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database using prepared statements to prevent SQL injection
    $query = "INSERT INTO users (email, password, firstname, lastname, gender, phonenumber, address, proflePicture) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssssssss", $email, $hashedPassword, $firstname, $lastname, $gender, $phonenumber, $address, $profilePicture);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'Database error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'Database error: ' . $conn->error;
    }
}
?>
