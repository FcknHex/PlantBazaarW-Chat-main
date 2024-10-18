<?php
include 'conn.php';
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

    // If no profile picture is available, use a default image
    if (empty($profilePic)) {
        $profilePic = 'ProfilePictures/Default-Profile-Picture.png';  // Path to a default profile picture
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="plantcategories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <title>Document</title>
</head>
<body>
    <?php include 'nav.php'; ?>
<div class="container">
    <!-- Categories Container -->
    <button id="openCategoriesModal" class="categories-modal-btn">Filter Categories</button>

<!-- Categories Container for Desktop -->
<div class="categories-container">
        <!-- Plant Type -->
        <div class="plant-type">
            <h3>Plant Type</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div class="plant-type-items">
                <label><input type="checkbox" class="category-checkbox" value="Outdoor"> Outdoor Plant</label>
                <label><input type="checkbox" class="category-checkbox" value="Indoor"> Indoor Plant</label>
                <label><input type="checkbox" class="category-checkbox" value="Flowers"> Flowers</label>
                <label><input type="checkbox" class="category-checkbox" value="Leaves"> Leaves</label>
                <label><input type="checkbox" class="category-checkbox" value="Bushes"> Bushes</label>
                <label><input type="checkbox" class="category-checkbox" value="Trees"> Trees</label>
                <label><input type="checkbox" class="category-checkbox" value="Climbers"> Climbers</label>
                <label><input type="checkbox" class="category-checkbox" value="Grasses"> Grasses</label>
                <label><input type="checkbox" class="category-checkbox" value="Succulent"> Succulent</label>
                <label><input type="checkbox" class="category-checkbox" value="Cacti"> Cacti</label>
                <label><input type="checkbox" class="category-checkbox" value="Aquatic"> Aquatic</label>
            </div>
        </div>

     <!-- Plant Size -->
        <div class="plant-size">
            <h3>Filter by Size</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div class="plant-size-items">
                <label><input type="checkbox" class="size-checkbox" value="Seedlings"> Seedlings</label>
                <label><input type="checkbox" class="size-checkbox" value="Juvenile"> Juvenile</label>
                <label><input type="checkbox" class="size-checkbox" value="Adult"> Adult</label>
            </div>
        </div>

     <!-- Plant Location -->
     <div class="plant-location">
            <h3>Filter by Location</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div id="locationCheckboxes"></div> <!-- Dynamic Location Checkboxes -->
        </div>
    </div>

    <!-- Modal for Categories (visible on mobile view only) -->
    <div id="categoriesModal" class="categories-modal">
        <div class="categories-modal-content">
            <button id="closeCategoriesModal" class="close-modal-btn">&times;</button>

            <!-- Copy of categories for the mobile view modal -->
            <div class="plant-type">
                <h3>Plant Type</h3>
                <button class="clear-all">Clear All</button>
                <div class="plant-type-items">
                    <label><input type="checkbox" class="category-checkbox" value="Outdoor"> Outdoor Plant</label>
                    <label><input type="checkbox" class="category-checkbox" value="Indoor"> Indoor Plant</label>
                    <label><input type="checkbox" class="category-checkbox" value="Flowers"> Flowers</label>
                    <label><input type="checkbox" class="category-checkbox" value="Leaves"> Leaves</label>
                    <label><input type="checkbox" class="category-checkbox" value="Bushes"> Bushes</label>
                    <label><input type="checkbox" class="category-checkbox" value="Trees"> Trees</label>
                    <label><input type="checkbox" class="category-checkbox" value="Climbers"> Climbers</label>
                    <label><input type="checkbox" class="category-checkbox" value="Grasses"> Grasses</label>
                    <label><input type="checkbox" class="category-checkbox" value="Succulent"> Succulent</label>
                    <label><input type="checkbox" class="category-checkbox" value="Cacti"> Cacti</label>
                    <label><input type="checkbox" class="category-checkbox" value="Aquatic"> Aquatic</label>
                </div>
            </div>
            <!-- Plant Size -->
        <div class="plant-size">
            <h3>Filter by Size</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div class="plant-size-items">
                <label><input type="checkbox" class="size-checkbox" value="Seedlings"> Seedlings</label>
                <label><input type="checkbox" class="size-checkbox" value="Juvenile"> Juvenile</label>
                <label><input type="checkbox" class="size-checkbox" value="Adult"> Adult</label>
            </div>
        </div>

        <!-- Plant Location -->
     <div class="plant-location">
            <h3>Filter by Location</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div id="locationCheckboxesMobile"></div> <!-- Dynamic Location Checkboxes -->
        </div>

</div>
</div>
        

<!-- Newly Listed Plants -->
<div class="listed-plants">
    <div class="sort-container">
        <h1>Listed Plants</h1>
        <select id="sortPrice" class="sort-price-dropdown">
            <option value="">Sort by Price</option>
            <option value="low">Lowest to Highest</option>
            <option value="high">Highest to Lowest</option>
        </select>
    </div>
   <div class="search-bar-container">
       <input type="text" id="searchBar" placeholder="Search...">
   </div>
    <div class="newly-contents" id="newly-contents">
        <!-- Products will be loaded dynamically -->
    </div>
</div>

<script src="script.js"></script>
<script>
$(document).ready(function () {
    // Fetch Newly Listed Plants via AJAX
    $.ajax({
        url: 'Ajax/fetch_categories.php',
        type: 'GET',
        dataType: 'json', // Ensure response is treated as JSON
        success: function (response) {
            try {
                let plants = response;

                if (!plants.length) {
                    $('#locationCheckboxes').html("<p>No plants available at the moment.</p>");
                    return;
                }

                // Group locations uniquely from plants
                let locations = [...new Set(plants.map(p => p.city))]; // Unique location names

                // Create location checkboxes dynamically
                let locationCheckboxesHtml = locations.map(location => 
                    `<label>
                        <input type="checkbox" class="location-checkbox" value="${location}">
                        ${location}
                    </label><br>`
                ).join('');

                $('#locationCheckboxes').html(locationCheckboxesHtml);
                $('#locationCheckboxesMobile').html(locationCheckboxesHtml);

                // Display all plants in the content area
                function displayPlants(plantsToDisplay) {
                    let contentHtml = plantsToDisplay.map(product => 
                        `<div class="plant-item" data-location="${product.city}" data-category="${product.plantcategories}" data-size="${product.plantSize}" data-price="${product.price}">
                            <div class="plant-image">
                                <img src="Products/${product.seller_email}/${product.img1}" alt="${product.plantname}">
                            </div>
                            <p>${product.plantname}</p>
                            <p>Price: ₱${product.price}</p>
                            <p>Category: ${product.plantcategories}</p>
                            <p>Size: ${product.plantSize}</p>
                            <div class="plant-item-buttons">
                                <button class="view-details" data-id="${product.plantid}" data-email="${product.seller_email}">View more details</button>
                                <button class="chat-seller" data-email="${product.seller_email}">Chat Seller</button>
                            </div>
                        </div>`
                    ).join('');
                    $('#newly-contents').html(contentHtml);
                }

                displayPlants(plants); // Initial display

                // Handle sorting
                $('#sortPrice').on('change', function () {
                    let sortOrder = $(this).val();
                    if (sortOrder === 'low') {
                        plants.sort((a, b) => a.price - b.price); // Lowest to highest
                    } else if (sortOrder === 'high') {
                        plants.sort((a, b) => b.price - a.price); // Highest to lowest
                    }
                    filterPlants(); // Reapply filters to maintain filtering state
                });

                // Handle checkbox change events for filtering
                $('.location-checkbox, .category-checkbox, .size-checkbox').on('change', filterPlants);

                // Clear All Button Logic
                $('.clear-all').on('click', function () {
                    $(this).closest('.plant-type').find('input[type="checkbox"]').prop('checked', false);
                    filterPlants();
                });

                function filterPlants() {
                    let selectedLocations = $('.location-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();
                    let selectedCategories = $('.category-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();
                    let selectedSizes = $('.size-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();

                    let filteredPlants = plants.filter(function (plant) {
                        let plantLocation = plant.city;
                        let plantCategory = plant.plantcategories;
                        let plantSize = plant.plantSize;

                        let matchesLocation = !selectedLocations.length || selectedLocations.includes(plantLocation);
                        let matchesCategory = !selectedCategories.length || selectedCategories.includes(plantCategory);
                        let matchesSize = !selectedSizes.length || selectedSizes.includes(plantSize);

                        return matchesLocation && matchesCategory && matchesSize;
                    });

                    // Sort filtered plants before displaying
                    if ($('#sortPrice').val() === 'low') {
                        filteredPlants.sort((a, b) => a.price - b.price); // Lowest to highest
                    } else if ($('#sortPrice').val() === 'high') {
                        filteredPlants.sort((a, b) => b.price - a.price); // Highest to lowest
                    }

                    displayPlants(filteredPlants);
                }

                // Save filter state before navigating to the details page
                $(document).on('click', '.view-details', function () {
                    saveFilterState();
                    let plantId = $(this).data('id');
                    let sellerEmail = $(this).data('email');
                    window.location.href = `viewdetails.php?plantId=${plantId}&sellerEmail=${sellerEmail}`;
                });

                function saveFilterState() {
                    let selectedLocations = $('.location-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get();
                    let selectedCategories = $('.category-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get();
                    let selectedSizes = $('.size-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get();
                    let sortOrder = $('#sortPrice').val();

                    sessionStorage.setItem('selectedLocations', JSON.stringify(selectedLocations));
                    sessionStorage.setItem('selectedCategories', JSON.stringify(selectedCategories));
                    sessionStorage.setItem('selectedSizes', JSON.stringify(selectedSizes));
                    sessionStorage.setItem('sortOrder', sortOrder);
                }

                function reapplyFilters() {
                    let selectedLocations = JSON.parse(sessionStorage.getItem('selectedLocations')) || [];
                    let selectedCategories = JSON.parse(sessionStorage.getItem('selectedCategories')) || [];
                    let selectedSizes = JSON.parse(sessionStorage.getItem('selectedSizes')) || [];
                    let sortOrder = sessionStorage.getItem('sortOrder') || '';

                    $('.location-checkbox').each(function() {
                        $(this).prop('checked', selectedLocations.includes($(this).val()));
                    });
                    $('.category-checkbox').each(function() {
                        $(this).prop('checked', selectedCategories.includes($(this).val()));
                    });
                    $('.size-checkbox').each(function() {
                        $(this).prop('checked', selectedSizes.includes($(this).val()));
                    });

                    $('#sortPrice').val(sortOrder);
                    filterPlants();
                }

                // Call reapplyFilters() when the page loads
                reapplyFilters();
            } catch (e) {
                console.error("Error parsing JSON:", e);
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load plants. Please try again.'
            });
        }
    });

    // Open modal for categories on mobile
    $('#openCategoriesModal').on('click', function() {
        $('#categoriesModal').addClass('show');
    });

    $('#closeCategoriesModal').on('click', function() {
        $('#categoriesModal').removeClass('show');
    });
});

// Clear session storage when user logs out or navigates away
window.addEventListener('unload', function () {
    sessionStorage.removeItem('selectedLocations');
    sessionStorage.removeItem('selectedCategories');
    sessionStorage.removeItem('selectedSizes');
    sessionStorage.removeItem('sortOrder');
});
</script>

    
</body>
</html>