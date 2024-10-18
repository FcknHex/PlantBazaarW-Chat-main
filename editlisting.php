<?php
include 'conn.php';

if (isset($_GET['plantid'])) {
    $plantId = $_GET['plantid'];
    
    // Fetch plant details from the database
    $query = "SELECT * FROM products WHERE plantid = '$plantId'"; // Adjust table name as needed
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        echo json_encode($product); // Return JSON response
    } else {
        echo json_encode(['error' => 'Plant not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>


<!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Listing</h2>
        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="editPlantId" name="plantid"> <!-- Hidden field to hold plant ID -->
            <label for="editPlantname">Plant Name:</label>
            <input type="text" id="editPlantname" name="plantname" required>

            <label for="editPrice">Price:</label>
            <input type="number" id="editPrice" name="price" required min="0" step="0.01">

            <label for="editPlantcategories">Category:</label>
            <input type="text" id="editPlantcategories" name="plantcategories" required>

            <label for="editImg1">Image:</label>
            <input type="file" id="editImg1" name="img1" accept="image/*">

            <button type="submit">Update Plant</button>
        </form>
        <div id="editMessage"></div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    // Function to handle opening the edit modal
    function openEditModal(plantId) {
        // Fetch the plant details via AJAX
        $.ajax({
            url: 'fetch_plant_details.php', // PHP file to fetch details based on plantId
            type: 'GET',
            data: { plantid: plantId },
            dataType: 'json',
            success: function(product) {
                // Populate the form with fetched data
                $('#editPlantId').val(product.plantid);
                $('#editPlantname').val(product.plantname);
                $('#editPrice').val(product.price);
                $('#editPlantcategories').val(product.plantcategories);

                // Show the edit modal
                $('#editProductModal').show();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
            }
        });
    }

    // Submit form for editing the product
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData object from the form

        $.ajax({
            url: 'edit_product_process.php', // PHP script to handle the edit request
            type: 'POST',
            data: formData,
            contentType: false, // Don't set contentType
            processData: false, // Don't process the data
            success: function(response) {
                $('#editMessage').html(response); // Display the response message
                $('#editProductForm')[0].reset(); // Reset the form
                $('#editProductModal').hide(); // Hide the modal after successful submission
                fetchProducts(); // Refresh the product list to show updated data
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
                $('#editMessage').html('<p style="color: red;">An error occurred while updating the product.</p>');
            }
        });
    });

    // Get the modal
    var editModal = $('#editProductModal');
    
    // Get the <span> element that closes the modal
    var editSpan = $('.close');

    // When the user clicks on <span> (x), close the modal
    editSpan.on('click', function() {
        editModal.hide();
    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).on('click', function(event) {
        if ($(event.target).is(editModal)) {
            editModal.hide();
        }
    });

    // Attach click event to Edit buttons dynamically
    $(document).on('click', '.edit-button', function() {
        var plantId = $(this).data('plantid'); // Get the plant ID from data attribute
        openEditModal(plantId); // Open the edit modal
    });
});

var buttonContainer = $('<div>').addClass('button-container');
buttonContainer.append($('<a>')
    .attr('href', '#') // Prevent default behavior
    .addClass('edit-button') // Add a class for the edit button
    // .data('plantid', product.plantid) // Store the plant ID
    .text('Edit Listing'));
        buttonContainer.append($('<a>')
    // .attr('href', 'delete_product.php?plantid=' + product.plantid)
    .text('Delete Listing'));

</script>


<style>
    .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

</style>