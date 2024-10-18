<?php
include '../conn.php';
session_start();

// Check if a user is logged in
$isLoggedIn = isset($_SESSION['email']) && !empty($_SESSION['email']);

$profilePic = ''; // Placeholder for the profile picture
$isSeller = false; // Flag to check if the user is a seller

if ($isLoggedIn) {
    $email = $_SESSION['email'];

    // Query to get the profile picture from the database
    $query = "SELECT id, proflePicture, firstname, lastname FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $profilePic = $user['proflePicture'];  // Assuming you store the path to the profile picture
        $userId = $user['id'];
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
    }


    // Query to check if the user is a seller
    $sellerQuery = "SELECT seller_id FROM sellers WHERE user_id = '$userId'";
    $sellerResult = mysqli_query($conn, $sellerQuery);

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $isSeller = true; // User is a seller
    }

    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="seller_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <title>Seller Dashboard</title>
</head>
<body>

<!--Start of Navbar -->
<div class="header">
    <nav class="navigation">
        <div class="logo">
            <span class="plant">PLANT</span>
            <p class="bazaar">-BAZAAR</p>
            <i class="fa-solid fa-spa"></i>
        </div>
        <div class="nav1">
            <a href="../index.php">Home</a>
            <a href="#">About</a>
            <a href="#">Services</a>
            <a href="#">Contact</a>
        </div>
        <div class="login-signup">
            <?php if ($isLoggedIn): ?>
                <a href="#" class="profile-link">
                    <img src="../ProfilePictures/<?php echo $profilePic; ?>" alt="Profile" class="profile-pic">
                </a>
            <?php else: ?>
                <a href="#" id="loginLink">Login</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="hamburger">
        <i class="fas fa-bars"></i>
    </div>
</div>

<div class="dropdown-profile">
    <?php
    if ($isLoggedIn) {
        echo '<p>Hello, ' . $firstname . ' ' . $lastname . '</p>';
    }
    ?>
    <?php if ($isSeller): ?>
        <a href="Seller/seller_dashboard.php">Seller Dashboard</a>
    <?php else: ?>
        <a href="#">Be A Seller</a>
    <?php endif; ?>
    <a href="#">Edit Profile</a>
    <a href="#" id="logoutLink">Logout</a>
</div>
<!-- End of Navbar -->


