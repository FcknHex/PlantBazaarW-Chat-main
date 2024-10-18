<?php
include '../conn.php'; // Ensure you have a proper connection to your database

// Query to select sellers with a rating above 4.5
$sql = "
    SELECT s.user_id, s.ratings, u.firstname, u.lastname, u.proflePicture 
    FROM sellers s
    INNER JOIN users u ON s.user_id = u.id
    WHERE s.ratings > 4.5 
    ORDER BY s.ratings DESC 
    LIMIT 4
";

// Execute the query
$result = mysqli_query($conn, $sql);
$num_rows = mysqli_num_rows($result); // Get the number of rows

// Send number of results to the frontend
echo '<input type="hidden" id="num_sellers" value="' . $num_rows . '">';

if ($num_rows > 0) {
    echo '<div class="splide" id="seller-slider">
            <div class="splide__track">
                <ul class="splide__list">';
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Prepare your HTML structure to display the data
        echo '
        <li class="splide__slide">
            <div class="seller-card">
                <img src="ProfilePictures/' . $row['proflePicture'] . '" alt="Profile Picture" class="profile-picture" />
                <div class="seller-info">
                    <h3>' . $row['firstname'] . ' ' . $row['lastname'] . '</h3>
                    <p class="rating">'.'(' . $row['ratings'] . '/5) ' . generateStarRating($row['ratings']) . '</p>
                </div>
            </div>
        </li>';
    }
    
    echo '</ul></div></div>';
} else {
    echo '<p>No top-rated sellers found.</p>';
}

// Close the connection
mysqli_close($conn);

/**
 * Function to generate star rating SVGs based on the rating.
 */
function generateStarRating($rating) {
    $fullStars = floor($rating); // Number of full stars
    $halfStar = ($rating - $fullStars) >= 0.5 ? true : false; // Check if half star
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); // Remaining empty stars

    $stars = '';

    // Generate full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<svg class="star full-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="gold"><path d="M12 .587l3.668 7.431 8.2 1.191-5.92 5.767 1.396 8.135L12 18.896l-7.344 3.875L6.052 15.2.131 9.433l8.2-1.191L12 .587z"/></svg>';
    }

    // Generate half star
    if ($halfStar) {
        $stars .= '<svg class="star half-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="gold"><path d="M12 .587l3.668 7.431 8.2 1.191-5.92 5.767 1.396 8.135L12 18.896V.587z"/><path fill="#ccc" d="M12 18.896L4.656 22.771l1.396-8.135L.131 9.433l8.2-1.191L12 .587V18.896z"/></svg>';
    }

    // Generate empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<svg class="star empty-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ccc"><path d="M12 .587l3.668 7.431 8.2 1.191-5.92 5.767 1.396 8.135L12 18.896l-7.344 3.875L6.052 15.2.131 9.433l8.2-1.191L12 .587z"/></svg>';
    }

    return $stars;
}
?>


<style>
.splide {
    padding: 20px;
}
.splide__track{
    padding:20px;
}
.seller-card {
    background-color: #fff;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 250px;
}

.seller-card img.profile-picture {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}
.seller-info{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
} 
.seller-info p {
    font-size: 18px;
    color: #333;
    margin-bottom: 5px;
}

.seller-info small {
    font-size: 16px;
    color: #777;
}
.rating{
    display: flex;
    align-items: center;
    justify-content: center;
}
.star {
    width: 20px;
    height: 20px;
    display: inline-block;
    margin-right: 3px;
}

.star.full-star {
    fill: gold;
}

.star.half-star {
    fill: gold;
    position: relative;
}

.star.half-star path:nth-child(2) {
    fill: #ccc; /* Fill second half of star */
}

.star.empty-star {
    fill: #ccc;
}
</style>