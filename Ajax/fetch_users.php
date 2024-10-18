<?php
include "../conn.php"; // Update this based on your folder structure

session_start();

$email=$_SESSION['email'];

$query = "SELECT id, proflePicture, firstname, lastname FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $profilePic = $user['proflePicture'];  // Assuming you store the path to the profile picture
    $userId = $user['id'];
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
}

$user_id = $userId; // Assuming you have a session variable for logged-in user

$query = "SELECT * FROM users WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<div class="user-item" data-id="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['email']) . '</div>';
}

$stmt->close();
$conn->close();
?>
<Script>
function loadUsers() {
    $.ajax({
        url: 'fetch_users.php', // This script fetches the list of users
        method: 'GET',
        success: function(data) {
            $('#user-list').html(data); // Update user list with new data
        },
        error: function(xhr, status, error) {
            console.error("Error loading users:", error);
        }
    });
}

function loadMessages() {
    $.ajax({
        url: 'fetch_messages.php', // This script fetches the messages
        method: 'GET',
        success: function(data) {
            $('#chat-messages').html(data); // Update chat messages with new data
        },
        error: function(xhr, status, error) {
            console.error("Error loading messages:", error);
        }
    });
}
</script>