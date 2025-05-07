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

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="audit_trail_export_' . date('Y-m-d') . '.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add BOM to fix CSV encoding in Excel
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Output the column headings
fputcsv($output, array('ID', 'Username', 'Activity', 'Date & Time'));

// Get search parameters
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build the query with conditions
$searchCondition = '';
$params = array();
$paramTypes = '';

if (!empty($searchQuery)) {
    $searchParam = "%" . $searchQuery . "%";
    $searchCondition = "WHERE trail_username LIKE ? OR trail_activity LIKE ?";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $paramTypes .= 'ss';
}

if (!empty($startDate) && !empty($endDate)) {
    if (empty($searchCondition)) {
        $searchCondition = "WHERE trail_date BETWEEN ? AND ?";
    } else {
        $searchCondition .= " AND trail_date BETWEEN ? AND ?";
    }
    $params[] = $startDate;
    $params[] = $endDate;
    $paramTypes .= 'ss';
}

// Prepare and execute the query
$query = "SELECT * FROM tbl_audit_trail $searchCondition ORDER BY trail_date DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Loop over the rows, outputting them
while ($row = $result->fetch_assoc()) {
    $csvRow = array(
        $row['trail_id'],
        $row['trail_username'],
        $row['trail_activity'],
        date('Y-m-d H:i:s', strtotime($row['trail_date']))
    );
    fputcsv($output, $csvRow);
}

// Log this export in the audit trail
$currentUser = $_SESSION['user_email'];
$activity = "Exported audit trail to CSV";
$currentDateTime = date('Y-m-d H:i:s');

$auditQuery = "INSERT INTO tbl_audit_trail (trail_username, trail_activity, trail_date) VALUES (?, ?, ?)";
$auditStmt = $conn->prepare($auditQuery);
$auditStmt->bind_param("sss", $currentUser, $activity, $currentDateTime);
$auditStmt->execute();