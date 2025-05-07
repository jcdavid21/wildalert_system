<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
require_once '../backend/config/config.php';

// Get user information
$user_id = $_SESSION['user_id'];
$user_query = "SELECT a.email, r.role_name 
               FROM tbl_account a 
               JOIN tbl_role r ON a.role_id = r.role_id 
               WHERE a.acc_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - WildAlert</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h5>Email</h5>
                            <p><?php echo $user_data['user_email']; ?></p>
                        </div>
                        <div class="mb-3">
                            <h5>Role</h5>
                            <p><?php echo $user_data['role_name']; ?></p>
                        </div>
                        <a href="update_profile.php" class="btn btn-outline-success">Edit Profile</a>
                        <a href="change_password.php" class="btn btn-outline-secondary">Change Password</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4>My Reports</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Species</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get user's reports
                                    $reports_query = "SELECT r.report_id, r.species_name, r.location, r.date_time, sr.status_rp_name
                                                     FROM tbl_reports r
                                                     JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
                                                     WHERE r.acc_id = ?
                                                     ORDER BY r.date_time DESC
                                                     LIMIT 5";
                                    $stmt = $conn->prepare($reports_query);
                                    $stmt->bind_param("i", $user_id);
                                    $stmt->execute();
                                    $reports_result = $stmt->get_result();

                                    if ($reports_result->num_rows > 0) {
                                        while ($row = $reports_result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['report_id'] . "</td>";
                                            echo "<td>" . $row['species_name'] . "</td>";
                                            echo "<td>" . $row['location'] . "</td>";
                                            echo "<td>" . date('M d, Y H:i', strtotime($row['date_time'])) . "</td>";
                                            echo "<td><span class='badge bg-" . getStatusColor($row['status_rp_name']) . "'>" . $row['status_rp_name'] . "</span></td>";
                                            echo "<td>
                                                    <a href='view_report.php?id=" . $row['report_id'] . "' class='btn btn-sm btn-info'>View</a>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No reports found</td></tr>";
                                    }

                                    function getStatusColor($status) {
                                        switch ($status) {
                                            case 'Pending':
                                                return 'warning';
                                            case 'Surveillance':
                                                return 'info';
                                            case 'Investigation':
                                                return 'primary';
                                            case 'Verified':
                                                return 'success';
                                            case 'No Action Needed':
                                                return 'secondary';
                                            case 'Closed Report':
                                                return 'dark';
                                            default:
                                                return 'secondary';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="reports_history.php" class="btn btn-success">View Complete Reports History</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>