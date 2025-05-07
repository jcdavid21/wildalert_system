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
    <title>WildAlert - Species Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .species-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .species-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .species-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .species-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            background-color: #eee;
        }

        .species-info {
            padding: 15px;
        }

        .species-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #014421;
        }

        .scientific-name {
            font-style: italic;
            margin-bottom: 10px;
            color: #666;
        }

        .species-details {
            font-size: 14px;
            color: #555;
        }

        .species-details p {
            margin: 5px 0;
        }

        .section-title {
            color: #014421;
        }

        .filters {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .filters .form-group {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 10px;
        }

        .filters select,
        .filters input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-btn {
            background-color: #014421;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-btn:hover {
            background-color: #013018;
        }

        .no-results {
            text-align: center;
            padding: 50px 0;
            color: #666;
        }

        .view-btn {
            background-color: #014421;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
        }

        .view-btn:hover {
            background-color: #013018;
        }

        /* Modal styles */
        .species-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close-modal:hover {
            color: #333;
        }

        .species-header {
            display: flex;
            margin-bottom: 20px;
        }

        .species-modal-image {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .species-modal-info {
            flex: 1;
        }

        .loading-spinner {
            text-align: center;
            padding: 30px;
        }

        .loading-spinner i {
            animation: spin 1s infinite linear;
            font-size: 40px;
            color: #014421;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media screen and (max-width: 768px) {
            .content {
                margin-left: 0;
            }

            .species-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }

            .species-header {
                flex-direction: column;
            }

            .species-modal-image {
                width: 100%;
                margin-right: 0;
                margin-bottom: 15px;
            }

            .modal-content {
                width: 90%;
                margin: 10% auto;
            }
        }

        .flex {
            display: flex;
            justify-content: space-between;
            align-items: center;

            border-bottom: 2px solid #014421;
            margin-bottom: 20px;
        }

        .flex a {
            text-decoration: none;
        }

        .flex button {
            background-color: #014421;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .flex button:hover {
            background-color: #013018;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="flex">
            <h1 class="section-title">Species Library</h1>
            <a href="models.php">
                <button>
                    3D Models
                </button>
            </a>
        </div>

        <div class="filters">
            <form method="GET" action="">
                <div class="form-group">
                    <select name="category" id="category">
                        <option value="">All Categories</option>
                        <?php
                        $categoryQuery = "SELECT * FROM tbl_category";
                        $stmt = $conn->prepare($categoryQuery);
                        $stmt->execute();
                        $categoryResult = $stmt->get_result();


                        if ($categoryResult->num_rows > 0) {
                            while ($row = $categoryResult->fetch_assoc()) {
                                $selected = (isset($_GET['category']) && $_GET['category'] == $row['category_id']) ? 'selected' : '';
                                echo "<option value='" . $row['category_id'] . "' $selected>" . $row['category_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <select name="type" id="type">
                        <option value="">All Types</option>
                        <?php
                        $typeQuery = "SELECT * FROM tbl_species_type";
                        $typeResult = $conn->query($typeQuery);

                        if ($typeResult->num_rows > 0) {
                            while ($row = $typeResult->fetch_assoc()) {
                                $selected = (isset($_GET['type']) && $_GET['type'] == $row['type_id']) ? 'selected' : '';
                                echo "<option value='" . $row['type_id'] . "' $selected>" . $row['type_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <input type="text" name="search" placeholder="Search species..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>

                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <div class="species-grid">
            <?php
            // Build the SQL query based on filters
            $sql = "SELECT s.*, c.category_name, t.type_name 
                   FROM tbl_species s
                   JOIN tbl_category c ON s.category_id = c.category_id
                   JOIN tbl_species_type t ON s.type_id = t.type_id
                   WHERE 1=1";

            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $categoryId = $conn->real_escape_string($_GET['category']);
                $sql .= " AND s.category_id = '$categoryId'";
            }

            if (isset($_GET['type']) && !empty($_GET['type'])) {
                $typeId = $conn->real_escape_string($_GET['type']);
                $sql .= " AND s.type_id = '$typeId'";
            }

            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $conn->real_escape_string($_GET['search']);
                $sql .= " AND (s.species_name LIKE '%$search%' OR s.scientific_name LIKE '%$search%')";
            }

            $sql .= " ORDER BY s.species_name ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="species-card">';
                    echo '<img src="' . $row['image_path'] . '" alt="' . $row['species_name'] . '" class="species-image" onerror="this.src=\'../images/placeholder.jpg\'">';
                    echo '<div class="species-info">';
                    echo '<div class="species-name">' . $row['species_name'] . '</div>';
                    echo '<div class="scientific-name">' . $row['scientific_name'] . '</div>';
                    echo '<div class="species-details">';
                    echo '<p><strong>Kingdom:</strong> ' . $row['kingdom'] . '</p>';
                    echo '<p><strong>Group:</strong> ' . $row['group_name'] . '</p>';
                    echo '<p><strong>Category:</strong> ' . $row['category_name'] . '</p>';
                    echo '<p><strong>Type:</strong> ' . $row['type_name'] . '</p>';
                    echo '</div>';
                    // Add the View Species button with data attributes
                    echo '<button class="view-btn" data-species-id="' . $row['species_id'] . '" data-species-name="' . $row['species_name'] . '" data-scientific-name="' . $row['scientific_name'] . '" data-image="' . $row['image_path'] . '">';
                    echo '<i class="fas fa-eye"></i> View Species';
                    echo '</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="no-results">';
                echo '<i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>';
                echo '<h3>No species found</h3>';
                echo '<p>Try adjusting your search criteria or browse all species.</p>';
                echo '</div>';
            }

            $conn->close();
            ?>
        </div>
    </div>

    <!-- Species Details Modal -->
    <div id="speciesModal" class="species-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modalContent">
                <div class="loading-spinner">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.content');

            // Setup sidebar toggle functionality
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');

                    // Adjust content margin based on sidebar state
                    if (window.innerWidth > 768) {
                        if (sidebar.classList.contains('active')) {
                            content.style.marginLeft = '250px';
                        } else {
                            content.style.marginLeft = '0';
                        }
                    }
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    content.style.marginLeft = '0';
                } else {
                    if (sidebar.classList.contains('active')) {
                        content.style.marginLeft = '250px';
                    } else {
                        content.style.marginLeft = '0';
                    }
                }
            });

            // Initial setup
            if (window.innerWidth <= 768) {
                content.style.marginLeft = '0';
                sidebar.classList.remove('active');
            } else {
                sidebar.classList.add('active');
                content.style.marginLeft = '250px';
            }

            // Species modal functionality
            const modal = document.getElementById('speciesModal');
            const modalContent = document.getElementById('modalContent');
            const closeModal = document.querySelector('.close-modal');

            // Close modal when clicking the X
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }

            // Close modal when clicking outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Add click event to all view buttons
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const speciesId = this.getAttribute('data-species-id');
                    const speciesName = this.getAttribute('data-species-name');
                    const scientificName = this.getAttribute('data-scientific-name');
                    const imagePath = this.getAttribute('data-image');

                    // Show modal with loading spinner
                    modal.style.display = 'block';
                    modalContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner"></i></div>';

                    // Fetch species details
                    fetchSpeciesDetails(speciesId, speciesName, scientificName, imagePath);
                });
            });
        });

        function fetchSpeciesDetails(speciesId, speciesName, scientificName, imagePath) {
            // Fix image path if needed
            const fixedImagePath = imagePath || '../images/placeholder.jpg';
            const modal = document.getElementById('speciesModal');
            const modalContent = document.getElementById('modalContent');

            // Show modal with loading spinner
            modal.style.display = 'block';
            modalContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner"></i></div>';

            // Make API call directly to the Python backend
            fetch('http://localhost:8800/get_species_details', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        species_id: speciesId,
                        species_name: speciesName,
                        scientific_name: scientificName
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Create modal content HTML
                    let modalHTML = `
                    <div class="species-header">
                        <img src="${fixedImagePath}" class="species-modal-image" onerror="this.src='../images/placeholder.jpg'">
                        <div class="species-modal-info">
                            <h2>${speciesName}</h2>
                            <p><em>${scientificName}</em></p>
                            <hr>
                        </div>
                    </div>
                    <div class="species-full-details">
                        ${data.details}
                    </div>
                `;

                    // Update modal content
                    modalContent.innerHTML = modalHTML;
                })
                .catch(error => {
                    console.error('Error fetching species details:', error);

                    // Create fallback content
                    const fallbackHTML = `
                    <h2>Description</h2>
                    <p>Information about this species is currently being compiled. Check back soon for detailed information about ${speciesName} (${scientificName}).</p>
                    
                    <h2>Basic Information</h2>
                    <ul>
                        <li><strong>Common Name:</strong> ${speciesName}</li>
                        <li><strong>Scientific Name:</strong> ${scientificName}</li>
                    </ul>
                    
                    <h2>Note</h2>
                    <p>We're currently unable to retrieve detailed information about this species. This could be due to server load or connectivity issues. Please try again later.</p>
                `;

                    modalContent.innerHTML = `
                    <div class="species-header">
                        <img src="${fixedImagePath}" class="species-modal-image" onerror="this.src='../images/placeholder.jpg'">
                        <div class="species-modal-info">
                            <h2>${speciesName}</h2>
                            <p><em>${scientificName}</em></p>
                            <hr>
                        </div>
                    </div>
                    <div class="species-full-details">
                        ${fallbackHTML}
                    </div>
                `;
                });
        }
    </script>
</body>

</html>