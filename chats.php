<?php 
session_start();
include "conn.php"; 

$email=$_SESSION['email'];
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index");
    exit();
}

    $query = "SELECT id, proflePicture, firstname, lastname FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $profilePic = $user['proflePicture'];  // Assuming you store the path to the profile picture
        $userId = $user['id'];
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System</title>
    <link rel="stylesheet" href="chatStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<div class="container">
    <div class="listofusers">
        <!-- User list will be displayed here via AJAX -->
    </div>
        <div class="chat-container">
            <div class="chat-header">
                <h2> <span class="receiver-username">Select a user to chat</span></h2>
            </div>
            <div class="chat-messages">
                <!-- Chat messages will display here -->
            </div>
            
            <!-- Reply Indicator -->
                <!-- Reply Indicator -->
                <!-- Reply Indicator -->
        <div class="reply-indicator" id="reply-indicator" style="display: none; background-color: #f0f0f0; padding: 8px; border-left: 3px solid #007bff;">
            <strong>Replying to:</strong> <span id="reply-message-display"></span>
            <img id="reply-image-display" src="" alt="Replying to image" style="width: 50px; height: auto; display: none; margin-left: 10px;" />
            <button id="dismiss-reply" style="cursor: pointer; background-color: transparent; border: none; font-size: 16px; margin-left: 10px;">X</button>
        </div>



            <div class="chat-form">
                <textarea id="message-input" placeholder="Type your message..."></textarea>
                <input type="file" id="file-input">
                <button id="send-button">Send</button>

                
            </div>
        </div>
    </div>
    
    <a href="logout.php"><input type="button" value="Logout"></a>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    
    <script>
        
       $(document).ready(function() {
        const textarea = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const maxHeight = 200; // Set your desired maximum height here
        const defaultHeight = 50; // Set your desired default height here

// Initial setup to set the height to the default height when the page loads
textarea.style.height = defaultHeight + 'px';

textarea.addEventListener('input', function() {
    // Reset the height to 'auto' to allow it to shrink when the content is smaller
    this.style.height = 'auto';

    // Get the current content height
    const contentHeight = this.scrollHeight;

    // Set the height based on content
    if (contentHeight === 0) {
        this.style.height = defaultHeight + 'px'; // Reset to default height if empty
        this.style.overflowY = 'hidden'; // Hide the vertical scrollbar
    } else if (contentHeight <= maxHeight) {
        this.style.height = contentHeight + 'px'; // Dynamically set the height
        this.style.overflowY = 'hidden'; // Hide the vertical scrollbar
    } else {
        this.style.height = maxHeight + 'px'; // Set height to maxHeight
        this.style.overflowY = 'scroll'; // Enable vertical scrollbar
    }
});

// Function to send the message and reset the textarea height
// sendButton.addEventListener('click', function() {
//     const message = textarea.value.trim();

//     if (message) {
//         // Here you would normally send the message to your chat application
//         console.log("Message sent:", message); // For demonstration purposes

//         // Reset the textarea after sending the message
//         textarea.value = ''; // Clear the input
//         textarea.style.height = defaultHeight + 'px'; // Reset to default height
//         textarea.style.overflowY = 'hidden'; // Hide the vertical scrollbar
//     }
// });
    

        function autoScroll() {
        var chatMessages = $('.chat-messages');
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
        console.log("Auto-scrolled to bottom. Height:", chatMessages[0].scrollHeight);
    }

    // Scroll to the bottom on page load
    $(window).on('load', function() {
        autoScroll(); // Ensure the chat starts at the bottom
    });

    var sender_id = <?php echo $userId;?>// Logged-in user's ID
    var receiver_id; // To store the ID of the selected receiver
    var replyToMessageId = null; // Default no reply

    // Initially hide the reply indicator
    $('#reply-indicator').hide();

    // Load users on page load
    function loadUsers() {
        $.ajax({
            url: "Ajax/fetch_users.php",
            method: "GET",
            success: function(data) {
                $(".listofusers").html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading users:", error);
            }
        });
    }

    loadUsers(); // Call to load users

    // Handle user selection
    $(document).on('click', '.user-item', function() {
        receiver_id = $(this).data("id"); // Get the receiver's ID
        $(".receiver-username").text($(this).text()); // Update the chat header with the receiver's name
        loadMessages(); // Load the messages for the selected user
    });

    // Load messages function
    function loadMessages() {
        if (receiver_id) {
            $.ajax({
                url: "Ajax/fetch_messages.php",
                method: "GET",
                data: { sender_id: sender_id, receiver_id: receiver_id },
                success: function(data) {
                    $(".chat-messages").html(data); // Display messages
                    autoScroll(); // Scroll to the bottom after loading messages
                },
                error: function(xhr, status, error) {
                    console.error("Error loading messages:", error);
                }
            });
        }
    }

    // Send message function
    $('#send-button').click(function() {
    var message = $('#message-input').val().trim(); // Trim any extra whitespace

    var formData = new FormData();
    // Check if message is not empty or if there's a file
    if (message.length > 0 || $('#file-input')[0].files.length > 0) {
        formData.append('message', message);
        formData.append('sender_id', sender_id);
        formData.append('receiver_id', receiver_id);
        formData.append('file', $('#file-input')[0].files[0]);
        
        
        if (replyToMessageId) {
            formData.append('reply_to', replyToMessageId); // Add reply_to if replying
        }

        $.ajax({
            url: 'Ajax/add_message.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Clear the input and file input
                $('#message-input').val(''); // Clear input
                $('#file-input').val(''); // Clear file input
                replyToMessageId = null; // Clear reply ID after sending
                $('#reply-indicator').hide(); // Hide reply indicator
                loadMessages(); // Refresh messages after sending
                
                // Reset the textarea height back to the default height
                $('#message-input').css('height', '50px'); // Change '50px' to your default height
                $('#message-input').css('overflow-y', 'hidden'); // Hide vertical scrollbar
            },
            error: function(xhr, status, error) {
                console.error("Error sending message:", error);
            }
        });
    } else {
        alert("Please enter a message or select a file to send."); // Alert if nothing is entered
    }
});

    // Handle reply button click
    $(document).on('click', '.reply-button', function() {
    var messageId = $(this).closest('.message').data('id'); // Get the ID of the message being replied to
    var messageText = $(this).closest('.message').find('.message-text').text(); // Get the text of the message
    var replyImage = $(this).data('reply-image'); // Get the image source from the button data attribute

    replyToMessageId = messageId; // Store the reply ID
    $('#reply-message-display').text(messageText); // Show the message text in the reply indicator

    // Show the reply image if it exists
    if (replyImage) {
        $('#reply-image-display').attr('src', 'ajax/' + replyImage).show(); // Set the image source and show it
    } else {
        $('#reply-image-display').hide(); // Hide the image display if there's no image
    }

    $('#reply-indicator').show(); // Show the reply indicator
    $('#message-input').focus(); // Focus the message input for quick replying
});
       });

