<?php
include '../conn.php';

$plantId = $_GET['plantId'];

$query = "SELECT * FROM product WHERE plantid = '$plantId'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $plant = mysqli_fetch_assoc($result);
  echo json_encode($plant);
} else {
  echo json_encode(array('error' => 'Plant not found'));
}
?>