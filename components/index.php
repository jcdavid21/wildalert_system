<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildAlert - Protecting Our Natural Heritage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/ph_eagle.avif') no-repeat center center;
            background-size: cover;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .hero-content {
            max-width: 800px;
            padding: 20px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .cta-button {
            background-color: #014421;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
        }

        .cta-button.secondary {
            background-color: transparent;
            border: 2px solid white;
        }

        .cta-button:hover {
            background-color: #02703a;
        }

        .cta-button.secondary:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Species Categories Section */
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2rem;
            color: #014421;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background-color: #014421;
            margin: 15px auto 0;
        }

        .species-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .species-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .species-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.15);
        }

        .species-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .species-info {
            padding: 20px;
        }

        .species-info h3 {
            margin-bottom: 10px;
            color: #014421;
            font-size: 1.4rem;
        }

        .species-info p {
            color: #666;
            margin-bottom: 15px;
        }

        .species-card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #014421;
            display: block;
            text-align: center;
        }

        /* Featured Report Section */
        .featured-report {
            background-color: #e9f5f0;
            padding: 60px 0;
            margin-bottom: 60px;
        }

        .report-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 0 auto;
        }

        .report-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .report-content {
            padding: 30px;
        }

        .report-content h3 {
            color: #014421;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .report-content p {
            margin-bottom: 20px;
            color: #555;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 20px;
        }

        .report-author {
            display: flex;
            align-items: center;
        }

        .author-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Stats Section */
        .stats-section {
            padding: 40px 0;
            margin-bottom: 60px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-item {
            background-color: white;
            padding: 30px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #014421;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #555;
        }

        /* Footer styling would be from your included footer.php */

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .hero {
                height: 400px;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .species-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .report-card {
                flex-direction: column;
            }
            
            .report-image {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover and Protect Earth's Biodiversity</h1>
            <p>Join our community in documenting, learning about, and conserving the diverse species of our planet</p>
            <div>
                <a href="library.php" class="cta-button">Explore Species</a>
                <a href="view_report.php" class="cta-button secondary">View Reports</a>
            </div>
        </div>
    </section>

    <!-- Species Categories Section -->
    <section class="container">
        <h2 class="section-title">Explore by Kingdom</h2>
        <div class="species-grid">
            <a href="category.php?category_id=1" class="species-card">
                <img src="../images/ph_eagle-1.avif" alt="Animalia" class="species-image">
                <div class="species-info">
                    <i class="fas fa-paw icon"></i>
                    <h3>Animalia</h3>
                    <p>Discover Philippine wildlife including the Philippine Eagle, Tamaraw, and Tarsier</p>
                </div>
            </a>
            <a href="category.php?category_id=5" class="species-card">
                <img src="../images/plantae.avif" alt="Plantae" class="species-image">
                <div class="species-info">
                    <i class="fas fa-seedling icon"></i>
                    <h3>Plantae</h3>
                    <p>Explore endemic plants like the Rafflesia and Waling-waling Orchid</p>
                </div>
            </a>
            <a href="category.php?category_id=2" class="species-card">
                <img src="../images/fungi.avif" alt="Fungi" class="species-image">
                <div class="species-info">
                    <i class="fas fa-cloud icon"></i>
                    <h3>Fungi</h3>
                    <p>Learn about mushrooms and other fascinating fungal organisms</p>
                </div>
            </a>
            <a href="category.php?category_id=4" class="species-card">
                <img src="../images/protista.webp" alt="Protista" class="species-image">
                <div class="species-info">
                    <i class="fas fa-bacteria icon"></i>
                    <h3>Protista</h3>
                    <p>Discover organisms like Giant Kelp and other protists</p>
                </div>
            </a>
            <a href="category.php?category_id=3" class="species-card">
                <img src="../images/monera.avif" alt="Monera" class="species-image">
                <div class="species-info">
                    <i class="fas fa-virus icon"></i>
                    <h3>Monera</h3>
                    <p>Explore bacteria like Cyanobacteria and Nitrogen-fixing bacteria</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Featured Species Section -->
    <section class="featured-report">
        <div class="container">
            <h2 class="section-title">Featured Species</h2>
            <div class="report-card">
                <img src="../images/ph_eagle-1.avif" alt="Philippine Eagle" class="report-image">
                <div class="report-content">
                    <div class="report-meta">
                        <span>Endemic to the Philippines</span>
                        <div class="report-author">
                            <span><strong>Status:</strong> Critically Endangered</span>
                        </div>
                    </div>
                    <h3>Philippine Eagle (Pithecophaga jefferyi)</h3>
                    <p>The Philippine Eagle is one of the rarest, largest, and most powerful birds in the world. With a wingspan of up to 7 feet, it's considered the national bird of the Philippines. These magnificent birds of prey are critically endangered, with only an estimated 400 breeding pairs remaining in the wild due to deforestation and hunting.</p>
                    <a href="library.php" class="cta-button">Learn More</a>
                </div>
            </div>
        </div>
    </section>


    <!-- Additional Features Section -->
    <section class="container">
        <h2 class="section-title">What You Can Do</h2>
        <div class="species-grid">
            <a href="contribute.php" class="species-card">
                <div class="species-info">
                    <i class="fas fa-camera-retro icon"></i>
                    <h3>Document Species</h3>
                    <p>Submit your sightings and help expand our knowledge database</p>
                </div>
            </a>
            <a href="endangered.php" class="species-card">
                <div class="species-info">
                    <i class="fas fa-exclamation-triangle icon"></i>
                    <h3>Report Threats</h3>
                    <p>Alert authorities about illegal activities or threats to species</p>
                </div>
            </a>
            <a href="education.php" class="species-card">
                <div class="species-info">
                    <i class="fas fa-book-open icon"></i>
                    <h3>Learn & Educate</h3>
                    <p>Access educational resources and spread awareness</p>
                </div>
            </a>
            <a href="events.php" class="species-card">
                <div class="species-info">
                    <i class="fas fa-users icon"></i>
                    <h3>Join Activities</h3>
                    <p>Participate in community events and conservation efforts</p>
                </div>
            </a>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>

        // Search suggestions functionality
        const searchInput = document.getElementById('searchInput');
        const searchSuggestions = document.getElementById('searchSuggestions');

        searchInput.addEventListener('focus', function() {
            // Here you would normally fetch suggestions from the server
            // For demo purposes, we'll just show some static suggestions
            showSuggestions(['Philippine Eagle', 'Tamaraw', 'Tarsier', 'Rafflesia', 'Waling-Waling']);
        });

        searchInput.addEventListener('input', function() {
            // In a real implementation, you would fetch filtered suggestions based on input
            if (searchInput.value.trim() !== '') {
                // Example - filter suggestions that include the input text
                const query = searchInput.value.toLowerCase();
                const allSuggestions = ['Philippine Eagle', 'Tamaraw', 'Tarsier', 'Rafflesia', 'Waling-Waling'];
                const filteredSuggestions = allSuggestions.filter(sugg => 
                    sugg.toLowerCase().includes(query)
                );
                showSuggestions(filteredSuggestions);
            } else {
                searchSuggestions.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });

        function showSuggestions(suggestions) {
            searchSuggestions.innerHTML = '';
            
            if (suggestions.length > 0) {
                suggestions.forEach(sugg => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.textContent = sugg;
                    div.addEventListener('click', function() {
                        searchInput.value = sugg;
                        searchSuggestions.style.display = 'none';
                        // Here you could trigger a search with the selected suggestion
                    });
                    searchSuggestions.appendChild(div);
                });
                searchSuggestions.style.display = 'block';
            } else {
                searchSuggestions.style.display = 'none';
            }
        }
    </script>
</body>
</html>