// Dismiss reply indicator
$('#dismiss-reply').click(function() {
    $('#reply-indicator').hide(); // Hide the reply indicator
    replyToMessageId = null; // Clear reply ID
    $('#reply-image-display').hide(); // Hide the reply image
});

            function openFullScreen(image) {
            var fullScreenDiv = document.createElement('div');
            fullScreenDiv.style.position = 'fixed';
            fullScreenDiv.style.top = 0;
            fullScreenDiv.style.left = 0;
            fullScreenDiv.style.width = '100%';
            fullScreenDiv.style.height = '100%';
            fullScreenDiv.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
            fullScreenDiv.style.display = 'flex';
            fullScreenDiv.style.alignItems = 'center';
            fullScreenDiv.style.justifyContent = 'center';
            fullScreenDiv.style.zIndex = 1000;

            var fullScreenImage = document.createElement('img');
            fullScreenImage.src = image.src; // Set the source to the clicked image
            fullScreenImage.style.maxWidth = '90%'; // Limit max width
            fullScreenImage.style.maxHeight = '90%'; // Limit max height

            fullScreenDiv.onclick = function() {
                document.body.removeChild(fullScreenDiv);
            };

            fullScreenDiv.appendChild(fullScreenImage);
            document.body.appendChild(fullScreenDiv);
        }

        // Send message when Enter key is pressed
        $('#message-input').keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) { // Check if Enter key is pressed and Shift is not held
                e.preventDefault(); // Prevent default action (newline in textarea)
                $('#send-button').click(); // Trigger the click event on the send button
            }
        });

// Function to scroll to the bottom on load
document.addEventListener('DOMContentLoaded', () => {
    const chatMessages = document.querySelector('.chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight; // Start at the bottom
});

// Call this function whenever you add a new message
function addNewMessage() {
    // Code to add a new message to the chat
    // ...

    // Get the chat messages container
    const chatMessages = document.querySelector('.chat-messages');
    
    // Determine if user is at the bottom
    const isAtBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= chatMessages.scrollTop + 1;

    // Scroll to the bottom if the user is already at the bottom
    if (isAtBottom) {
        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the latest message
    }
}
function showReplyIndicator(message, imageUrl = null) {
    // Show the reply indicator
    document.getElementById('reply-indicator').style.display = 'block';

    // Set the message being replied to
    document.getElementById('reply-message-display').textContent = message;

    // Set the image if there's one being replied to
    if (imageUrl) {
        const replyImage = document.getElementById('reply-image-display');
        replyImage.src = imageUrl;
        replyImage.style.display = 'inline'; // Make sure the image is displayed
    } else {
        document.getElementById('reply-image-display').style.display = 'none'; // Hide image if not replying to one
    }
}

// Function to dismiss the reply indicator
document.getElementById('dismiss-reply').addEventListener('click', function() {
    // Hide the reply indicator
    document.getElementById('reply-indicator').style.display = 'none';
    document.getElementById('reply-message-display').textContent = '';
    document.getElementById('reply-image-display').src = '';
});
showReplyIndicator('This is the text I’m replying to');
showReplyIndicator('This is the text I’m replying to', 'path-to-image.jpg');


function loadUsers() {
    $.ajax({
        url: 'ajax/fetch_users.php',
        method: 'GET',
        success: function(data) {
            $('#user-list').html(data);
        },
        error: function(xhr, status, error) {
            console.error("Error loading users:", error);
        }
    });
}

function loadMessages() {

    $.ajax({
        url: 'Ajax/fetch_messages.php',
        method: 'GET',
        success: function(data) {
            $('#chat-messages').html(data);
        },
        error: function(xhr, status, error) {
            console.error("Error loading messages:", error);
        }
    });
}

function pollForNewMessages() {
    setInterval(function() {
        $.ajax({
            url: 'ajax/checking_for_new_message.php',
            method: 'GET',
            success: function(data) {
                if (data.hasNewMessages === true) {
                    loadUsers();
                    loadMessages();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error checking for new messages:", error);
            }
        });
    }, 5000); // Check every 5 seconds
}


$(document).ready(function() {
    loadUsers();
    loadMessages();
    pollForNewMessages();

    
});



    </script>
</body>
</html>
