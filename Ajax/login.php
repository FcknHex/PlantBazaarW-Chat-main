<?php
include '../conn.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("s", $email);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if($user['password'] == $password) {
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(array('success' => true, 'message' => 'Successfully logged in'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Invalid password'));
        }
    } else {
        echo json_encode(array('success' => false, 'message' => 'Email not found'));
    }
}
?>