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

// Pagination settings
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Search functionality
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
if (!empty($searchQuery)) {
    $searchParam = "%" . $searchQuery . "%";
    $searchCondition = "WHERE trail_username LIKE ? OR trail_activity LIKE ?";
}

// Date filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if (!empty($startDate) && !empty($endDate)) {
    if (empty($searchCondition)) {
        $searchCondition = "WHERE trail_date BETWEEN ? AND ?";
    } else {
        $searchCondition .= " AND trail_date BETWEEN ? AND ?";
    }
}

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM tbl_audit_trail $searchCondition";
$stmt = $conn->prepare($countQuery);

if (!empty($searchQuery)) {
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

if (!empty($startDate) && !empty($endDate)) {
    if (!empty($searchQuery)) {
        $stmt->bind_param("ssss", $searchParam, $searchParam, $startDate, $endDate);
    } else {
        $stmt->bind_param("ss", $startDate, $endDate);
    }
}

$stmt->execute();
$totalResult = $stmt->get_result();
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch audit trail records
$query = "SELECT * FROM tbl_audit_trail $searchCondition ORDER BY trail_date DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);

// Bind parameters appropriately based on conditions
if (!empty($searchQuery) && !empty($startDate) && !empty($endDate)) {
    $stmt->bind_param("ssssii", $searchParam, $searchParam, $startDate, $endDate, $offset, $recordsPerPage);
} elseif (!empty($searchQuery)) {
    $stmt->bind_param("ssii", $searchParam, $searchParam, $offset, $recordsPerPage);
} elseif (!empty($startDate) && !empty($endDate)) {
    $stmt->bind_param("ssii", $startDate, $endDate, $offset, $recordsPerPage);
} else {
    $stmt->bind_param("ii", $offset, $recordsPerPage);
}

$stmt->execute();
$result = $stmt->get_result();

// Add audit trail entry for viewing the page
$currentUser = $_SESSION['user_email'];
$activity = "Viewed audit trail page";
$currentDateTime = date('Y-m-d H:i:s');

$auditQuery = "INSERT INTO tbl_audit_trail (trail_username, trail_activity, trail_date) VALUES (?, ?, ?)";
$auditStmt = $conn->prepare($auditQuery);
$auditStmt->bind_param("sss", $currentUser, $activity, $currentDateTime);
$auditStmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/sidebar_style.css">
    <title>Audit Trail - WildAlert Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        /* Content styles */
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        @media screen and (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }
            
            .content-wrapper.with-sidebar {
                margin-left: 250px;
            }
        }
        
        .page-title {
            color: #2C3E50;
            margin-bottom: 20px;
        }
        
        .filters-container {
            background-color: #f9f9f9;
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filters-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-primary {
            background-color:rgb(55, 154, 58);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .audit-table th, .audit-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .audit-table th {
            background-color: #4CAF50;
            color: white;
        }
        
        .audit-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .audit-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 6px 12px;
            border: 1px solid #ddd;
            color: #4CAF50;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .pagination a:hover {
            background-color: #f1f1f1;
        }
        
        .pagination .active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .export-btn {
            margin-left: auto;
            background-color: #28a745;
            color: white;
        }
        
        .actions-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content-wrapper">
        <h1 class="page-title"><i class="fas fa-history"></i> Audit Trail</h1>
        
        <div class="filters-container">
            <form class="filters-form" method="GET" action="">
                <div class="form-group">
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" placeholder="Search by username or activity" value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="audit_trail.php" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Reset</a>
                </div>
            </form>
        </div>
        
        <div class="actions-container">
            <div class="result-count">
                <strong>Total Records: <?php echo $totalRecords; ?></strong>
            </div>
            <a href="export_audit.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn export-btn">
                <i class="fas fa-file-export"></i> Export to CSV
            </a>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Activity</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['trail_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['trail_username']); ?></td>
                                <td><?php echo htmlspecialchars($row['trail_activity']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['trail_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo !empty($startDate) ? '&start_date=' . urlencode($startDate) : ''; ?><?php echo !empty($endDate) ? '&end_date=' . urlencode($endDate) : ''; ?>">First</a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo !empty($startDate) ? '&start_date=' . urlencode($startDate) : ''; ?><?php echo !empty($endDate) ? '&end_date=' . urlencode($endDate) : ''; ?>">Prev</a>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo !empty($startDate) ? '&start_date=' . urlencode($startDate) : ''; ?><?php echo !empty($endDate) ? '&end_date=' . urlencode($endDate) : ''; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo !empty($startDate) ? '&start_date=' . urlencode($startDate) : ''; ?><?php echo !empty($endDate) ? '&end_date=' . urlencode($endDate) : ''; ?>">Next</a>
                        <a href="?page=<?php echo $totalPages; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo !empty($startDate) ? '&start_date=' . urlencode($startDate) : ''; ?><?php echo !empty($endDate) ? '&end_date=' . urlencode($endDate) : ''; ?>">Last</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-records">
                <i class="fas fa-info-circle"></i> No audit trail records found.
            </div>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile sidebar toggle functionality
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const contentWrapper = document.querySelector('.content-wrapper');
        
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', function() {
                sidebar.classList.add('active');
                contentWrapper.classList.add('with-sidebar');
            });
        }
        
        // Close sidebar when clicking on close button
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.remove('active');
                contentWrapper.classList.remove('with-sidebar');
            });
        }
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = sidebar.contains(event.target) || 
                                (mobileSidebarToggle && mobileSidebarToggle.contains(event.target));
                                
            if (!isClickInside && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                if (contentWrapper) {
                    contentWrapper.classList.remove('with-sidebar');
                }
            }
        });
    });
    </script>
</body>
</html>