<!-- Start of Main Content  -->
<div class="main-content">
    <h1>Welcome to Your Seller Dashboard</h1>
    <div class="product-list">
        <h2>Your Listed Plants</h2>
        <div class="card-container">
            <!-- Products will be dynamically inserted here -->
        </div>
    </div>
    
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Plant</h2>
            <form id="addProductForm"  enctype="multipart/form-data">
                <label for="plantname">Plant Name:</label>
                <input type="text" id="plantname" name="plantname" required>

                <label for="plantsize">Plant Size:</label>
                <br>
                <select name="plantsize" id="plantsize" required>
                    <option value="" disabled selected>Select Size</option>
                    <option value="Seedling">Seedling</option>
                    <option value="Juvenile">Juvenile</option>
                    <option value="Adult">Adult</option>
                </select>
                <br>
            
                <label for="plantdetails">Description (optional) :</label>
                <textarea name="plantdetails" id="plantdetails" cols="30" rows="10"></textarea>
                <br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required min="0" step="0.01">

                <label for="plantcategories">Category:<span class="required"></label>
                <br>
                <select name="plantcategories" id="plantcategories" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Outdoor">Outdoor Plant</option>
                    <option value="Indoor">Indoor Plants</option>
                    <option value="Flowers">Flowers</option>
                    <option value="Leaves">Leaves</option>
                    <option value="Bushes">Bushes</option>
                    <option value="Trees">Trees</option>
                    <option value="Climbers">Climbers Plant</option>
                    <option value="Grasses">Grasses</option>
                    <option value="Succulent">Succulent Plant</option>
                    <option value="Cacti">Cacti Plant</option>
                    <option value="Aquatic">Aquatic Plant</option>
                </select>
                <br>

                <label for="Location">Location:</label>
                <div class="col-sm-6 mb-3">
                <label class="form-label">Region <span class="text-danger">*</span></label>
                <select name="region" class="form-control form-control-md" id="region"></select>
                <input type="hidden" class="form-control form-control-md" name="region" id="region-text" required>
                </div>
                <div class="col-sm-6 mb-3">
                <label class="form-label">Province *</label>
                <select name="province" class="form-control form-control-md" id="province"></select>
                <input type="hidden" class="form-control form-control-md" name="province" id="province-text" required>
                </div>
                <div class="col-sm-6 mb-3">
                <label class="form-label">City / Municipality *</label>
                <select name="city" class="form-control form-control-md" id="city"></select>
                <input type="hidden" class="form-control form-control-md" name="city" id="city-text" required>
                </div>
                <div class="col-sm-6 mb-3">
                <label class="form-label">Barangay *</label>
                <select name="barangay" class="form-control form-control-md" id="barangay"></select>
                <input type="hidden" class="form-control form-control-md" name="barangay" id="barangay-text" required>
                </div>
                <div class="col-md-6 mb-3">
                <label for="street-text" class="form-label">Street (Optional)</label>
                <input type="text" class="form-control form-control-md" name="street" id="street-text">
                </div>

                <div class="image-upload-container">
                <div class="image-upload-column">
                    <label for="img1">1st Image:</label>
                    <input type="file" id="img1" name="img1" accept="image/*" required>
                    <img id="img1Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
                </div>
                <div class="image-upload-column">
                    <label for="img2">2nd Image:</label>
                    <input type="file" id="img2" name="img2" accept="image/*">
                    <img id="img2Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
                </div>
                <div class="image-upload-column">
                    <label for="img3">3rd Image:</label>
                    <input type="file" id="img3" name="img3" accept="image/*">
                    <img id="img3Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
                </div>
            </div>


                <button type="submit">Add Product</button>
            </form>
            <div id="message"></div>
        </div>
    </div>
    <!-- <a href="add_product.php" id="openModalLink" class="add-product-btn">+ Add New Plant</a> -->
    <button type="button" id="openModalBtn1">Add New Plant</button>
        <!-- End of Main Content -->
         
    <!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Plant</h2>
        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="editPlantId" name="plantid"> <!-- Hidden field to hold plant ID -->
            <label for="editplantname">Plant Name:</label>
            <input type="text" id="editplantname" name="editplantname" value="<?php echo $row['plantname']; ?>" required>

            <label for="editPlantSize">Plant Size:</label>
                <br>
                <select name="editPlantSize" id="editPlantSize" required>
                    <option value="" disabled selected>Select Size</option>
                    <option value="Seedling">Seedling</option>
                    <option value="Juvenile">Juvenile</option>
                    <option value="Adult">Adult</option>
                </select>
                <br>

                <label for="editplantdetails">Description (optional) :</label>
                <textarea name="editplantdetails" id="editplantdetails" cols="30" rows="10"></textarea>
                <br>

            <label for="editPlantCategories">Category:<span class="required"></label>
                <br>
                <select name="editPlantcategories" id="editPlantcategories" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Outdoor">Outdoor Plant</option>
                    <option value="Indoor">Indoor Plants</option>
                    <option value="Flowers">Flowers</option>
                    <option value="Leaves">Leaves</option>
                    <option value="Bushes">Bushes</option>
                    <option value="Trees">Trees</option>
                    <option value="Climbers">Climbers Plant</option>
                    <option value="Grasses">Grasses</option>
                    <option value="Succulent">Succulent Plant</option>
                    <option value="Cacti">Cacti Plant</option>
                    <option value="Aquatic">Aquatic Plant</option>
                </select>
                <br>

            <label for="editLocation">Location:</label>
            <div class="col-sm-6 mb-3">
            <label class="form-label">Region <span style="color:red;">*</span></label>
            <select name="editregion" class="region" id="region"></select>
            <input type="hidden" class="editregion" name="editregion" id="region-text" required>
            </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label">Province *</label>
            <select name="eprovince" class="province" id="province"></select>
            <input type="hidden" class="editprovince" name="editprovince" id="province-text" required>
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label">City / Municipality *</label>
            <select name="ecity"  class="city" id="city"></select>
            <input type="hidden" class="editcity" name="editcity" id="city-text" required>
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label">Barangay *</label>
            <select name="ebarangay" class="barangay" id="barangay"></select>
            <input type="hidden" class="editbarangay" name="editbarangay" id="barangay-text" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="street-text" class="form-label">Street (Optional)</label>
            <input type="hidden" class="editstreet" name="editstreet" id="street-text">
        </div>

            <label for="editPrice">Price:</label>
            <input type="number" id="editPrice" name="editPrice" required min="0" step="0.01" required>
            
            <div class="image-upload-container">

            <div class="image-upload-column">
            <label for="editImg1">1st Image:</label>
            <input type="file" id="editImg1" name="img1" accept="image/*">
            <img id="editImg1Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            <div class="image-upload-column">
            <label for="editImg2">2nd Image:</label>
            <input type="file" id="editImg2" name="img2" accept="image/*">
            <img id="editImg2Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            <div class="image-upload-column">
            <label for="editImg3">3rd Image:</label>
            <input type="file" id="editImg3" name="img3" accept="image/*">
            <img id="editImg3Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            </div>
            <button type="submit">Update Product</button>
        </form>
        <div id="editMessage"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete this listing?</p>
        <button id="confirmDeleteButton">Yes, Delete</button>
        <button id="cancelDeleteButton">Cancel</button>
    </div>
