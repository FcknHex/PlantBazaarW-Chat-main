<?php
// Include the database connection
include 'conn.php';

// Check if sellerId is set in the URL
if (isset($_GET['sellerId'])) {
    $sellerId = $_GET['sellerId'];

    // Fetch seller's profile data
    $sellerQuery = "SELECT u.firstname, u.lastname, u.email, u.proflepicture, u.address
                    FROM users u 
                    JOIN sellers s ON u.id = s.user_id 
                    WHERE s.seller_id = ?";
    $sellerStmt = $conn->prepare($sellerQuery);
    $sellerStmt->bind_param("i", $sellerId);
    $sellerStmt->execute();
    $sellerResult = $sellerStmt->get_result();
    $sellerData = $sellerResult->fetch_assoc();

    if ($sellerData) {
        // Extract seller's profile data
        $sellerFirstname = $sellerData['firstname'];
        $sellerLastname = $sellerData['lastname'];
        $sellerEmail = $sellerData['email'];
        $sellerProfilePicture = $sellerData['proflepicture'];
        $sellerAddress = $sellerData['address'];
    } else {
        echo "Seller not found.";
        exit;
    }

    // Fetch seller's listings
    $listingsQuery = "SELECT * FROM product WHERE added_by = ?";
    $listingsStmt = $conn->prepare($listingsQuery);
    $listingsStmt->bind_param("i", $sellerId);
    $listingsStmt->execute();
    $listingsResult = $listingsStmt->get_result();

} else {
    echo "No seller ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Profile - <?php echo htmlspecialchars($sellerFirstname . ' ' . $sellerLastname); ?></title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="seller-profile">
    <div class="backBtn-container">
        <a href="#" class="back-btn" id="back">Back</a>
    </div>
        <h2>Seller Profile</h2>
        <div class="seller-info">
            <img src="ProfilePictures/<?php echo htmlspecialchars($sellerProfilePicture); ?>" alt="Seller Profile Picture">
            <h3><?php echo htmlspecialchars($sellerFirstname . ' ' . $sellerLastname); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($sellerEmail); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($sellerAddress); ?></p>
        </div>
        <div>
    <h3>Listings</h3>
    <div class="plant-listings">
    <?php
    if ($listingsResult->num_rows > 0) {
        while ($listing = $listingsResult->fetch_assoc()) {
            echo '<div class="plant-card">';
            echo '<div class="plant-img-container">';
            echo '<img class="plant-img" src="Products/' . htmlspecialchars($sellerEmail) . '/' . htmlspecialchars($listing['img1']) . '" alt="' . htmlspecialchars($listing['plantname']) . '">';
            echo '</div>';
            echo '<div class="plant-details">';
            echo '<h4>' . htmlspecialchars($listing['plantname']) . '</h4>';
            echo '<p><strong>Price:</strong> ₱' . htmlspecialchars($listing['price']) . '</p>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($listing['details']) . '</p>';
            // Modify the View More Details button to redirect to viewmoredetails.php
            echo '<button class="view-details" onclick="viewMoreDetails(' . htmlspecialchars($listing['plantid']) . ', \'' . htmlspecialchars($sellerEmail) . '\')">View more details</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No listings available.</p>';
    }
    ?>
</div>

<!-- Zoom Modal for Plant Image -->
<div id="zoom-plant-modal">
    <span class="close">&times;</span>
    <img id="zoomed-plant-img" src="" alt="Plant Image">
</div>

<!-- Zoom Modal -->
<div id="zoom-modal">
    <span class="close">&times;</span>
    <img id="zoomed-img" src="" alt="Profile Picture">
</div>

<script>
     // Get elements for profile image zoom
     const profileImg = document.querySelector('.seller-info img');
    const modal = document.getElementById('zoom-modal');
    const zoomedImg = document.getElementById('zoomed-img');
    const closeModal = document.querySelector('#zoom-modal .close');
    const back = document.getElementById('back');

    // Open modal when profile image is clicked
    profileImg.addEventListener('click', function() {
        zoomedImg.src = profileImg.src;
        modal.style.display = 'block';
    });

    // Close modal when 'x' is clicked
    closeModal.addEventListener('click', function() {
        window.history.back();
    });

    // Close modal when clicking outside the image
    modal.addEventListener('click', function(e) {
        if (e.target !== zoomedImg) {
            modal.style.display = 'none';
        }
    });

    // Add event listeners for plant images
    document.querySelectorAll('.plant-img').forEach(img => {
        img.addEventListener('click', function() {
            const zoomedPlantImg = document.getElementById('zoomed-plant-img');
            zoomedPlantImg.src = img.src;
            document.getElementById('zoom-plant-modal').style.display = 'block';
        });
    });

    // Close zoom modal for plant image
    document.querySelector('#zoom-plant-modal .close').addEventListener('click', function() {
        document.getElementById('zoom-plant-modal').style.display = 'none';
    });

    // Close zoom modal when clicking outside the image
    document.getElementById('zoom-plant-modal').addEventListener('click', function(e) {
        if (e.target !== document.getElementById('zoomed-plant-img')) {
            document.getElementById('zoom-plant-modal').style.display = 'none';
        }
    });

    // Add event listeners for back button
    back.addEventListener('click', function() {
        window.history.back();
    });


    // Function to handle viewing more details
    function viewMoreDetails(plantId, sellerEmail) {
        // Redirect to viewmoredetails.php with plantId and sellerEmail
        window.location.href = 'viewmoredetails.php?plantId=' + plantId + '&sellerEmail=' + encodeURIComponent(sellerEmail);
    }
</script>

</body>
</html>
