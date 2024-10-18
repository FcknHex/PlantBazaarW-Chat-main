<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "gabplant";
if ($conn = mysqli_connect($host, $user, $pass, $db)) {
} else {
    echo "Connection failed";
}
?>