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

// Check if report ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to reports page if no valid ID
    header("Location: reports_history.php");
    exit();
}

// Database connection
require_once '../backend/config/config.php';

$report_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get report details
$report_query = "SELECT r.*, c.category_name, sr.status_rp_name 
                FROM tbl_reports r
                JOIN tbl_category c ON r.category_id = c.category_id
                JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
                WHERE r.report_id = ? AND r.acc_id = ?";
$stmt = $conn->prepare($report_query);
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$report_result = $stmt->get_result();

// Check if report exists and belongs to the user
if ($report_result->num_rows === 0) {
    // Report not found or doesn't belong to the user
    header("Location: reports_history.php");
    exit();
}

$report = $report_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report - WildAlert</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Report Details</h2>
            <div>
                <a href="reports_history.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
                <?php if ($report['status_report_id'] == 1): // Only allow updates if report is still pending ?>
                <a href="update_report.php?id=<?php echo $report_id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Report
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h4>Report #<?php echo $report_id; ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Basic Information</h5>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Reporter:</label>
                            <div class="col-sm-8">
                                <?php echo htmlspecialchars($report['reporter_name']); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Species:</label>
                            <div class="col-sm-8">
                                <?php echo htmlspecialchars($report['species_name']); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Category:</label>
                            <div class="col-sm-8">
                                <?php echo htmlspecialchars($report['category_name']); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Location:</label>
                            <div class="col-sm-8">
                                <?php echo htmlspecialchars($report['location']); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Date & Time:</label>
                            <div class="col-sm-8">
                                <?php echo date('F d, Y \a\t h:i A', strtotime($report['date_time'])); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Species Status:</label>
                            <div class="col-sm-8">
                                <span class="badge bg-<?php echo ($report['status_name'] == 'Endangared') ? 'danger' : 'primary'; ?>">
                                    <?php echo htmlspecialchars($report['status_name']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 fw-bold">Report Status:</label>
                            <div class="col-sm-8">
                                <span class="badge bg-<?php echo getStatusColor($report['status_rp_name']); ?>">
                                    <?php echo htmlspecialchars($report['status_rp_name']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Image & Additional Information</h5>
                        
                        <?php if (!empty($report['image_path'])): ?>
                            <div class="mb-4">
                                <div class="card">
                                    <img src="<?php echo $report['image_path']; ?>" class="card-img-top" alt="Species Image" style="max-height: 300px; object-fit: contain;">
                                    <div class="card-footer text-center">
                                        <a href="<?php echo $report['image_path']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-expand"></i> View Full Size
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary">
                                <i class="fas fa-image"></i> No image was uploaded with this report.
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="mt-4">Comments:</h5>
                        <div class="card">
                            <div class="card-body">
                                <?php if (!empty($report['comments'])): ?>
                                    <p><?php echo nl2br(htmlspecialchars($report['comments'])); ?></p>
                                <?php else: ?>
                                    <p class="text-muted">No comments provided.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Status Timeline</h5>
                        
                        <ul class="list-group">
                            <?php
                            $statuses = [
                                ['name' => 'Pending', 'icon' => 'clock', 'description' => 'Your report has been received and is awaiting initial review.'],
                                ['name' => 'Surveillance', 'icon' => 'binoculars', 'description' => 'We are monitoring the reported area to verify the sighting.'],
                                ['name' => 'Investigation', 'icon' => 'search', 'description' => 'Our team is actively investigating your report.'],
                                ['name' => 'Verified', 'icon' => 'check-circle', 'description' => 'Your report has been verified and confirmed.'],
                                ['name' => 'No Action Needed', 'icon' => 'ban', 'description' => 'After review, it was determined that no further action is required.'],
                                ['name' => 'Closed Report', 'icon' => 'folder-closed', 'description' => 'This report has been processed and is now closed.']
                            ];
                            
                            $current_status_id = $report['status_report_id'];
                            
                            foreach ($statuses as $index => $status) {
                                $status_id = $index + 1;
                                $active = $status_id <= $current_status_id;
                                $current = $status_id == $current_status_id;
                                
                                echo '<li class="list-group-item ' . ($active ? 'list-group-item-success' : '') . '">';
                                echo '<div class="d-flex align-items-center">';
                                echo '<div class="me-3">';
                                echo '<i class="fas fa-' . $status['icon'] . ' ' . ($active ? 'text-success' : 'text-secondary') . ' fa-lg"></i>';
                                echo '</div>';
                                echo '<div>';
                                echo '<h6 class="mb-0">' . $status['name'] . ($current ? ' <span class="badge bg-primary">Current</span>' : '') . '</h6>';
                                echo '<p class="mb-0 small">' . $status['description'] . '</p>';
                                echo '</div>';
                                echo '</div>';
echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                
                <?php if ($report['status_report_id'] == 4): // Only show conservation actions for verified reports ?>
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Conservation Actions</h5>
                        
                        <?php
                        // Get conservation actions for this report
                        $actions_query = "SELECT * FROM tbl_conservation_actions 
                                        WHERE report_id = ? 
                                        ORDER BY action_date DESC";
                        $stmt = $conn->prepare($actions_query);
                        $stmt->bind_param("i", $report_id);
                        $stmt->execute();
                        $actions_result = $stmt->get_result();
                        ?>
                        
                        <?php if ($actions_result->num_rows > 0): ?>
                            <div class="list-group">
                                <?php while ($action = $actions_result->fetch_assoc()): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($action['action_title']); ?></h5>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($action['action_date'])); ?>
                                            </small>
                                        </div>
                                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($action['action_description'])); ?></p>
                                        <small class="text-muted">Conducted by: <?php echo htmlspecialchars($action['conducted_by']); ?></small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No conservation actions have been recorded for this report yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($report['admin_comments']) && $report['status_report_id'] > 1): ?>
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Feedback from Authorities</h5>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($report['admin_comments'])); ?></p>
                                <?php if (!empty($report['admin_update_date'])): ?>
                                <p class="card-text"><small class="text-muted">Last updated: <?php echo date('F d, Y \a\t h:i A', strtotime($report['admin_update_date'])); ?></small></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Report submitted on: <?php echo date('F d, Y \a\t h:i A', strtotime($report['date_time'])); ?></small>
                    <?php if ($report['status_report_id'] == 1): ?>
                        <form action="delete_report.php" method="post" onsubmit="return confirm('Are you sure you want to delete this report? This action cannot be undone.');">
                            <input type="hidden" name="report_id" value="<?php echo $report_id; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete Report
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php
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
</body>
</html>
                                