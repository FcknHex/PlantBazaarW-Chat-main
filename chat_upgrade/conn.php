<?php 
$conn = mysqli_connect("localhost", "root", "", "gabplant");
if(!$conn){
    echo "Connection error: ". mysqli_connect_error();
    
}
?>