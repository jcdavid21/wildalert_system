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
    <title>About Us</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .hero {
            background-color: #2E7D32;
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .mission-section,
        .team-section,
        .how-it-works {
            margin-bottom: 50px;
        }

        h2 {
            color: #2E7D32;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .feature-box {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feature-box h3 {
            color: #2E7D32;
            margin-top: 0;
        }

        .cta-button {
            display: inline-block;
            background-color: #2E7D32;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #1B5E20;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .team-member {
            text-align: center;
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .team-member h4 {
            margin: 10px 0 5px;
            color: #2E7D32;
        }

        .team-member p {
            font-style: italic;
            margin-top: 0;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="hero">
        <div class="container">
            <h1>About WildAlert</h1>
            <p>Connecting citizens with science to document, identify, and protect the diverse species of our planet</p>
        </div>
    </div>

    <div class="container">
        <section class="mission-section">
            <h2>Our Mission</h2>
            <p>WildAlert is dedicated to advancing our understanding of global biodiversity through community participation. We believe that by empowering individuals to document and identify species across all kingdoms of life—animals, fungi, monera, protista, and plantae—we can collectively build a more comprehensive picture of our living world.</p>

            <p>Our platform serves as both an educational resource and a crucial data collection tool that helps scientists track biodiversity changes, monitor endangered species populations, and discover new species.</p>
        </section>

        <section class="how-it-works">
            <h2>What We Do</h2>
            <div class="feature-grid">
                <div class="feature-box">
                    <h3>Species Identification</h3>
                    <p>Upload images of any organism you encounter, and our AI-powered system helps identify the species. Our database covers animals, plants, fungi, bacteria, and protists from around the world.</p>
                </div>
                <div class="feature-box">
                    <h3>Conservation Status Reporting</h3>
                    <p>Contribute vital data by reporting whether a species appears endangered or thriving in its habitat. These observations help conservationists track population changes over time.</p>
                </div>
                <div class="feature-box">
                    <h3>WildAlert Mapping</h3>
                    <p>Your reports help us create detailed biodiversity maps showing species distribution, population density, and environmental health indicators across different regions.</p>
                </div>
            </div>
        </section>

        <p><strong>Office Hours:</strong></p>
        <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
        <p>Saturday: 9:00 AM - 12:00 PM</p>
        <p>Sunday: Closed</p>


        <section class="join-us">
            <h2>Join Our Community</h2>
            <p>Whether you're a professional biologist, an amateur naturalist, or simply curious about the living world around you, your contributions matter. Join thousands of citizen scientists helping to document and protect biodiversity worldwide.</p>
            <a href="login.php" class="cta-button">Create an Account</a>
        </section>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>