<?php
// Include the database connection
include 'conn.php';

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
        $plantLocation = $plantLocationStreet . ', ' . $plantLocationBarangay . ', ' . $plantLocationCity . ', ' . $plantLocationProvince . ', ' . $plantLocationRegion;
        $plantSize = $plant['plantSize'];
        $plantCategories = $plant['plantcategories'];
        $img1 = $plant['img1'];
        $img2 = $plant['img2'];
        $img3 = $plant['img3'];

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
    return null; // Return null if the image is invalid or doesn't exist
}

// Collect valid image paths
$images = [];
if ($path = getImagePath($sellerEmail, $img1)) $images[] = $path;
if ($path = getImagePath($sellerEmail, $img2)) $images[] = $path;
if ($path = getImagePath($sellerEmail, $img3)) $images[] = $path;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View More Details - <?php echo $plantName; ?></title>
    <link rel="stylesheet" href="viewmoredetails.css">
</head>
<body>
    <!-- X button on the left to redirect to index.php -->
    <a href="#" class="close-card" id="close">&times;</a>

    <div class="container">
        <div class="plantContainer">
            <div class="card">
                <div class="card-image-container">
                    <img id="plant-image" src="<?php echo $images[0]; ?>" alt="<?php echo $plantName; ?>">

                    <!-- Always show the buttons, even if there’s only one image -->
                    <div class="card-image-controls">
                        <button id="prev-btn"><</button>
                        <button id="next-btn">></button>
                    </div>
                </div>
                <div class="card-content">
                    <h1><?php echo $plantName; ?></h1>
                    <div class="plant-details">
                        <p><strong>Price:</strong> ₱<?php echo $plantPrice; ?></p>
                        <p><strong>Location:</strong> <?php echo $plantLocation; ?></p>
                        <p><strong>Size:</strong> <?php echo $plantSize; ?></p>
                        <p><strong>Categories:</strong> <?php echo $plantCategories; ?></p>
                        <p><strong>Description:</strong> <?php echo $plantDescription; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal for Image Zoom -->
        <div id="imageModal" class="modal">
            <span class="close-modal" id="closeModal">&times;</span>
            <img class="modal-content" id="zoomed-image">
        </div>

        <script>
            // Array of image paths
            let images = <?php echo json_encode($images); ?>;
            let currentImageIndex = 0;

            // Select the image element and buttons
            const plantImage = document.getElementById('plant-image');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const modal = document.getElementById('imageModal');
            const zoomedImage = document.getElementById('zoomed-image');
            const closeModal = document.getElementById('closeModal');
            const close = document.getElementById('close');

            // Check if there’s only one image
            if (images.length === 1) {
                prevBtn.disabled = true; // Disable Previous button
                nextBtn.disabled = true; // Disable Next button
            }

            // Zoom in on the image when clicked
            plantImage.addEventListener('click', function() {
                modal.style.display = "block";
                zoomedImage.src = plantImage.src;
            });

            // Close the modal without redirecting
            closeModal.addEventListener('click', function() {
                modal.style.display = "none";
            });

            close.addEventListener('click', function() {
            if (document.referrer) {
                window.history.back(); // Go back to the previous page
            } else {
                // Fallback if no referrer (could redirect to index or show an alert)
                window.location.href = 'index.php'; // Optional: Redirect to index if no history
            }
        });

            // Event listener for the Previous button
            prevBtn.addEventListener('click', function() {
                if (images.length > 1) { // Allow cycling only if there’s more than one image
                    currentImageIndex--;
                    if (currentImageIndex < 0) {
                        currentImageIndex = images.length - 1;
                    }
                    plantImage.src = images[currentImageIndex];
                }
            });

            // Event listener for the Next button
            nextBtn.addEventListener('click', function() {
                if (images.length > 1) { // Allow cycling only if there’s more than one image
                    currentImageIndex++;
                    if (currentImageIndex >= images.length) {
                        currentImageIndex = 0;
                    }
                    plantImage.src = images[currentImageIndex];
                }
            });

            // Error handling for image loading
            plantImage.addEventListener('error', function() {
                console.error('Error loading image:', plantImage.src);
            });

            // Close the modal when clicking outside of the image
            window.onclick = function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </div>
</body>
</html>
