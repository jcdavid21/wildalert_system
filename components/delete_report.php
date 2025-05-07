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

// Check if it's a POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Not a POST request, redirect to reports page
    header("Location: reports_history.php");
    exit();
}

// Check if report_id is provided
if (!isset($_POST['report_id']) || !is_numeric($_POST['report_id'])) {
    $_SESSION['error'] = "Invalid report ID.";
    header("Location: reports_history.php");
    exit();
}

// Database connection
require_once '../backend/config/config.php';

$report_id = intval($_POST['report_id']);
$user_id = $_SESSION['user_id'];

// First, check if the report exists, belongs to the user, and is still in pending status
$check_query = "SELECT report_id, image_path FROM tbl_reports 
                WHERE report_id = ? AND acc_id = ? AND status_report_id = 1";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Report not found, doesn't belong to the user, or is not in pending status
    $_SESSION['error'] = "You cannot delete this report. It may have been processed already or doesn't exist.";
    header("Location: reports_history.php");
    exit();
}

// Get the image path to delete the file if it exists
$report = $result->fetch_assoc();
$image_path = $report['image_path'];

// Begin transaction
$conn->begin_transaction();

try {
    // Delete any related records in other tables
    
    // Delete comments if you have a comments table
    $delete_comments = "DELETE FROM tbl_comments WHERE report_id = ?";
    $stmt = $conn->prepare($delete_comments);
    if ($stmt) {
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
    }
    
    // Delete the report itself
    $delete_report = "DELETE FROM tbl_reports WHERE report_id = ? AND acc_id = ?";
    $stmt = $conn->prepare($delete_report);
    $stmt->bind_param("ii", $report_id, $user_id);
    $stmt->execute();
    
    // Check if the report was deleted
    if ($stmt->affected_rows === 0) {
        // No rows affected, likely already deleted or doesn't belong to user
        throw new Exception("Failed to delete report. It may have been already deleted.");
    }
    
    // Commit the transaction
    $conn->commit();
    
    // Delete the image file if it exists
    if (!empty($image_path) && file_exists($image_path)) {
        unlink($image_path);
    }
    
    // Success message
    $_SESSION['success'] = "Report deleted successfully.";
    header("Location: reports_history.php");
    exit();
    
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    
    // Error message
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: reports_history.php");
    exit();
}
?>