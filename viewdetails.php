<?php
// Include the database connection
include 'conn.php';
session_start();
// Check if plantId and sellerEmail are set in the URL
if (isset($_GET['plantId']) && isset($_GET['sellerEmail'])) {
    $plantId = $_GET['plantId'];
    $sellerEmail = $_GET['sellerEmail'];

    // Fetch plant details from the database
    $sql = "SELECT * FROM product WHERE plantid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $plantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $plant = $result->fetch_assoc();

    if ($plant) {
        // Extract plant data
        $sellerId = $plant['added_by'];
        
        $plantName = $plant['plantname'];
        $plantDescription = $plant['details'];
        $plantPrice = $plant['price'];
        $plantLocationRegion = $plant['region'];
        $plantLocationProvince = $plant['province'];
        $plantLocationCity = $plant['city'];
        $plantLocationBarangay = $plant['barangay'];
        $plantLocationStreet = $plant['street'];
        if($plant['street'] == null){
        $plantLocation = $plantLocationBarangay . ', ' . $plantLocationCity . ', ' . $plantLocationProvince . ', ' . $plantLocationRegion;
        }else{
        $plantLocation = $plantLocationStreet.', '.$plantLocationBarangay.', '.$plantLocationCity.', '.$plantLocationProvince.', '.$plantLocationRegion;
        }
        $plantSize = $plant['plantSize'];
        $plantCategories = $plant['plantcategories'];
        $img1 = $plant['img1'];
        $img2 = $plant['img2'];
        $img3 = $plant['img3'];

        // Fetch seller's profile data
        $sellerQuery = "SELECT u.firstname, u.lastname, u.email, u.proflepicture, u.address FROM users u JOIN sellers s ON u.id = s.user_id WHERE s.seller_id = ?";
        $sellerStmt = $conn->prepare($sellerQuery);
        $sellerStmt->bind_param("i", $sellerId);
        $sellerStmt->execute();
        $sellerResult = $sellerStmt->get_result();
        $sellerData = $sellerResult->fetch_assoc();

        if ($sellerData) {
            // Extract seller's profile data
            // print_r($sellerData); // This will output the entire array for inspection
            // // Check if ratings exists
            // if (array_key_exists('ratings', $sellerData)) {
            //     $sellerRatings = $sellerData['ratings'];
            // } else {
            //     echo "Ratings data not found.";
            // }

            $sellerFirstname = $sellerData['firstname'];
            $sellerLastname = $sellerData['lastname'];
            $sellerEmail = $sellerData['email'];
            $sellerProfilePicture = $sellerData['proflepicture'];
            $sellerAddress = $sellerData['address'];
        } else {
            echo "No data found for seller ID: " . $sellerId;
            exit;
        }

    } else {
        echo "Plant not found.";
        exit;
    }
} else {
    echo "Invalid plant ID or seller email.";
    exit;
}


