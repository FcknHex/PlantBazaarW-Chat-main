<?php
session_start();

// Destroy the session to log out the user
session_unset();
session_destroy();

// Return a success message
echo "success";
?>
