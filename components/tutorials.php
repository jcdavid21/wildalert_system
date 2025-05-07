<?php 
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../backend/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Tutorial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
        }
        .page-header {
            background-color: #f8f9fa;
            padding: 30px 0;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #e7e7e7;
        }
        .page-header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 2.5rem;
        }
        .page-header p {
            margin-top: 10px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        .tutorial-step {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .tutorial-step h3 {
            color: #2c3e50;
            margin-top: 0;
            display: flex;
            align-items: center;
            border-bottom: 2px solid #e7e7e7;
            padding-bottom: 10px;
        }
        .tutorial-step h3 i {
            margin-right: 10px;
            color: #27ae60;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php' ?>

    <div class="page-header">
        <div class="container">
            <h1>Tutorial</h1>
            <p>Learn how to use our platform effectively</p>
        </div>
    </div>

    <div class="container">
        <h2>Getting Started</h2>
        
        <div class="tutorial-step">
            <h3><i class="fas fa-user-plus"></i> Creating an Account</h3>
            <p>Before you can submit reports or access all features, you need to create an account:</p>
            <ol>
                <li>Click on the "Sign Up" button in the navigation menu</li>
                <li>Fill in your personal information in the registration form</li>
                <li>Choose a strong password</li>
                <li>Click "Create Account" to complete registration</li>
                <li>Check your email for a verification link (if applicable)</li>
            </ol>
        </div>

        <div class="tutorial-step">
            <h3><i class="fas fa-sign-in-alt"></i> Logging In</h3>
            <p>To access your account and use all features:</p>
            <ol>
                <li>Click the "Login" button in the navigation menu</li>
                <li>Enter your registered email address and password</li>
                <li>Click "Login" to access your account</li>
            </ol>
        </div>

        <div class="tutorial-step">
            <h3><i class="fas fa-clipboard-list"></i> Submitting a Report</h3>
            <p>To submit a report about a species that is alive or endangered:</p>
            <ol>
                <li>Log in to your account</li>
                <li>Navigate to the "Report" section</li>
                <li>Fill out the report form with details about the species</li>
                <li>Include information about the location and condition</li>
                <li>Upload any relevant photos if available</li>
                <li>Review your information for accuracy</li>
                <li>Click "Submit Report" to finalize</li>
            </ol>
            <p>Your report will be reviewed by our team and added to our database.</p>
        </div>

        <div class="tutorial-step">
            <h3><i class="fas fa-book-open"></i> Using the Species Library</h3>
            <p>To explore and learn about different species in our database:</p>
            <ol>
                <li>Navigate to the "Library" section from the main menu</li>
                <li>Browse the categories or use the search function to find specific species</li>
                <li>Click on any species to view detailed information</li>
                <li>Read about habitat, conservation status, physical characteristics, and more</li>
                <li>Use the filter options to narrow down results by conservation status, habitat, etc.</li>
            </ol>
        </div>

        <div class="tutorial-step">
            <h3><i class="fas fa-user-edit"></i> Managing Your Profile</h3>
            <p>To update your profile information:</p>
            <ol>
                <li>Click on your username or profile picture in the top-right corner</li>
                <li>Select "Profile" from the dropdown menu</li>
                <li>Update your personal information as needed</li>
                <li>Change your password if necessary</li>
                <li>Click "Save Changes" to update your profile</li>
            </ol>
        </div>
    </div>

    <script>
        // Any JavaScript you want to add
    </script>

    <?php include 'footer.php' ?>
</body>
</html>