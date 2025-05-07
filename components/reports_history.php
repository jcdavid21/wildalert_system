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

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Filter options
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$species_filter = isset($_GET['species']) ? $_GET['species'] : '';

// Prepare base query
$base_query = "FROM tbl_reports r
              JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
              JOIN tbl_category c ON r.category_id = c.category_id
              WHERE r.acc_id = ?";

$params = array($user_id);
$types = "i";

// Add filters to query if provided
if (!empty($status_filter)) {
    $base_query .= " AND r.status_report_id = ?";
    $params[] = $status_filter;
    $types .= "i";
}

if (!empty($date_from)) {
    $base_query .= " AND r.date_time >= ?";
    $params[] = $date_from . ' 00:00:00';
    $types .= "s";
}

if (!empty($date_to)) {
    $base_query .= " AND r.date_time <= ?";
    $params[] = $date_to . ' 23:59:59';
    $types .= "s";
}

if (!empty($species_filter)) {
    $base_query .= " AND r.species_name LIKE ?";
    $params[] = "%$species_filter%";
    $types .= "s";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total " . $base_query;
$stmt = $conn->prepare($count_query);
bind_params($stmt, $types, $params);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get reports data with pagination
$reports_query = "SELECT r.report_id, r.species_name, c.category_name, r.location, r.date_time, 
                 sr.status_rp_name, r.comments, r.image_path 
                 " . $base_query . " 
                 ORDER BY r.date_time DESC
                 LIMIT ?, ?";
$params[] = $offset;
$params[] = $records_per_page;
$types .= "ii";

$stmt = $conn->prepare($reports_query);
bind_params($stmt, $types, $params);
$stmt->execute();
$reports_result = $stmt->get_result();

// Helper function to bind parameters dynamically
function bind_params($stmt, $types, $params) {
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_names);
}

// Get all status options for filter dropdown
$status_query = "SELECT status_rp_id, status_rp_name FROM tbl_status_report ORDER BY status_rp_name";
$status_result = $conn->query($status_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports History - WildAlert</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Reports History</h2>
            <a href="profile.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Profile</a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Filter Reports</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="species" class="form-label">Species</label>
                        <input type="text" class="form-control" id="species" name="species" value="<?php echo htmlspecialchars($species_filter); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <?php while ($status = $status_result->fetch_assoc()): ?>
                                <option value="<?php echo $status['status_rp_id']; ?>" <?php echo ($status_filter == $status['status_rp_id']) ? 'selected' : ''; ?>>
                                    <?php echo $status['status_rp_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="reports_history.php" class="btn btn-outline-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4>Reports (<?php echo $total_records; ?> total)</h4>
            </div>
            <div class="card-body">
                <?php if ($reports_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Species</th>
                                    <th>Category</th>
                                    <th>Location</th>
                                    <th>Date Reported</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($report = $reports_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $report['report_id']; ?></td>
                                        <td>
                                            <?php if (!empty($report['image_path'])): ?>
                                                <?php $image_filename = basename($report['image_path']); ?>
                                                <img src="../images/reported_images/<?php echo $image_filename; ?>" alt="Species Image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $report['species_name']; ?></td>
                                        <td><?php echo $report['category_name']; ?></td>
                                        <td><?php echo $report['location']; ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($report['date_time'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusColor($report['status_rp_name']); ?>">
                                                <?php echo $report['status_rp_name']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_report.php?id=<?php echo $report['report_id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page-1; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&species=<?php echo $species_filter; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&species=<?php echo $species_filter; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page+1; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&species=<?php echo $species_filter; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No reports found matching your criteria.
                    </div>
                <?php endif; ?>
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