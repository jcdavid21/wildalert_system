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
    <title>Frequently Asked Questions</title>
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
        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #e7e7e7;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .faq-question {
            font-weight: bold;
            font-size: 1.1rem;
            color: #2c3e50;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 5px 5px 0 0;
        }
        .faq-question:hover {
            background-color: #e9ecef;
        }
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .faq-answer.show {
            padding: 20px;
            max-height: 500px;
            transition: max-height 0.5s ease-in;
        }
        .faq-category {
            margin-bottom: 40px;
        }
        .faq-category h2 {
            color: #2c3e50;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php' ?>

    <div class="page-header">
        <div class="container">
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about our platform</p>
        </div>
    </div>

    <div class="container">
        <div class="faq-category">
            <h2><i class="fas fa-info-circle"></i> General Information</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    What is the purpose of this platform?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Our platform serves as a community-driven database for tracking and monitoring endangered and at-risk species. By collecting reports from users like you, we can help conservation efforts and raise awareness about threatened wildlife.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Who can use this platform?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Anyone interested in wildlife conservation can use our platform. While viewing species information in the library is available to all visitors, creating an account allows you to submit reports and access additional features.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Is this service free to use?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Yes, our platform is completely free for all users. We believe that conservation efforts benefit from widespread community participation, so there are no fees to create an account or access any of our features.</p>
                </div>
            </div>
        </div>

        <div class="faq-category">
            <h2><i class="fas fa-user"></i> Account & Registration</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Do I need an account to view information in the library?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>No, the species library is accessible to all visitors. However, creating an account allows you to submit reports, save favorite species, and receive updates about conservation efforts.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    How do I reset my password?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>To reset your password:</p>
                    <ol>
                        <li>Click on the "Login" button in the navigation menu</li>
                        <li>Click on "Forgot Password" below the login form</li>
                        <li>Enter your registered email address</li>
                        <li>Check your email for password reset instructions</li>
                        <li>Follow the link in the email to create a new password</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="faq-category">
            <h2><i class="fas fa-clipboard-list"></i> Reports & Submissions</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    What information should I include in my species report?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>When submitting a report, please include:</p>
                    <ul>
                        <li>Species name (common and scientific if known)</li>
                        <li>Location where you observed the species (as specific as possible)</li>
                        <li>Date and time of the observation</li>
                        <li>Number of individuals observed</li>
                        <li>Condition of the animal/plant</li>
                        <li>Photos if available (these help with verification)</li>
                        <li>Any threats you observed in the habitat</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Are my reports publicly visible?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>By default, species reports are reviewed by our team before being added to the public database. Your personal information remains private, but the species data and location (general area) may be shared to help conservation efforts. You can set privacy preferences for your reports in your account settings.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    How do I know if a species is endangered?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Each species in our library is labeled with its conservation status according to the IUCN Red List categories:</p>
                    <ul>
                        <li><strong>Extinct (EX)</strong> - No known living individuals</li>
                        <li><strong>Extinct in the Wild (EW)</strong> - Survives only in captivity</li>
                        <li><strong>Critically Endangered (CR)</strong> - Extremely high risk of extinction</li>
                        <li><strong>Endangered (EN)</strong> - High risk of extinction</li>
                        <li><strong>Vulnerable (VU)</strong> - High risk of becoming endangered</li>
                        <li><strong>Near Threatened (NT)</strong> - Likely to become threatened in the near future</li>
                        <li><strong>Least Concern (LC)</strong> - Widespread and abundant</li>
                    </ul>
                    <p>This information is displayed prominently on each species profile.</p>
                </div>
            </div>
        </div>

        <div class="faq-category">
            <h2><i class="fas fa-book-open"></i> Using the Library</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    How can I find a specific species in the library?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>You can find species in our library in several ways:</p>
                    <ul>
                        <li>Use the search bar to search by common or scientific name</li>
                        <li>Browse by category (mammals, birds, reptiles, etc.)</li>
                        <li>Filter by conservation status</li>
                        <li>Filter by habitat or region</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Can I contribute information to species profiles?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Yes! While our core species information is maintained by our research team, registered users can contribute additional information, photos, and observations through the report system. Your contributions will be reviewed and may be incorporated into the species profiles.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFAQ(element) {
            // Toggle the active class on the question
            element.classList.toggle("active");
            
            // Toggle the show class on the answer
            var answer = element.nextElementSibling;
            if (answer.classList.contains("show")) {
                answer.classList.remove("show");
                element.querySelector("i").className = "fas fa-chevron-down";
            } else {
                answer.classList.add("show");
                element.querySelector("i").className = "fas fa-chevron-up";
            }
        }
    </script>

    <?php include 'footer.php' ?>
</body>
</html>