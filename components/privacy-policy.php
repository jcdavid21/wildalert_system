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
    <title>Privacy Policy</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .hero {
            background-color: #2E7D32;
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .hero h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }
        section {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2E7D32;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            margin-top: 0;
        }
        h3 {
            color: #2E7D32;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        ul {
            padding-left: 20px;
        }
        .last-updated {
            font-style: italic;
            text-align: center;
            margin-top: 40px;
            color: #666;
        }
        .contact {
            background-color: #f1f8e9;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="hero">
        <div class="container">
            <h1>Privacy Policy</h1>
            <p>How we collect, use, and protect your data while using WildAlert</p>
        </div>
    </div>

    <div class="container">
        <section>
            <h2>Introduction</h2>
            <p>Welcome to WildAlert. We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website and use our services, and it tells you about your privacy rights and how the law protects you.</p>
            
            <p>This policy applies to information we collect through:</p>
            <ul>
                <li>Our website</li>
                <li>Our mobile application</li>
                <li>Email, text, and other electronic communications</li>
                <li>User-submitted content including images and reports</li>
            </ul>
        </section>

        <section>
            <h2>Information We Collect</h2>
            
            <h3>Personal Information</h3>
            <p>We may collect, use, store, and transfer different kinds of personal data about you, including:</p>
            <ul>
                <li><strong>Identity Data</strong>: Name, username, and profile information</li>
                <li><strong>Contact Data</strong>: Email address and optional phone number</li>
                <li><strong>Technical Data</strong>: IP address, browser type and version, time zone setting, browser plug-in types, operating system and platform</li>
                <li><strong>Usage Data</strong>: Information about how you use our website and services</li>
                <li><strong>Location Data</strong>: Geographic coordinates when you submit species reports (if permitted)</li>
            </ul>

            <h3>Species Report Data</h3>
            <p>When you submit reports or identify species, we collect:</p>
            <ul>
                <li>Images you upload</li>
                <li>Geographic location of sightings (if provided)</li>
                <li>Species identification information</li>
                <li>Conservation status reports</li>
                <li>Date and time of observations</li>
                <li>Environmental conditions (if provided)</li>
            </ul>
        </section>

        <section>
            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide, operate, and maintain our services</li>
                <li>Improve and personalize your user experience</li>
                <li>Develop new features, products, and services</li>
                <li>Communicate with you about your account, updates, or other information</li>
                <li>Process species identification requests</li>
                <li>Create biodiversity maps and conservation reports</li>
                <li>Share aggregated biodiversity data with research partners</li>
                <li>Monitor and analyze usage patterns and trends</li>
            </ul>
        </section>

        <section>
            <h2>Scientific and Research Use</h2>
            <p>The biodiversity data you contribute (species sightings, images, location data) may be used for scientific research and conservation purposes. We may share this data with:</p>
            <ul>
                <li>Academic research institutions</li>
                <li>Conservation organizations</li>
                <li>Government environmental agencies</li>
                <li>Other scientific partners</li>
            </ul>
            
            <p>When sharing data for scientific purposes, we prioritize your privacy by:</p>
            <ul>
                <li>Removing personally identifiable information unless you've explicitly opted to be credited</li>
                <li>Providing only the level of geographic precision necessary for scientific validity</li>
                <li>Ensuring partners agree to appropriate data protection terms</li>
            </ul>
        </section>

        <section>
            <h2>Data Sharing and Disclosure</h2>
            <p>We may share your personal information with:</p>
            <ul>
                <li><strong>Service Providers</strong>: Companies that help us deliver our services (e.g., cloud storage, email services)</li>
                <li><strong>Research Partners</strong>: As described in the Scientific and Research Use section</li>
                <li><strong>Legal Requirements</strong>: When required by law or to protect our rights</li>
            </ul>
            
            <p>We do not sell your personal information to third parties.</p>
        </section>

        <section>
            <h2>Image Usage and Rights</h2>
            <p>When you upload images to WildAlert:</p>
            <ul>
                <li>You retain ownership of your images</li>
                <li>You grant us a non-exclusive license to use, reproduce, and display the images for platform functionality</li>
                <li>Images may be used for species identification, verification, and database development</li>
                <li>With your permission (via opt-in), images may be used in educational materials, research publications, or conservation reports</li>
            </ul>
            
            <p>For sensitive species (e.g., endangered or at risk of poaching), we may automatically obscure precise location data to protect these species.</p>
        </section>

        <section>
            <h2>Data Security</h2>
            <p>We have implemented appropriate security measures to prevent your personal data from being accidentally lost, used, or accessed in an unauthorized way. We limit access to your personal data to employees and partners who have a business need to know.</p>
            
            <p>While we use reasonable efforts to protect your personal information, no method of transmission over the Internet or electronic storage is 100% secure.</p>
        </section>

        <section>
            <h2>Your Data Rights</h2>
            <p>Depending on your location, you may have the following rights regarding your data:</p>
            <ul>
                <li>Access your personal data</li>
                <li>Correct inaccurate data</li>
                <li>Delete your personal data</li>
                <li>Object to processing of your data</li>
                <li>Request restriction of processing</li>
                <li>Request transfer of your data</li>
                <li>Withdraw consent</li>
            </ul>
            
            <p>If you wish to exercise any of these rights, please contact us using the details in the Contact section.</p>
        </section>

        <section>
            <h2>Children's Privacy</h2>
            <p>Our services are not directed to children under 13 (or the applicable age in your jurisdiction). We do not knowingly collect personal information from children. If you believe we have collected personal information from a child, please contact us so we can remove the information.</p>
        </section>

        <section>
            <h2>Cookies and Tracking</h2>
            <p>We use cookies and similar tracking technologies to track activity on our platform and hold certain information. Cookies are files with small amounts of data which may include an anonymous unique identifier.</p>
            
            <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our service.</p>
        </section>

        <section>
            <h2>Changes to This Privacy Policy</h2>
            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
            
            <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
        </section>

        <section class="contact">
            <h2>Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us:</p>
            <ul>
                <li>By email: privacy@biodiversitytracker.org</li>
            </ul>
        </section>
        
        <p class="last-updated">Last Updated: May 7, 2025</p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>