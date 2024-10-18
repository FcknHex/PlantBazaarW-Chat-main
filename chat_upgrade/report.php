<?php 
session_start();
include "conn.php"; // Include your database connection

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $reported_user_id = $_POST['reported_user_id'];
    $report_reason = $_POST['report_reason'];
    $description = $_POST['description'];

    // Handle file uploads for proof images
    $proof_images = array_fill(1, 6, null); // Initialize an array with 6 null values

    for ($i = 1; $i <= 6; $i++) {
        if (!empty($_FILES["proof_img_$i"]['name'])) {
            $target_dir = "uploads/proof_images/";
            $file_name = basename($_FILES["proof_img_$i"]["name"]);
            $target_file = $target_dir . $file_name;

            // Check if the directory exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["proof_img_$i"]["tmp_name"], $target_file)) {
                $proof_images[$i] = $target_file; // Save the uploaded file path
            }
        }
    }

    // Insert report into database
    $sql = "INSERT INTO reports (user_id, reported_user_id, report_reason, description, proof_img_1, proof_img_2, proof_img_3, proof_img_4, proof_img_5, proof_img_6, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    
    // Prepare variables for bind_param. Replace empty file paths with NULL
    for ($i = 1; $i <= 6; $i++) {
        $proof_images[$i] = empty($proof_images[$i]) ? null : $proof_images[$i];
    }

    // Bind parameters: provide NULL where no image was uploaded
    $stmt->bind_param(
        "iissssssss", 
        $user_id, 
        $reported_user_id, 
        $report_reason, 
        $description, 
        $proof_images[1], 
        $proof_images[2], 
        $proof_images[3], 
        $proof_images[4], 
        $proof_images[5], 
        $proof_images[6]
    );

    if ($stmt->execute()) {
        echo "<div class='message success'>Report submitted successfully!</div>";
    } else {
        echo "<div class='message error'>Error: " . $stmt->error . "</div>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report User</title>
    <link rel="stylesheet" href="report.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container">
        <h1>Report User</h1>
        <form action="report.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="reported_user_id" value="<?php echo $_GET['user_id']; ?>">
            <label for="report_reason">Reason for Report:</label>
            <select name="report_reason" id="report_reason" required>
                <option value="Prohibited item">Prohibited item</option>
                <option value="Scam">Scam</option>
                <option value="Counterfeit">Counterfeit</option>
                <option value="Offensive">Offensive messages/images</option>
                <option value="Other">Other</option>
            </select>
            
            <label for="description">Description:</label>
            <textarea name="description" id="description" placeholder="Further elaborate on your selected reason"></textarea>
            
            <label for="proof_img_1">Proof Image 1:</label>
            <input type="file" name="proof_img_1" accept="image/*">
            <label for="proof_img_2">Proof Image 2:</label>
            <input type="file" name="proof_img_2" accept="image/*">
            <label for="proof_img_3">Proof Image 3:</label>
            <input type="file" name="proof_img_3" accept="image/*">
            <label for="proof_img_4">Proof Image 4:</label>
            <input type="file" name="proof_img_4" accept="image/*">
            <label for="proof_img_5">Proof Image 5:</label>
            <input type="file" name="proof_img_5" accept="image/*">
            <label for="proof_img_6">Proof Image 6:</label>
            <input type="file" name="proof_img_6" accept="image/*">
            
            <button type="submit">Submit Report</button>
        </form>
        <a href="index.php" class="back-btn">Back to Messages</a>
    </div>

    <script src="path/to/your/script.js"></script> <!-- Link to your JavaScript file -->
</body>
</html>
