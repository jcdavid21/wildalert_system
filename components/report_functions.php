<?php
/**
 * WildAlert Report Functions
 * This file contains utility functions for managing reports
 */

/**
 * Updates the status of a report
 * 
 * @param mysqli $conn Database connection
 * @param int $report_id Report ID
 * @param int $status_id New status ID
 * @param string $admin_comments Admin comments (optional)
 * @param int $admin_id Admin ID who updated the status
 * @return bool True on success, false on failure
 */
function updateReportStatus($conn, $report_id, $status_id, $admin_comments = '', $admin_id = 0) {
    // Validate inputs
    $report_id = intval($report_id);
    $status_id = intval($status_id);
    $admin_id = intval($admin_id);
    
    // Prepare update query
    $update_query = "UPDATE tbl_reports 
                    SET status_report_id = ?, 
                        admin_comments = ?, 
                        admin_id = ?,
                        admin_update_date = NOW() 
                    WHERE report_id = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("isii", $status_id, $admin_comments, $admin_id, $report_id);
    $result = $stmt->execute();
    
    return $result;
}

/**
 * Adds a conservation action to a report
 * 
 * @param mysqli $conn Database connection
 * @param int $report_id Report ID
 * @param string $action_title Title of the action
 * @param string $action_description Description of the action
 * @param string $conducted_by Who conducted the action
 * @param string $action_date Date of the action (YYYY-MM-DD)
 * @param int $admin_id Admin ID who added the action
 * @return bool True on success, false on failure
 */
function addConservationAction($conn, $report_id, $action_title, $action_description, $conducted_by, $action_date, $admin_id) {
    // Validate inputs
    $report_id = intval($report_id);
    $admin_id = intval($admin_id);
    
    // Prepare insert query
    $insert_query = "INSERT INTO tbl_conservation_actions 
                    (report_id, action_title, action_description, conducted_by, action_date, admin_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("issssi", $report_id, $action_title, $action_description, $conducted_by, $action_date, $admin_id);
    $result = $stmt->execute();
    
    return $result;
}

/**
 * Gets reports by user ID with optional filters
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @param array $filters Optional filters array (status, date_from, date_to, species)
 * @param int $page Page number
 * @param int $per_page Items per page
 * @return array Array containing 'reports' and 'total_records'
 */
function getReportsByUser($conn, $user_id, $filters = [], $page = 1, $per_page = 10) {
    $user_id = intval($user_id);
    $page = intval($page);
    $per_page = intval($per_page);
    $offset = ($page - 1) * $per_page;
    
    // Base query
    $base_query = "FROM tbl_reports r
                  JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
                  JOIN tbl_category c ON r.category_id = c.category_id
                  WHERE r.acc_id = ?";
    
    $params = array($user_id);
    $types = "i";
    
    // Add filters if provided
    if (!empty($filters['status'])) {
        $base_query .= " AND r.status_report_id = ?";
        $params[] = intval($filters['status']);
        $types .= "i";
    }

    if (!empty($filters['date_from'])) {
        $base_query .= " AND r.date_time >= ?";
        $params[] = $filters['date_from'] . ' 00:00:00';
        $types .= "s";
    }

    if (!empty($filters['date_to'])) {
        $base_query .= " AND r.date_time <= ?";
        $params[] = $filters['date_to'] . ' 23:59:59';
        $types .= "s";
    }

    if (!empty($filters['species'])) {
        $base_query .= " AND r.species_name LIKE ?";
        $params[] = "%" . $filters['species'] . "%";
        $types .= "s";
    }
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total " . $base_query;
    $stmt = $conn->prepare($count_query);
    
    // Bind parameters
    bind_params($stmt, $types, $params);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_records = $total_row['total'];
    
    // Get reports with pagination
    $reports_query = "SELECT r.report_id, r.species_name, c.category_name, r.location, r.date_time, 
                     sr.status_rp_name, r.comments, r.image_path 
                     " . $base_query . " 
                     ORDER BY r.date_time DESC
                     LIMIT ?, ?";
    
    $new_params = $params;
    $new_params[] = $offset;
    $new_params[] = $per_page;
    $new_types = $types . "ii";
    
    $stmt = $conn->prepare($reports_query);
    bind_params($stmt, $new_types, $new_params);
    $stmt->execute();
    $reports_result = $stmt->get_result();
    
    $reports = [];
    while ($row = $reports_result->fetch_assoc()) {
        $reports[] = $row;
    }
    
    return [
        'reports' => $reports,
        'total_records' => $total_records
    ];
}

/**
 * Gets a specific report by ID and user ID
 * 
 * @param mysqli $conn Database connection
 * @param int $report_id Report ID
 * @param int $user_id User ID
 * @return array|null Report data or null if not found
 */
function getReportById($conn, $report_id, $user_id) {
    $report_id = intval($report_id);
    $user_id = intval($user_id);
    
    $query = "SELECT r.*, c.category_name, s.status_name, sr.status_rp_name 
              FROM tbl_reports r
              JOIN tbl_category c ON r.category_id = c.category_id
              JOIN tbl_status s ON r.status_id = s.status_id
              JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
              WHERE r.report_id = ? AND r.acc_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $report_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}

/**
 * Helper function to bind parameters dynamically
 * 
 * @param mysqli_stmt $stmt Statement object
 * @param string $types Parameter types string
 * @param array $params Parameters array
 */
function bind_params($stmt, $types, $params) {
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_names);
}

/**
 * Get status color for badges
 * 
 * @param string $status Status name
 * @return string Bootstrap color class
 */
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

/**
 * Gets the number of reports by status for a specific user
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @return array Status counts
 */
function getReportStatusCounts($conn, $user_id) {
    $user_id = intval($user_id);
    
    $query = "SELECT sr.status_rp_name, COUNT(*) as count 
              FROM tbl_reports r
              JOIN tbl_status_report sr ON r.status_report_id = sr.status_rp_id
              WHERE r.acc_id = ?
              GROUP BY r.status_report_id
              ORDER BY sr.status_rp_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $counts[$row['status_rp_name']] = $row['count'];
    }
    
    return $counts;
}