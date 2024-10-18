<?php 
session_start();
include "../conn.php";
$logged_in_user_id = $_SESSION['user_id'];

$stmt = "SELECT * FROM users;";
$result = mysqli_query($conn, $stmt);
while($row = mysqli_fetch_assoc($result)){
    $id = $row['id'];
    $email = $row['email'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $fullname = $firstname . " " . $lastname;
    // $username = $row['username'];
    if ($id != $logged_in_user_id) {
    echo "<div class='user' id=$id data-username='$fullname' data-email='$email'>
    <h5>$fullname</h5>

     <div class='message-notifcation'>
        <p class='message-preview'></p>
        <p class='time-stamp'></p>
    </div>
    </div>";
    }
    ?>
   
    <?php 
}

 

?>