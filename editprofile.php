<?php
include 'conn.php';
session_start();

// Start output buffering
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit;
}

// Retrieve the user's data from the database
$email = $_SESSION['email'];
$query = "SELECT id, firstname, lastname, phonenumber, address FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $userId = $user['id'];
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $phonenumber = $user['phonenumber'];
    $address = $user['address'];
} else {
    echo 'Error retrieving user data';
    exit;
}

$updated = false;

// Handle form submission
if (isset($_POST['submit'])) {
    $newFirstname = $_POST['firstname'];
    $newLastname = $_POST['lastname'];
    $newPhoneNumber = $_POST['phonenumber'];
    $newAddress = $_POST['address'];

    // Check if any data has changed
    if ($newFirstname != $firstname || $newLastname != $lastname || $newPhoneNumber != $phonenumber || $newAddress != $address) {
        // Update the user's data in the database
        $query = "UPDATE users SET firstname = ?, lastname = ?, phonenumber = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $newFirstname, $newLastname, $newPhoneNumber, $newAddress, $userId);

        if ($stmt->execute()) {
            // Set updated flag to true
            $updated = true;
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="editprofile.css">

    <!-- SweetAlert Library -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="firstname">Firstname:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>"><br><br>
        <label for="lastname">Lastname:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>"><br><br>
        <label for="phonenumber">Phone Number:</label>
        <input type="text" id="phonenumber" name="phonenumber" value="<?php echo $phonenumber; ?>"><br><br>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>"><br><br>
        <input type="submit" name="submit" value="Update Profile">
    </form>

    <!-- Check if the profile was updated, then trigger SweetAlert -->
    <?php if ($updated): ?>
        <script>
            swal({
                title: "Profile Updated!",
                text: "Your profile has been updated successfully",
                icon: "success",
                button: "Ok",
            }).then(function() {
                window.location.href = "editprofile.php";
            });
        </script>
    <?php endif; ?>
</body>
</html>
