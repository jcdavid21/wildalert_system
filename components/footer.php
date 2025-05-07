<?php
// No PHP processing needed for footer, just HTML and CSS
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Footer Styles */
        .footer {
            background-color: #014421; /* Dark green */
            color: white;
            padding: 30px 0;
            margin-top: 30px;
            z-index: 1000;
        }

        .footer-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1;
            min-width: 250px;
            padding: 0 20px;
            margin-bottom: 20px;
        }

        .footer-section h3 {
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 5px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #e6e6e6;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: white;
            text-decoration: underline;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
        }

        .office-hours {
            margin-top: 10px;
        }

        .office-hours p {
            margin: 5px 0;
            font-size: 14px;
        }

        @media screen and (max-width: 768px) {
            .footer-section {
                flex: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>About WildAlert</h3>
                <ul>
                    <li><a href="aboutUs.php">About Us</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Help & Support</h3>
                <ul>
                    <li><a href="tutorials.php">Tutorials</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <!-- <li><a href="contact.php">Contact Us</a></li> -->
                    <li><a href="reports.php">Report an Issue</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <ul>
                    <li><a href="https://facebook.com/wildalert" target="_blank">Facebook</a></li>
                    <li><a href="https://twitter.com/wildalert" target="_blank">Twitter</a></li>
                    <li><a href="https://instagram.com/wildalert" target="_blank">Instagram</a></li>
                    <li><a href="https://youtube.com/wildalert" target="_blank">YouTube</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> WildAlert. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>