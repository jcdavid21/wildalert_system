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

// Check if report ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to reports page if no valid ID
    header("Location: reports_history.php");
    exit();
}

$report_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get report details
$report_query = "SELECT r.*, c.category_name, s.status_name 
                FROM tbl_reports r
                JOIN tbl_category c ON r.category_id = c.category_id
                JOIN tbl_status s ON r.status_id = s.status_id
                WHERE r.report_id = ? AND r.acc_id = ? AND r.status_report_id = 1";
$stmt = $conn->prepare($report_query);
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$report_result = $stmt->get_result();

// Check if report exists, belongs to the user, and is still in pending status
if ($report_result->num_rows === 0) {
    // Report not found, doesn't belong to the user, or is not in pending status
    $_SESSION['error'] = "You cannot edit this report. It may have been processed already or doesn't exist.";
    header("Location: reports_history.php");
    exit();
}

$report = $report_result->fetch_assoc();

// Get all categories
$category_query = "SELECT category_id, category_name FROM tbl_category ORDER BY category_name";
$category_result = $conn->query($category_query);

// Get all species statuses
$status_query = "SELECT status_id, status_name FROM tbl_status ORDER BY status_name";
$status_result = $conn->query($status_query);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $species_name = trim($_POST['species_name']);
    $category_id = intval($_POST['category_id']);
    $location = trim($_POST['location']);
    $date_time = $_POST['date_time'];
    $status_id = intval($_POST['status_id']);
    $comments = trim($_POST['comments']);
    $update_date = date('Y-m-d H:i:s');
    $reporter_name = $report['reporter_name']; // Keep existing reporter name
    
    // Validate required fields
    $errors = [];
    if (empty($species_name)) {
        $errors[] = "Species name is required.";
    }
    if ($category_id <= 0) {
        $errors[] = "Please select a valid category.";
    }
    if (empty($location)) {
        $errors[] = "Location is required.";
    }
    if (empty($date_time)) {
        $errors[] = "Date and time is required.";
    }
    if ($status_id <= 0) {
        $errors[] = "Please select a valid species status.";
    }
    
    // Handle image upload if a new one is provided
    $image_path = $report['image_path']; // Keep the existing path by default
    
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        // Define directory paths correctly
        $server_upload_path = "../images/reported_images/"; // Physical path on server for file upload
        $db_path = "/images/reported_images/"; // Path to be stored in database
        
        // Create the directory if it doesn't exist
        if (!file_exists($server_upload_path)) {
            mkdir($server_upload_path, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "report_" . $report_id . "_" . time() . "." . $file_extension;
        $server_file_path = $server_upload_path . $new_filename;
        $db_file_path = $db_path . $new_filename;
        
        // Check file size (max 5MB)
        if ($_FILES["image"]["size"] > 5000000) {
            $errors[] = "Sorry, your file is too large. Maximum size is 5MB.";
        }
        
        // Allow only certain file formats
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        
        // If no errors, try to upload the file
        if (empty($errors)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $server_file_path)) {
                // Delete the old image if it exists and is different
                if (!empty($image_path)) {
                    $old_server_path = ".." . $image_path; // Convert DB path to server path
                    if (file_exists($old_server_path)) {
                        unlink($old_server_path);
                    }
                }
                $image_path = $db_file_path; // Store the database path format
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // If no errors, update the report
    if (empty($errors)) {
        // Check the structure of the tbl_reports table for the correct fields
        $update_query = "UPDATE tbl_reports 
                        SET species_name = ?, 
                            category_id = ?, 
                            location = ?, 
                            date_time = ?, 
                            status_id = ?, 
                            comments = ?, 
                            image_path = ? 
                        WHERE report_id = ? AND acc_id = ?";
                        
        // Debug the query and parameters (for development only)
        // echo $update_query . "<br>";
        // echo "Species: $species_name, Category: $category_id, Location: $location<br>";
        // echo "Date: $date_time, Status: $status_id, Comments: $comments<br>";
        // echo "Image: $image_path, Report ID: $report_id, User ID: $user_id<br>";
        // exit();
                        
        $stmt = $conn->prepare($update_query);
        
        if ($stmt === false) {
            // Query preparation failed
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sisssssii", 
                          $species_name, 
                          $category_id, 
                          $location, 
                          $date_time, 
                          $status_id, 
                          $comments, 
                          $image_path, 
                          $report_id, 
                          $user_id);
            
            if ($stmt->execute()) {
                // Success
                $_SESSION['success'] = "Report updated successfully.";
                header("Location: view_report.php?id=" . $report_id);
                exit();
            } else {
                // Execution error
                $errors[] = "Database error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Report - WildAlert</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Update Report</h2>
            <a href="view_report.php?id=<?php echo $report_id; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Report
            </a>
        </div>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h4>Update Report #<?php echo $report_id; ?></h4>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="species_name" class="form-label">Species Name *</label>
                                <input type="text" class="form-control" id="species_name" name="species_name" value="<?php echo htmlspecialchars($report['species_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- Select Category --</option>
                                    <?php 
                                    // Reset the result pointer
                                    $category_result->data_seek(0);
                                    while ($category = $category_result->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $category['category_id']; ?>" <?php echo ($category['category_id'] == $report['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">Location *</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($report['location']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_time" class="form-label">Date & Time of Sighting *</label>
                                <input type="datetime-local" class="form-control" id="date_time" name="date_time" value="<?php echo date('Y-m-d\TH:i', strtotime($report['date_time'])); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status_id" class="form-label">Species Status *</label>
                                <select class="form-select" id="status_id" name="status_id" required>
                                    <option value="">-- Select Status --</option>
                                    <?php 
                                    // Reset the result pointer
                                    $status_result->data_seek(0);
                                    while ($status = $status_result->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $status['status_id']; ?>" <?php echo ($status['status_id'] == $report['status_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status['status_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image (Optional)</label>
                                <?php if (!empty($report['image_path'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo $report['image_path']; ?>" class="img-thumbnail" alt="Current Image" style="max-height: 200px;">
                                        <p class="text-muted">Current image. Upload a new one to replace it.</p>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image">
                                <div class="form-text">Accepted formats: JPG, JPEG, PNG, GIF. Max size: 5MB.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comments" class="form-label">Additional Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="5"><?php echo htmlspecialchars($report['comments']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="view_report.php?id=<?php echo $report_id; ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>