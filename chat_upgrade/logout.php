<?php 
session_start(); // Start the session if it hasn't been started

// Clear all session variables
session_unset(); // Optional but often used to clear specific session variables
session_destroy(); // Destroy the session

// Redirect to login page
header("location: login.php");
exit(); // Optional but good practice to call exit after a redirect
?>
