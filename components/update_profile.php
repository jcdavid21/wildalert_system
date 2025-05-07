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

// Handle form submission
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST['email']);
    
    // Validate input
    $valid = true;
    
    // Check email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
        $valid = false;
    }
    
    // Check if email already exists for another user
    $email_check_query = "SELECT acc_id FROM tbl_account WHERE email = ? AND acc_id != ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error_message = "Email is already in use by another account";
        $valid = false;
    }
    
    // Update user information if valid
    if ($valid) {
        $update_query = "UPDATE tbl_account SET email = ? WHERE acc_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $email, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully";
            
            // Refresh user data
            $stmt = $conn->prepare($user_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user_result = $stmt->get_result();
            $user_data = $user_result->fetch_assoc();
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - WildAlert</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>Edit Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                                <div class="form-text">This email is used for login and notifications.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars($user_data['role_name']); ?>" readonly>
                                <div class="form-text">Role cannot be changed. Contact administrator for role changes.</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>