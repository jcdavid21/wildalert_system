<?php include '../backend/config/config.php'; ?>

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


$error = '';
$success = '';

// Process signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $check_sql = "SELECT acc_id FROM tbl_account WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email already exists. Please use a different email or login.";
        } else {
            // Email is unique, proceed with registration
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Default role_id is 1 (regular user)
            $role_id = 1;
            
            // Insert new user
            $insert_sql = "INSERT INTO tbl_account (email, password, role_id) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssi", $email, $hashed_password, $role_id);
            
            if ($insert_stmt->execute()) {
                $success = "Account created successfully! You can now login.";
                // Redirect to login page after a short delay
                header("refresh:3;url=login.php");
            } else {
                $error = "Registration failed. Please try again later.";
            }
            $insert_stmt->close();
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
    <title>Sign Up - WildAlert</title>
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

        .signup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px - 300px);
            padding: 20px;
        }

        .signup-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
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

        .password-requirements {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
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

        .password-toggle {
            position: relative;
        }

        .password-toggle i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }

        @media screen and (max-width: 480px) {
            .signup-card {
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <!-- Include the Navbar -->
    <?php include 'navbar.php'; ?>
    
    <div class="signup-container">
        <div class="signup-card">
            <div class="card-header">
                <i class="fas fa-user-plus" style="margin-right: 10px;"></i>Create Account
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-toggle">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                            <i class="far fa-eye" id="togglePassword"></i>
                        </div>
                        <div class="password-requirements">
                            Password must be at least 8 characters long.
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-toggle">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
                            <i class="far fa-eye" id="toggleConfirmPassword"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms" style="display: inline; margin-left: 5px;">
                            I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="privacy-policy.php" target="_blank">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn">Sign Up</button>
                </form>
            </div>
            <div class="card-footer">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
    
    <!-- Include the Footer -->
    <?php include 'footer.php'; ?>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle the icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Toggle confirm password visibility
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirm_password');
        
        toggleConfirmPassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            
            // Toggle the icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password match validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match');
            }
        });
    </script>
</body>
</html>