// Function to get the valid image path
function getImagePath($sellerEmail, $img) {
    $path = "Products/$sellerEmail/$img";
    // Check if the image exists and is not default or empty
    if (!empty($img) && file_exists($path) && $img !== 'default-image.jpg') {
        return $path; // Return the valid image path
    }
    return "default-image.jpg"; // Fallback to default image
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Details - <?php echo $plantName; ?></title>
    <link rel="stylesheet" href="viewdetails.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <!-- X button on the left to redirect to index.php -->
 

<div class="container">
   <!-- New Back button -->
   <a href="javascript:history.back()" class="back-button">Back</a>
    <div class="plantContainer">
    <div class="card">
        <div class="card-image-container">
        <img id="plant-image" src="<?php echo getImagePath($sellerEmail, $img1); ?>" alt="<?php echo $plantName; ?>">

        <div class="card-image-controls">
            <button id="prev-btn"><</button>
            <button id="next-btn">></button>
        </div>
        </div>
        <div class="card-content">
    <h1><?php echo $plantName; ?></h1>
    <div class="plant-details">
        <p><strong>Price:</strong> <?php echo $plantPrice; ?> ₱</p>
        <p><strong>Location:</strong> <?php echo $plantLocation; ?></p>
        <p><strong>Size:</strong> <?php echo $plantSize; ?></p>
        <p><strong>Categories:</strong> <?php echo $plantCategories; ?></p>
        <p><strong>Description:</strong> <?php echo $plantDescription; ?></p>
    </div>
    <!-- View More Button -->
    <div class="view-more-btn">View More</div>
</div>

    </div>
</div>

<div class="profilerContainer">
    <!-- Seller Profile Section -->
        <div class="seller-profile">
        <div class="seller-info">
            <img src="ProfilePictures/<?php echo $sellerProfilePicture; ?>" alt="Seller Profile">
            <h3><?php echo $sellerFirstname . ' ' . $sellerLastname; ?></h3>
            <p class="small">@<?php echo $sellerData['email']; ?></p>
        </div>
        <form action="profile.php" method="get">
        <input type="hidden" name="sellerId" value="<?php echo $sellerId; ?>">
        <button type="submit">View Seller Profile</button>
</form>
</div>
</div>
    <!-- Modal for Image Zoom -->
    <div id="imageModal" class="modal">
        <span class="close-modal" id="closeModal">&times;</span>
        <img class="modal-content" id="zoomed-image">
        <!-- Navigation buttons inside the modal -->
        <button id="zoom-prev-btn" class="modal-nav-btn"><</button>
            <button id="zoom-next-btn" class="modal-nav-btn">></button>
    </div>

    

    <script>
        // Modal functionality
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('imageModal');
    const zoomedImage = document.getElementById('zoomed-image');
    const closeModal = document.getElementById('closeModal');
    const prevBtn = document.getElementById('zoom-prev-btn');
    const nextBtn = document.getElementById('zoom-next-btn');

    let images = [
        '<?php echo getImagePath($sellerEmail, $img1); ?>',
        '<?php echo getImagePath($sellerEmail, $img2); ?>',
        '<?php echo getImagePath($sellerEmail, $img3); ?>'
    ];
    let currentIndex = 0;

    function showImage(index) {
        zoomedImage.src = images[index];
    }

    prevBtn.addEventListener('click', function () {
        currentIndex = (currentIndex === 0) ? images.length - 1 : currentIndex - 1;
        showImage(currentIndex);
    });

    nextBtn.addEventListener('click', function () {
        currentIndex = (currentIndex === images.length - 1) ? 0 : currentIndex + 1;
        showImage(currentIndex);
    });

    // Close modal
    closeModal.addEventListener('click', function () {
        modal.style.display = "none";
    });

    // Open modal on image click
    document.querySelector('.card-image-container img').addEventListener('click', function () {
        modal.style.display = "flex"; // Show modal
        showImage(currentIndex); // Show the current image
    });
});

          // JavaScript for handling the "View More" button
          document.addEventListener('DOMContentLoaded', function () {
            const viewMoreBtn = document.querySelector('.view-more-btn');
            const cardContent = document.querySelector('.card-content');

            if (viewMoreBtn && cardContent) {
                viewMoreBtn.addEventListener('click', function () {
                    cardContent.classList.toggle('expanded');
                    if (cardContent.classList.contains('expanded')) {
                        viewMoreBtn.textContent = 'View Less';
                    } else {
                        viewMoreBtn.textContent = 'View More';
                    }
                });
            }
        });
      $(document).ready(function() {
      

//     // Array of image paths
    let images = [
        '<?php echo getImagePath($sellerEmail, $img1); ?>',
        '<?php echo getImagePath($sellerEmail, $img2); ?>',
        '<?php echo getImagePath($sellerEmail, $img3); ?>'
     ];

    let currentImageIndex = 0;

     const plantImage = $('#plant-image');
    const modal = $('#imageModal');
    const zoomedImage = $('#zoomed-image');
   const closeModal = $('#closeModal');
   const prevBtn = $('#prev-btn');
   const nextBtn = $('#next-btn');
    const zoomPrevBtn = $('#zoom-prev-btn');
    const zoomNextBtn = $('#zoom-next-btn');

   // Zoom in on image click
     plantImage.on('click', function() {
       modal.show();
        zoomedImage.attr('src', images[currentImageIndex]);
    });

     // Close modal
     closeModal.on('click', function() {
        modal.hide();
     });

    // Navigate images in the card
     prevBtn.on('click', function() {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
         plantImage.attr('src', images[currentImageIndex]);
     }); 
    
     nextBtn.on('click', function() {
         currentImageIndex = (currentImageIndex + 1) % images.length;
         plantImage.attr('src', images[currentImageIndex]);
     });

     // Navigate images in the zoomed modal
     zoomPrevBtn.on('click', function() {
         currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
         zoomedImage.attr('src', images[currentImageIndex]);
     });

     zoomNextBtn.on('click', function() {
         currentImageIndex = (currentImageIndex + 1) % images.length;
         zoomedImage.attr('src', images[currentImageIndex]);
     });

     // Close modal when clicking outside
    $(window).on('click', function(event) {
        if (event.target == modal[0]) {
            modal.hide();
        }
    });
});

    </script>
</body>
</html>