</div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="ph-address-selector.js"></script>
    <script>

  // Add event listeners to image inputs to preview images
document.getElementById('editImg1').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg1Preview').src = event.target.result;
      document.getElementById('editImg1Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('editImg2').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg2Preview').src = event.target.result;
      document.getElementById('editImg2Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('editImg3').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg3Preview').src = event.target.result;
      document.getElementById('editImg3Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('img1').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('img1Preview').src = event.target.result;
      document.getElementById('img1Label').innerHTML = file.name; // Display the file name next to the input field

      // Rename the image
      const newFileName = 'image1_' + Date.now() + '.' + file.name.split('.').pop();
      document.getElementById('img1').files[0].name = newFileName;
    };
    reader.readAsDataURL(file);
});

document.getElementById('img2').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('img2Preview').src = event.target.result;
      document.getElementById('img2Label').innerHTML = file.name; // Display the file name next to the input field

      // Rename the image
      const newFileName = 'image2_' + Date.now() + '.' + file.name.split('.').pop();
      document.getElementById('img2').files[0].name = newFileName;
    };
    reader.readAsDataURL(file);
});

document.getElementById('img3').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('img3Preview').src = event.target.result;
      document.getElementById('img3Label').innerHTML = file.name; // Display the file name next to the input field

      // Rename the image
      const newFileName = 'image3_' + Date.now() + '.' + file.name.split('.').pop();
      document.getElementById('img3').files[0].name = newFileName;
    };
    reader.readAsDataURL(file);
});
   document.addEventListener('DOMContentLoaded', function() {
    // Check if the profile link exists (only when the user is logged in)
    const profileLink = document.querySelector('.profile-link');
    
    if (profileLink) {
        profileLink.addEventListener('click', function() {
            const dropdownMenu = document.querySelector('.dropdown-profile');
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('show');
            } else {
                console.error("Dropdown menu not found");
            }
        });
    } else {
        console.log("Profile link not found - User may not be logged in");
    }

    // Hamburger menu functionality
    const hamburger = document.querySelector('.hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('show');
            } else {
                console.error("Dropdown menu not found");
            }
        });
    } else {
        console.error("Hamburger menu not found");
    }
});

    $(document).ready(function() {
        fetchProducts(); // Fetch products on page load

function fetchProducts() {
    $.ajax({
        url: 'fetch_listed_plants.php',
        type: 'GET',
        dataType: 'json', // Specify the expected data type as JSON
        success: function(data) {
            var productContainer = $('.card-container');
            productContainer.empty();

            data.forEach(function(product) {
            var card = $('<div>').addClass('card');
            card.append($('<h1>').text(product.plantname));
            var imgSrc = '../Products/' + product.seller_email + '/' + product.img1;
                if (product.img1 === '') {
                    if (product.img2 !== '') {
                    imgSrc = '../Products/' + product.seller_email + '/' + product.img2;
                    } else if (product.img3 !== '') {
                    imgSrc = '../Products/' + product.seller_email + '/' + product.img3;
                    } else {
                    imgSrc = '../plant-bazaar.jpg' // Display a default image if all images are empty
                    }
  }
  
            card.append($('<img>').attr('src', imgSrc).attr('alt', product.plantname));
            card.append($('<p>').text('Price: ₱' + product.price));
            card.append($('<p>').text('Category: ' + product.plantcategories));
            
            // Create a container for the buttons
            var buttonContainer = $('<div>').addClass('button-container');

// Create the Edit button
var editButton = $('<button>')
    .addClass('edit-button') // Add a class for CSS styling and event handling
    .data('plantid', product.plantid) // Store the plant ID in a data attribute
    .text('Edit Listing') // Set the button text
    .css({
        backgroundColor: '#4CAF50', // Green background
        color: 'white', // White text
        border: 'none', // No border
        padding: '10px 15px', // Padding for the button
        textAlign: 'center', // Center text
        fontSize: '16px', // Font size
        margin: '4px 2px', // Margin around the button
        cursor: 'pointer', // Pointer cursor on hover
        borderRadius: '5px' // Rounded corners
    });

// Create the Delete button
var deleteButton = $('<button>')
    .addClass('delete-button') // Add a class for CSS styling
    .data('plantid', product.plantid) // Store the plant ID in a data attribute
    .text('Delete Listing') // Set the button text
    .css({
        backgroundColor: '#f44336', // Red background
        color: 'white', // White text
        border: 'none', // No border
        padding: '10px 15px', // Padding for the button
        textAlign: 'center', // Center text
        fontSize: '16px', // Font size
        margin: '4px 2px', // Margin around the button
        cursor: 'pointer', // Pointer cursor on hover
        borderRadius: '5px' // Rounded corners
    });

// Append buttons to the button container
buttonContainer.append(editButton);
buttonContainer.append(deleteButton);

// Append the buttonContainer to your product card
// Example: $('#productCard').append(buttonContainer);


    card.append(buttonContainer); // Append the button container to the card
    productContainer.append(card);
});

            if (data.length === 0) {
                productContainer.append($('<p>').text('You have no plants listed.'));
            }
        },
        error: function(xhr, status, error) {
            console.error("Request failed:", error);
        }
    });
}
    function loadAddProductForm() {
            $.ajax({
                url: 'add_product.php', // Load the add product form
                type: 'GET',
                success: function(data) {
                    $('.main-content').html(data); // Replace content with the add product form
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                }
            });
        }
});
$(document).ready(function() {
    // Get the modal
    var modal = $('#addProductModal');
    
    // Get the button that opens the modal
    var btn = $('#openModalBtn1');
    
    // Get the <span> element that closes the modal
    var span = $('.close');
    
    // When the user clicks the button, open the modal 
    btn.on('click', function(e) {
        e.preventDefault(); // Prevent default button behavior (stop navigating)
        modal.show(); // Show the modal
    });
    
    // When the user clicks on <span> (x), close the modal
    span.on('click', function() {
        modal.hide();
    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    // Submit form via AJAX
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData object from the form

        $.ajax({
            url: 'add_product.php', // URL to the PHP script that will handle the form submission
            type: 'POST', // POST request
            data: formData,
            contentType: false, // Tell jQuery not to set contentType
            processData: false, // Tell jQuery not to process the data
            success: function(response) {
                console.log(response);
                $('#message').html(response); // Display the response message
                $('#addProductForm')[0].reset(); // Reset the form
                modal.hide(); // Hide the modal after successful submission
                setTimeout(function() {
                    location.reload();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
                $('#message').html('<p style="color: red;">An error occurred while adding the product.</p>');
            }
        });
    });
});

$(document).ready(function() {
        // Get the modal
        var modal = $('#addProductModal');
        
        // Get the button that opens the modal
        var btn = $('#openModalBtn1');
        
        // Get the <span> element that closes the modal
        var span = $('.close');
        
        // When the user clicks the button, open the modal 
        btn.on('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            modal.show();
        });
        
        // When the user clicks on <span> (x), close the modal
        span.on('click', function() {
            modal.hide();
        });

        // When the user clicks anywhere outside of the modal, close it
        $(window).on('click', function(event) {
            if ($(event.target).is(modal)) {
                modal.hide();
            }
        });
    });

    $(document).on('click', '.edit-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
    // Fetch the existing data for this plant and populate the modal fields
    console.log('plantId:', plantId);
$.ajax({
  // ...
});
    $.ajax({
  url: 'fetch_listed_products.php', // URL to fetch product details
  type: 'GET',
  dataType: 'json',
  data: { plantid: plantId }, // Pass the plant ID
  success: function(data) {
    console.log(data);
    // Populate the modal fields with fetched data
    $('#editPlantId').prop('value', data.plantid);
    $('#editplantname').prop('value', data.plantname);
    $('#editPrice').prop('value', data.price);
    $('.editregion').prop('value', data.region);
    $('.editprovince').prop('value', data.province);
    $('.editcity').prop('value', data.city);
    $('.editbarangay').prop('value', data.barangay);
    $('.editstreet').prop('value', data.street);
    $('#editplantdetails').prop('value', data.details);
    $('#editPlantcategories').prop('value', data.plantcategories);
    $('#editPlantSize').prop('value', data.plantSize);
    $('#editPlantColor').prop('value', data.plantColor);
    $('#editLocation').prop('value', data.location);
    $('#editImg1Preview').attr('src', '../Products/' + '<?php echo $_SESSION['email'] ?>' + '/' + data.img1).attr('alt', data.plantname).text(data.img1);
    $('#editImg2Preview').attr('src', '../Products/' + '<?php echo $_SESSION['email'] ?>' + '/' + data.img2).attr('alt', data.plantname).text(data.img2);
    $('#editImg3Preview').attr('src', '../Products/' + '<?php echo $_SESSION['email'] ?>' + '/' + data.img3).attr('alt', data.plantname).text(data.img3);
    $('#editImg1Label').text(data.img1);
    $('#editImg2Label').text(data.img2);
    $('#editImg3Label').text(data.img3);

    // Open the edit modal
    $('#editProductModal').show();
  },
  error: function() {
    alert('Error fetching product data.');
  }
});
});

