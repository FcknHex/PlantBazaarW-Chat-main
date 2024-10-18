<?php 
session_start();
include "../conn.php";

$logged_in_user_id = $_SESSION['user_id'];

// Query to fetch users who have conversations with the logged-in user
$query = "
    SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.proflePicture
    FROM users u
    INNER JOIN messages m ON (u.id = m.sender_id AND m.receiver_id = ?) 
                         OR (u.id = m.receiver_id AND m.sender_id = ?)
    WHERE u.id != ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('iii', $logged_in_user_id, $logged_in_user_id, $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
    $id = $row['id'];
    $email = $row['email'];
    $profilePic = $row['proflePicture'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $fullname = $firstname . " " . $lastname;

    // Only show users who have had conversations with the logged-in user
    echo "<div class='user' id='$id' data-username='$fullname' data-profilePic='$profilePic'>
            <img src='../ProfilePictures/$profilePic'>
            <h5>$fullname</h5>
            <div class='message-notification'>
                <p class='message-preview'></p>
                <p class='time-stamp'></p>
            </div>
        </div>";
}

$stmt->close();
$conn->close();
?>
