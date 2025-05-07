<?php include '../backend/config/config.php'; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $sql = "SELECT acc_id, email, password, role_id FROM tbl_account WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['acc_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role_id'];
                $_SESSION['user_name'] = explode('@', $user['email'])[0];
                
                if ($user['role_id'] == 2) {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WildAlert</title>
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

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px - 300px);
            padding: 20px;
        }

        .login-card {
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
            .login-card {
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <!-- Include the Navbar -->
    <?php include 'navbar.php'; ?>
    
    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <i class="fas fa-user-circle" style="margin-right: 10px;"></i>Login
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
                    </div>
                    
                    <button type="submit" class="btn">Login</button>
                </form>
            </div>
            <div class="card-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                <!-- <p style="margin-top: 10px;"><a href="forgot-password.php">Forgot Password?</a></p> -->
            </div>
        </div>
    </div>
    
    <!-- Include the Footer -->
    <?php include 'footer.php'; ?>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>