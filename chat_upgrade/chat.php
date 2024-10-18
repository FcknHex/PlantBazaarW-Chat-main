<?php 
session_start();
include "conn.php";

// Check if the user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
    exit();
}

// Get seller's email from the query parameter
$sellerEmail = isset($_GET['seller_email']) ? mysqli_real_escape_string($conn, $_GET['seller_email']) : '';

if ($sellerEmail === '') {
    // Redirect if no seller email is provided
    header("location: some_page.php"); // Change to a valid page
    exit();
}

// Fetch seller's details from the database
$sellerQuery = "SELECT * FROM users WHERE email = '$sellerEmail'";
$sellerResult = mysqli_query($conn, $sellerQuery);
$seller = mysqli_fetch_assoc($sellerResult);

// Check if seller exists
if (!$seller) {
    header("location: some_page.php"); // Change to a valid page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Seller</title>
    <link rel="stylesheet" href="chat.css">
</head>
<body>
    <div class="container">
        <a href="../index.php">Back</a>
      
        <div class="chat-container">
            <h1>Chat with <?php echo htmlspecialchars($seller['firstname'] . ' ' . $seller['lastname']); ?></h1>
            <div class="message-container" id="message-container">
                <!-- Messages will be loaded here -->
            </div>
            <div class="reply-indicator" style="display: none;">
                <p class="reply-message">
                    <strong>Replying to: </strong>
                    <span class="reply-text"></span>
                    <span class="close-reply" style="cursor: pointer; color: red; margin-left: 10px;">&times;</span>
                </p>
            </div>

            <form class="message-form" id="messageForm">
                <input type="text" name="message" id="message" placeholder="Type your message here" required>
                <button id="send" type="submit">Send</button>
            </form>
        </div>
    </div>

    <a href="logout.php">Logout</a>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const sellerEmail = <?php echo json_encode($sellerEmail); ?>; // Pass seller email from PHP
            const userId = <?php echo json_encode($_SESSION['user_id']); ?>; // Pass user ID from session

            // Function to load messages
            function loadMessages() {
                $.ajax({
                    url: 'direct/display_messages.php',
                    method: 'GET',
                    data: { seller_email: sellerEmail, user_id: userId },
                    success: function(response) {
                        $('#message-container').html(response);
                        scrollToBottom();

                        // Enable reply functionality on message click
                        $('.message-item').on('click', function() {
                            const replyText = $(this).find('.message-text').text(); // Assuming messages have a .message-text class
                            $('.reply-text').text(replyText);
                            $('.reply-indicator').show();
                        });
                    }
                });
            }

            // Function to scroll to bottom
            function scrollToBottom() {
            var chatContainer = document.querySelector('.message-container');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }


            // Send message
            $('#messageForm').on('submit', function(event) {
                event.preventDefault(); // Prevent form submission
                const message = $('#message').val().trim();

                if (message !== '') {
                    $.ajax({
                        url: 'direct/send_message.php',
                        method: 'POST',
                        data: {
                            message: message,
                            seller_email: sellerEmail,
                            user_id: userId
                        },
                        success: function() {
                            $('#message').val(''); // Clear input
                            loadMessages(); // Refresh messages
                        }
                    });
                }
            });

            // Close reply indicator
            $(document).on('click', '.close-reply', function() {
                $('.reply-indicator').hide(); // Hide the reply indicator
                $('.reply-text').text(''); // Clear the reply text
            });

            // Load messages periodically
            setInterval(function() {
            var selectedUserId = $('.user.selected').attr('id');
            if (selectedUserId) {
                display_messages(selectedUserId);
            }
        }, 2000); // Adjust interval as needed
                });
    </script>
</body>
</html>