// Handle the delete button click
$(document).on('click', '.delete-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
    $('#deleteConfirmationModal').show(); // Open the delete confirmation modal

    // Set up the confirm button in the modal
    $('#confirmDeleteButton').off('click').on('click', function() {
        // Redirect to delete script
        window.location.href = 'delete_product.php?plantid=' + plantId;
    });
});

// Close modals when clicking on the close button
$('.close').on('click', function() {
    $('#editProductModal').hide();
    $('#deleteConfirmationModal').hide();
});

// Close the modals when clicking outside of them
$(window).on('click', function(event) {
    if ($(event.target).is('#editProductModal')) {
        $('#editProductModal').hide();
    } else if ($(event.target).is('#deleteConfirmationModal')) {
        $('#deleteConfirmationModal').hide();
    }
});

// Handle the form submission for updating the product
$('#editProductForm').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    var formData = new FormData(this); // Create FormData object from the form

    $.ajax({
        url: 'edit_product.php', // URL to the PHP script that will handle the form submission
        type: 'POST',
        data: formData,
        contentType: false, // Tell jQuery not to set contentType
        processData: false, // Tell jQuery not to process the data
        success: function(response) {
            $('#editMessage').html(response); // Display the response message
            $('#editProductForm')[0].reset(); // Reset the form
            $('#editProductModal').hide(); // Hide the modal after successful submission

            window.location.reload(); // Refresh the page
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + status + " " + error);
            $('#editMessage').html('<p style="color: red;">An error occurred while updating the product.</p>');
        }
    });
});

// Logout AJAX
$(document).on('click', '#logoutLink', function(event) {
        event.preventDefault();

        $.ajax({
            url: '../Ajax/logout.php', // Path to your logout.php file
            type: 'POST',
            success: function(response) {
                if (response.trim() === "success") {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Successfully Logged out',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    // Reload page after 3 seconds
                    setTimeout(function() {
                    window.location.href = '../index.php';
                    }, 3000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Logout Failed',
                        text: 'Please try again.',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error: " + status + " - " + error);
                Swal.fire({
                icon: "error",
                title: "Error",
                text: "An unexpected error occurred. Please try again later."
            });
            }
        });
    });


    
    </script>
</body>
</html>