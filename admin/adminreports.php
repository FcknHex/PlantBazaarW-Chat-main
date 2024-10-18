<?php
session_start();
include '../conn.php'; // Include your connection file

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch the total number of users
$query = "SELECT COUNT(id) AS total_users FROM users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalUsers = $row['total_users']; // Get the total number of users

// Fetch the total number of sellers
$querySellers = "SELECT COUNT(seller_id) AS total_sellers FROM sellers";
$resultSellers = mysqli_query($conn, $querySellers);
$rowSellers = mysqli_fetch_assoc($resultSellers);
$totalSellers = $rowSellers['total_sellers']; // Get the total number of sellers

// Fetch the total number of pending seller applicants
$queryPendingApplicants = "SELECT COUNT(applicantID) AS total_pending_applicants FROM seller_applicant WHERE status = 'pending'";
$resultPendingApplicants = mysqli_query($conn, $queryPendingApplicants);
$rowPendingApplicants = mysqli_fetch_assoc($resultPendingApplicants);
$totalPendingApplicants = $rowPendingApplicants['total_pending_applicants']; // Get the total number of pending applicants

// Fetch the total number of reported users
$queryReportedUsers = "SELECT COUNT(DISTINCT reported_user_id) AS total_reported_users FROM reports WHERE status = 'pending'";
$resultReportedUsers = mysqli_query($conn, $queryReportedUsers);
$rowReportedUsers = mysqli_fetch_assoc($resultReportedUsers);
$totalReportedUsers = $rowReportedUsers['total_reported_users']; // Get the total number of reported users

// Handle approve/reject actions for reports
if (isset($_POST['action_report'])) {
    $reportId = $_POST['reportID'];

    if ($_POST['action_report'] === 'approve') {
        // Approve: Archive the user before deleting
        $archiveUserQuery = "INSERT INTO reported_user_archive (user_id, firstname, lastname, email)
                              SELECT u.id, u.firstname, u.lastname, u.email 
                              FROM users u
                              JOIN reports r ON u.id = r.user_id
                              WHERE r.report_id = ?";
        $stmt = mysqli_prepare($conn, $archiveUserQuery);
        mysqli_stmt_bind_param($stmt, 'i', $reportId);
        mysqli_stmt_execute($stmt);

        // Delete the user account
        $deleteUserQuery = "DELETE FROM users WHERE id = (SELECT user_id FROM reports WHERE report_id = ?)";
        $stmt = mysqli_prepare($conn, $deleteUserQuery);
        mysqli_stmt_bind_param($stmt, 'i', $reportId);
        mysqli_stmt_execute($stmt);

        // Send email notification to the user about account deletion
        // First, get the user's email for the notification
        $emailQuery = "SELECT u.email FROM users u JOIN reports r ON u.id = r.user_id WHERE r.report_id = ?";
        $stmt = mysqli_prepare($conn, $emailQuery);
        mysqli_stmt_bind_param($stmt, 'i', $reportId);
        mysqli_stmt_execute($stmt);
        $resultEmail = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($resultEmail);
        $userEmail = $user['email'];

        // Prepare the email content
        $subject = "Account Deletion Notification";
        $message = "Dear user, your account has been deleted due to violation of our terms of service. If you have any questions, please contact support.";
        $headers = "From: support@plantbazaar.com"; // Change to your support email

        // Send email
        mail($userEmail, $subject, $message, $headers);

        // Update report status to approved
        $updateReport = "UPDATE reports SET status = 'approved' WHERE report_id = ?";
        $stmt = mysqli_prepare($conn, $updateReport);
        mysqli_stmt_bind_param($stmt, 'i', $reportId);
        mysqli_stmt_execute($stmt);

        echo "<script>
                Swal.fire('Success!', 'Report Approved and User Deleted!', 'success')
                    .then(() => location.reload());
              </script>";
    } elseif ($_POST['action_report'] === 'reject') {
        // Reject the report: Just delete the report entry from the reports table
        $deleteReport = "DELETE FROM reports WHERE reported_user_id = ?";
        $stmt = mysqli_prepare($conn, $deleteReport);
        mysqli_stmt_bind_param($stmt, 'i', $reportId);
        mysqli_stmt_execute($stmt);

        echo "<script>
                Swal.fire('Rejected', 'Report has been removed.', 'info')
                    .then(() => location.reload());
              </script>";
    }
}

