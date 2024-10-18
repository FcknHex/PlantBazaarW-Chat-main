<?php 
session_start();
include "conn.php";
if(isset($_SESSION['email'])){
    $username = $_SESSION['email'];
    $user = $_SESSION['user_id'];
}else{
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="chat.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php
    include "nav.php";
    ?>
    <div class="container">
    <!-- para sa sounds pag send  -->
    <audio style="display: none;" id="send-sound" src="sounds/send-sound.mp3" preload="auto"></audio>
    <audio style="display: none;" id="receive-sound" src="sounds/Notification-Sound.mp3" preload="auto"></audio>

        <!-- Fullscreen Image Overlay -->
         <!-- para sa picture  -->
    <div id="fullscreen-overlay" style="display: none;">
        <span id="close-overlay" style="cursor: pointer; color: white; position: absolute; top: 10px; right: 20px; font-size: 24px;">&times;</span>
        <img id="fullscreen-image" src="" alt="Full Screen" style="max-width: 100%; max-height: 100%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    </div>

        <div class="user-container">
            <!-- Burger icon to toggle user list -->
            <h2 class="message-header" >Chats <button style="display: none;" id="refresh-btn" class="refresh-btn" >refresh</button></h2>
            <!-- dito yung mga users -->
            <div class="user-list">
                
            </div>
        </div>

        <div class="chat-container">
            <div class="reciepient-header">
                <!-- static muna pero dito display ng username ng i memesage -->
                <button id="back-btn">
                <i class='bx bx-arrow-back'></i>
                </button>
                <div class="report-btn-container">
                <img src="" class="profile-picture" alt="">
                </div>
                <div class="report-btn-container">
                <h1 class="username" ></h1>
                <button class='report-btn' id="report-btn"><i class='fas fa-flag'></i></button>
                </div>
            </div>
            <!-- dito yung mga chats  -->
            <div class="message-container" id="message-container">
            </div>
             <!-- dito display para alam yung message ng rereplyan mo  -->
            <div class="reply-indicator">
                <p class="reply-message" style="display: none;">
                    <strong>Replying to: </strong>
                    <span class="reply-text"></span>
                    <span class="close-reply" style="cursor: pointer; color: red; margin-left: 10px;">&times;</span>
                </p>
                <div class="reply-image" style="display: none;">
                    <img src="" alt="Reply Image" class="reply-image-preview" style="max-width:50px; max-height: 50px; margin-top: 5px;">
                </div>
            </div>
             <!-- pang send ng messages  -->
            <div class="message-form-container">
            <form class="message-form" id="messageForm" enctype="multipart/form-data">
                <i class='bx bx-image-add bx-md' id="image-upload-icon"></i>
                <input class="message-input" type="text" name="message" id="message" placeholder="Type your message here">
                <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;">
                <button id="send" type="submit">Send</button>
            </form>
            </div>
        </div>
    </div>
 
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // eto naman para sa option sa messages reply and delete function 
    var openMessageId = null;
// Click event for ellipsis
$(document).on('click', '.ellipsis', function() {
    var optionsMenu = $(this).siblings('.options-menu');
    $('.options-menu').not(optionsMenu).hide(); // Hide other open menus
    // alert("clicked");
    // Toggle the current menu and store the message ID if it's open
    optionsMenu.toggle();
    if (optionsMenu.is(':visible')) {
        openMessageId = $(this).data('message-id'); // Store the message ID
    } else {
        openMessageId = null; // Reset if closed
    }
});
 // Back button to return to user list in mobile view
 $(document).on('click', '#back-btn', function() {
        $('.chat-container').removeClass('show-chat');
        $('.user-container').removeClass('hide-users');
        console.log('Back button clicked');
    });
    // Click event for delete button
    $(document).on('click', '.delete-btn', function() {
        var messageId = $(this).data('message-id');
        console.log('Delete message ID:', messageId);
        // Implement delete functionality here
    });
    // Hide options when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.message-options').length) {
            $('.options-menu').hide();
        }
    });
    // When the image upload icon is clicked para sa image to
    $('#image-upload-icon').on('click', function() {
        $('#image-upload').click();
    });
    $(document).on('click', '.message-image', function() {
        // Get the src of the clicked image
        var imageSrc = $(this).attr('src'); 
        // Set the src of the fullscreen image
        $('#fullscreen-image').attr('src', imageSrc);
        // Show the overlay
        $('#fullscreen-overlay').fadeIn();
    });
    // When the overlay is clicked
    $('#close-overlay').on('click', function() {
        $('#fullscreen-overlay').fadeOut(); // Hide the overlay
    });
    // Optional: Hide the overlay if the user clicks anywhere on the overlay
    $('#fullscreen-overlay').on('click', function() {
        $(this).fadeOut(); // Hide the overlay
    });
    function loadUsers() {
        // Fetching the users from the database
        $.ajax({
            url: "ajax/fetch_user.php",
            type: "POST",
            success: function(data) {
                $(".user-list").html(data);
                attachClickEvent();
            }
        });
    }

    loadUsers();
    // Adding click events to the users
    function attachClickEvent() {
        $('.user').on('click', function() {
            // Mark the clicked user as selected
            $('.user').removeClass('selected'); // Remove selected class from all users
            $(this).addClass('selected'); // Add selected class to the clicked user

            var selectedUserId = $('.user.selected').attr('id'); // This should capture the ID of the currently selected user
            var username = $(this).data('username');
            var profilepic = $(this).data('profilepic');
            console.log('Clicked user ID:', selectedUserId);
            console.log('Clicked username:', username);
            console.log('Clicked profile picture:', profilepic);
            // Assuming `username` is the variable with the username value
            var formattedUsername = username.charAt(0).toUpperCase() + username.slice(1);

            // Update the username and profile picture
            $('.profile-picture').attr('src','../ProfilePictures/' + profilepic);
            $('.username').text(formattedUsername);
            $('.report-btn').css('display', 'block');
            $('.message-form-container').css('display', 'block');
            console.log('Formatted username:', formattedUsername);
            console.log('Formatted profile picture:', profilepic);
            console.log('style display:', $('.report-btn').style('block'));
            // Display messages for the selected user immediately
            display_messages(selectedUserId);
            // Show chat container and hide user list in mobile view
            if ($(window).width() < 768) {
                $('.chat-container').addClass('show-chat');
                $('.user-container').addClass('hide-users');
            }
        });
    }
            loadUsers();

        // Load users
        function loadUsers() {
            $.ajax({
                url: "ajax/fetch_user.php",
                type: "GET",
                success: function(data) {
                    $(".user-list").html(data);
                    attachClickEvent();
                }
            });
        }
    function report_user(){
        $('.report-btn').on('click', function(event) {
            var selectedUserId = $('.user.selected').attr('id');

            if (!selectedUserId) {
                alert("Please select a user before reporting.");
                return;
            }
        })
    }
    // Send message function
   function send_message() {
    $('#send').on('click', function(event) {
        event.preventDefault(); // Prevent form submission and page reload

        var selectedUserId = $('.user.selected').attr('id'); // Get the selected user ID

        if (!selectedUserId) {
            alert("Please select a user before sending a message.");
            return; // Exit if no user is selected
        }

        var formData = new FormData($('#messageForm')[0]); // Create form data object, including the file

        var recipientId = selectedUserId; // Use the selected recipient ID
        var senderId = <?php echo $_SESSION['user_id']; ?>; // Get the sender ID from the session

        // Append sender_id and recipient_id to form data
        formData.append('sender_id', senderId);
        formData.append('recipient_id', recipientId);

        // Check if replying to a message
        var replyMessageId = $('.reply-message').data('message-id'); // Get the message ID being replied to
        if (replyMessageId) {
            formData.append('reply_to', replyMessageId); // Append this to the form data
        }

        $.ajax({
            url: 'ajax/send_message.php', // Your PHP file to handle saving the message and image
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('Message sent successfully:', response);

                $('#message').val(''); // Clear the input field after sending
                $('#image-upload').val(''); // Clear the file input after sending
                $('.reply-message').hide(); // Hide the reply indicator after sending

                // Reset the form
                $('#message').val('');
                var audio = document.getElementById("send-sound");
                audio.play();
                // Clear the reply-to data
                $('.reply-message').hide().data('message-id', null).html('');
                
                scrollToBottom();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
}
/// Reply button logic
$(document).on('click', '.reply-btn', function() {
    var messageId = $(this).data('message-id'); // Capture the message ID being replied to
    // Find the content of the message being replied to
    var messageContent = $(this).closest('.message-item').find('.message-text').text();
    // Find the image associated with the replied message, if any
    var replyImage = $(this).closest('.message-item').find('.message-image img').attr('src');
    // Create the reply HTML with the cancel button included
    var replyHtml = `
    <div class="reply-content" style="display: flex; align-items: center;">
        <strong>Replying to:</strong> ${messageContent}
        ${replyImage ? `<div class="replied-image" style="margin-left: 10px;"><img src="${replyImage}" alt="Replied Image" style="max-width: 100px; max-height: 100px; border-radius: 5px;" /></div>` : ''}
        <button class="cancel-reply" style="padding: 5px; cursor: pointer; margin-left: 10px;">X</button>
    </div>
    `;
    // Display the reply message
    $('.reply-message').show()
        .data('message-id', messageId) // Store the message ID in a data attribute
        .html(replyHtml);
    // Reset the form in case the user cancels or doesn't complete a previous reply
    $('#message').val(''); // Clear the message input to prepare for a new reply
});

// Cancel reply button logic
$(document).on('click', '.cancel-reply', function() {
    $('.reply-message').hide(); // Hide the reply message indicator
    $('.reply-message').data('message-id', null); // Clear the message ID being replied to
});
    send_message();
    report_user();
    // unction to display messages
    let isUserAtBottom = true;

// Function to scroll to the bottom of the message container
function scrollToBottom() {
    const chatContainer = document.querySelector('.message-container');
    chatContainer.scrollTop = chatContainer.scrollHeight - chatContainer.clientHeight;
}

// Check if the user is at the bottom of the chat container
function checkScrollPosition() {
    const chatContainer = document.querySelector('.message-container');
    isUserAtBottom = chatContainer.scrollTop + chatContainer.clientHeight >= chatContainer.scrollHeight;
}

// Call this function whenever the user scrolls
document.querySelector('.message-container').addEventListener('scroll', checkScrollPosition);

// Function to display messages
function display_messages(recipientId) {
    $.ajax({
        url: 'ajax/display_message.php', // Your server endpoint to fetch messages
        method: 'GET',
        data: { recipient_id: recipientId },
        success: function(response) {
            // Assuming you append the response to the chat container
            $('.message-container').html(response);

            // Scroll to the bottom only if the user is at the bottom
            if (isUserAtBottom) {
                scrollToBottom();
            }
        }
    });
}

// Call the display_messages function every interval
setInterval(function() {
    var selectedUserId = $('.user.selected').attr('id');
    if (selectedUserId) {
        display_messages(selectedUserId);
    }
}, 2000); // Adjust time interval as needed
document.querySelector('.refresh-btn').addEventListener('click', function() {
});

// Polling function for new messages
// Function to play notification sound
// Assuming you have a variable for the logged-in user ID
let loggedInUserId = <?php echo $_SESSION['user_id']; ?>; // Replace with actual logged-in user ID
let notificationAudio = new Audio('sounds/Notification-Sound.mp3');
let unseenMessageCount = 0; // Tracks unseen messages
let soundEnabled = true; // Enable sound notifications by default
function fetchLatestMessages() {
    
    $.ajax({
        url: "ajax/fetch_latest_message.php",
        method: "POST",
        dataType: "json",
        success: function(response) {
            console.log("Response from server:", response);
            unseenMessageCount = 0; // Reset ang bilang ng unseen messages

            response.forEach(function(userMessage) {
                // I-update ang mga detalye ng mensahe sa UI
                let userElement = $("#" + userMessage.user_id);
                let messagePreview = userElement.find('.message-preview');
                
                messagePreview.text(userMessage.message);
                userElement.find('.time-stamp').text(userMessage.timestamp);
                
                console.log("Message status:", userMessage.status);
                console.log("User ID:", userMessage.user_id);

                // Check kung ang message ay unseen at para sa naka-login na user
                if (userMessage.status === '0' && userMessage.recipient_id == loggedInUserId) {
                    unseenMessageCount++;

                    // Optional: maglagay ng visual cue sa UI
                    userElement.find('.message-notification').addClass('new-message');
                    setTimeout(function() {
                    userElement.find('.message-notification').removeClass('new-message');
                    }, 1000);
                }
            });

            // Mag-play ng tunog kung may unseen messages na para sa user
            if (unseenMessageCount > 0 && soundEnabled || unseenMessageCount > 0) {
            }
            else{
                notificationAudio.pause();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX error:", textStatus, errorThrown);
        }
    });
}


function playNotificationSound() {
    notificationAudio.currentTime = 0; // Reset to start
    notificationAudio.play().catch(error => {
        console.log('Notification sound could not be played:', error);
    });
}




// Function to set up notification permission and initial sound
function setupNotification() {
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log("Notification permission granted.");
        } else {
            console.log("Notification permission denied.");
        }
    });
}
// Add an event listener to enable sound after first interaction
document.addEventListener('click', () => {
  // Your sound play logic goes here
//   playNotificationSound();
}, { once: true }); // This ensures it only runs once


// Set up the notification on window load
window.onload = function() {
    // setupNotification();
    
    // Fetch messages immediately on load
    fetchLatestMessages();

    // Set interval to fetch new messages every 2 seconds
    setInterval(fetchLatestMessages, 2000);

    // Set up event listener for refresh button
    document.querySelector('.refresh-btn').addEventListener('click', fetchLatestMessages);
};



    $(document).on("click", ".user", function() {
    let selectedUserId = $(this).attr("id"); // Assuming each .user has the recipient's ID as the element ID

    $.ajax({
        url: "ajax/mark_as_seen.php",
        method: "POST",
        data: { recipient_id: selectedUserId },
        success: function(response) {
            console.log("Messages marked as seen for recipient:", selectedUserId);
        }
    });

    // Load messages for the selected user, assuming you have a function for that
    display_messages(selectedUserId);
});
$(document).on('click', '#report-btn', function() {
    // Get the selected user's ID
    var selectedUserId = $('.user.selected').attr('id');

    if (!selectedUserId) {
        alert("Please select a user before reporting.");
        return;
    }

    // Redirect to report.php with the selected user's ID as a query parameter
    window.location.href = 'report.php?user_id=' + selectedUserId;
});

});
</script>

</html>