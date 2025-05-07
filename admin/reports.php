<?php
include '../backend/config/config.php';
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: login.php");
    exit();
}

// Process status update if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $report_id = $_POST['report_id'];
    $new_status = $_POST['status_report_id'];
    $comments = $_POST['comments'];
    
    // Update the report status
    $update_query = "UPDATE tbl_reports SET status_report_id = ?, comments = ? WHERE report_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("isi", $new_status, $comments, $report_id);
    
    if ($stmt->execute()) {
        // Log the activity in audit trail
        $admin_email = $_SESSION['user_email'];
        $activity = "Updated report #$report_id status to " . getStatusName($conn, $new_status);
        $audit_query = "INSERT INTO tbl_audit_trail (trail_username, trail_activity, trail_date) VALUES (?, ?, NOW())";
        $audit_stmt = $conn->prepare($audit_query);
        $audit_stmt->bind_param("ss", $admin_email, $activity);
        $audit_stmt->execute();
        
        $success_message = "Report status updated successfully!";
    } else {
        $error_message = "Error updating report status: " . $conn->error;
    }
}

// Function to get status name from status_id
function getStatusName($conn, $status_id) {
    $query = "SELECT status_rp_name FROM tbl_status_report WHERE status_rp_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $status_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['status_rp_name'];
    }
    return "Unknown";
}

// Get all reports with relevant details
$query = "SELECT r.*, sr.status_rp_name, c.category_name, a.email as reporter_email
          FROM tbl_reports r
          JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
          JOIN tbl_category c ON r.category_id = c.category_id
          JOIN tbl_account a ON r.acc_id = a.acc_id
          ORDER BY r.date_time DESC";
$result = $conn->query($query);

// Get all possible report statuses for dropdown
$status_query = "SELECT * FROM tbl_status_report";
$status_result = $conn->query($status_query);
$statuses = [];
while ($status = $status_result->fetch_assoc()) {
    $statuses[] = $status;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/sidebar_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Manage Reports - WildAlert Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            margin-left: 250px;
            padding: 20px;
        }
        .reports-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .reports-table th, .reports-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .reports-table th {
            background-color:rgb(44, 162, 48);
            color: white;
        }
        .reports-table tr:hover {
            background-color: #f5f5f5;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-surveillance {
            background-color: #17a2b8;
            color: #fff;
        }
        .status-investigation {
            background-color: #007bff;
            color: #fff;
        }
        .status-verified {
            background-color: #28a745;
            color: #fff;
        }
        .status-noaction {
            background-color: #6c757d;
            color: #fff;
        }
        .status-closed {
            background-color: #dc3545;
            color: #fff;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .report-image {
            max-width: 100px;
            max-height: 100px;
            cursor: pointer;
        }
        .image-modal {
            display: none;
            position: fixed;
            z-index: 2;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .image-modal-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .image-modal-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="container">
        <h1>Wildlife Reports Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <table class="reports-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Species</th>
                    <th>Reporter</th>
                    <th>Location</th>
                    <th>Date Reported</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($report = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $report['report_id']; ?></td>
                            <td>
                                <img src="<?php echo $report['image_path']; ?>" alt="<?php echo $report['species_name']; ?>" class="report-image" onclick="showImage('<?php echo $report['image_path']; ?>')">
                            </td>
                            <td>
                                <strong><?php echo $report['species_name']; ?></strong><br>
                                <small>Category: <?php echo $report['category_name']; ?></small>
                            </td>
                            <td>
                                <?php echo $report['reporter_name']; ?><br>
                                <small><?php echo $report['reporter_email']; ?></small>
                            </td>
                            <td><?php echo $report['location']; ?></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($report['date_time'])); ?></td>
                            <td>
                                <?php 
                                $statusClass = '';
                                switch($report['status_report_id']) {
                                    case 1: $statusClass = 'status-pending'; break;
                                    case 2: $statusClass = 'status-surveillance'; break;
                                    case 3: $statusClass = 'status-investigation'; break;
                                    case 4: $statusClass = 'status-verified'; break;
                                    case 5: $statusClass = 'status-noaction'; break;
                                    case 6: $statusClass = 'status-closed'; break;
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $report['status_rp_name']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-primary" onclick="openModal(<?php echo $report['report_id']; ?>, '<?php echo $report['species_name']; ?>', <?php echo $report['status_report_id']; ?>, '<?php echo htmlspecialchars($report['comments'], ENT_QUOTES); ?>')">Update Status</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No reports found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Status Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Update Report Status</h2>
            <form method="POST" action="">
                <input type="hidden" id="report_id" name="report_id">
                
                <div class="form-group">
                    <label>Species:</label>
                    <p id="species_name"></p>
                </div>
                
                <div class="form-group">
                    <label for="status_report_id">Status:</label>
                    <select class="form-control" id="status_report_id" name="status_report_id" required>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status['status_rp_id']; ?>"><?php echo $status['status_rp_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="comments">Comments:</label>
                    <textarea class="form-control" id="comments" name="comments" rows="4"></textarea>
                </div>
                
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <img class="image-modal-content" id="modalImage">
    </div>
    
    <script>
        // Modal functions
        function openModal(reportId, speciesName, currentStatus, comments) {
            document.getElementById('report_id').value = reportId;
            document.getElementById('species_name').textContent = speciesName;
            document.getElementById('status_report_id').value = currentStatus;
            document.getElementById('comments').value = comments ? comments : '';
            document.getElementById('updateModal').style.display = "block";
        }
        
        function closeModal() {
            document.getElementById('updateModal').style.display = "none";
        }
        
        // Image modal functions
        function showImage(imagePath) {
            var modal = document.getElementById('imageModal');
            var modalImg = document.getElementById('modalImage');
            modal.style.display = "block";
            modalImg.src = imagePath;
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = "none";
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('updateModal');
            var imageModal = document.getElementById('imageModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
            if (event.target == imageModal) {
                imageModal.style.display = "none";
            }
        }
    </script>
</body>
</html>