// Fetch reports
$queryReports = "SELECT r.reported_user_id, r.user_id, u.firstname, u.lastname, u.email, r.report_reason, r.proof_img_1, r.proof_img_2, r.proof_img_3, r.proof_img_4, r.proof_img_5, r.proof_img_6
                 FROM reports r
                 JOIN users u ON r.user_id = u.id
                 WHERE r.status = 'pending'";
$resultReports = mysqli_query($conn, $queryReports);

// Fetch the total number of users, sellers, applicants, and reports (already fetched in previous steps)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS (same as the previous one you provided) */
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('https://www.transparenttextures.com/patterns/leaf.png');
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        nav {
            background-color: #4C8C4A;
            color: white;
            padding: 10px;
            position: fixed;
            height: 100%;
            width: 200px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }
        nav h2 {
            color: white;
            margin: 0;
            padding: 10px 0;
            font-family: 'Georgia', serif;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav li {
            margin: 10px 0;
        }
        nav li a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 5px;
            transition: background 0.3s;
        }
        nav li a:hover {
            background-color: limegreen;
        }
        .container {
            margin-left: 220px;
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-family: 'Georgia', serif;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .summary-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
            margin: 0 10px;
            margin-bottom: 20px;
        }
        .summary-box h2 {
            color: #4C8C4A;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4C8C4A;
            color: white;
        }

        /* Modal CSS */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.9); /* Black w/ opacity */
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <nav>
        <h2>PlantBazaar</h2>
        <ul>
            <li><a class="active" href="admindashboard.php">Users</a></li>
            <li><a href="sellerapplicants.php">Seller Applicants</a></li>
            <li><a href="reportedusers.php">Reported Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-box">
                <h2>Total Users</h2>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="summary-box">
                <h2>Total Sellers</h2>
                <p><?php echo $totalSellers; ?></p>
            </div>
            <div class="summary-box">
                <h2>Pending Applicants</h2>
                <p><?php echo $totalPendingApplicants; ?></p>
            </div>
        </div>

        <!-- Reported Users Table -->
        <h2>Reported Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Reported User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Reason for Report</th>
                    <th>Proof Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultReports)) { ?>
                <tr>
                    <td><?php echo $row['reported_user_id']; ?></td>
                    <td><?php echo $row['firstname']; ?></td>
                    <td><?php echo $row['lastname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['report_reason']; ?></td>
                    <td>
                        <?php 
for ($i = 1; $i <= 6; $i++) {
    if (!empty($row['proof_img_'.$i])) { ?>
        <button onclick="openModal('<?php echo $row['proof_img_'.$i]; ?>')">Click to View Proof <?php echo $i; ?></button>
    <?php } else { ?>
        No Proof Available
    <?php }
} 
?>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="reportID" value="<?php echo $row['reported_user_id']; ?>">
                            <button type="submit" name="action_report" value="approve">Approve</button>
                            <button type="submit" name="action_report" value="reject">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Modal for viewing proof -->
        <div id="proofModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="proofImage" alt="Proof Image">
        </div>
    </div>

    <script>
        // Function to open the modal and show the image
        function openModal(imageUrl) {
            const modal = document.getElementById("proofModal");
            const img = document.getElementById("proofImage");
            img.src = imageUrl; // Set the image source
            modal.style.display = "block"; // Show the modal
        }

        // Function to close the modal
        function closeModal() {
            const modal = document.getElementById("proofModal");
            modal.style.display = "none"; // Hide the modal
        }
    </script>
</body>
</html>
