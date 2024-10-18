<?php
include '../conn.php'; // Include your connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Admin registered successfully!'); window.location.href='adminlogin.php';</script>";
    } else {
        echo "<script>alert('Registration failed!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Register</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
