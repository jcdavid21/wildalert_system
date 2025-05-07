<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to home page
    header("Location: index.php");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "WildAlert_db";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    
    // Validate email
    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if email exists in database
        $check_sql = "SELECT acc_id FROM tbl_account WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $error = "No account found with that email address";
        } else {
            // In a real application, you would:
            // 1. Generate a unique token
            // 2. Store the token in the database with an expiration time
            // 3. Send a password reset email to the user with a link containing the token
            
            // For demonstration, we'll just show a success message
            $success = "Password reset instructions have been sent to your email address. Please check your inbox.";
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - WildAlert</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .forgot-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px - 300px);
            padding: 20px;
        }

        .forgot-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .card-header {
            background-color: #014421;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .card-body {
            padding: 30px;
        }

        .card-message {
            margin-bottom: 20px;
            text-align: center;
            color: #555;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #014421;
            outline: none;
            box-shadow: 0 0 0 2px rgba(1, 68, 33, 0.2);
        }

        .btn {
            background-color: #014421;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #013319;
        }

        .error-message {
            color: #d9534f;
            margin-bottom: 15px;
            text-align: center;
        }

        .success-message {
            color: #5cb85c;
            margin-bottom: 15px;
            text-align: center;
        }

        .card-footer {
            background-color: #f9f9f9;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .card-footer a {
            color: #014421;
            text-decoration: none;
        }

        .card-footer a:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 480px) {
            .forgot-card {
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <!-- Include the Navbar -->
    <?php include 'navbar.php'; ?>
    
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="card-header">
                <i class="fas fa-lock" style="margin-right: 10px;"></i>Forgot Password
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="card-message">
                    <p>Enter your email address below and we'll send you instructions to reset your password.</p>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    
                    <button type="submit" class="btn">Reset Password</button>
                </form>
            </div>
            <div class="card-footer">
                <p>Remember your password? <a href="login.php">Login</a></p>
                <p style="margin-top: 10px;">Need an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>
    
    <!-- Include the Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>