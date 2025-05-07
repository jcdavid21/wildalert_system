<?php

include '../backend/config/config.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: login.php");
    exit();
}


// Handle species deletion if requested
if (isset($_POST['delete_species']) && isset($_POST['species_id'])) {
    $species_id = $_POST['species_id'];
    
    // Record audit trail before deletion
    $admin_email = $_SESSION['user_email'];
    $activity = "Deleted species ID: $species_id";
    $conn->query("INSERT INTO tbl_audit_trail (trail_username, trail_activity, trail_date) 
                  VALUES ('$admin_email', '$activity', NOW())");
    
    // Delete species
    $delete_query = "DELETE FROM tbl_species WHERE species_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $species_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Species deleted successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting species: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    $stmt->close();
    header("Location: species.php");
    exit();
}

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query based on search and filter
$query = "SELECT s.*, c.category_name, t.type_name 
          FROM tbl_species s
          JOIN tbl_category c ON s.category_id = c.category_id
          JOIN tbl_species_type t ON s.type_id = t.type_id
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (s.species_name LIKE '%$search%' OR s.scientific_name LIKE '%$search%')";
}

if (!empty($filter_category)) {
    $query .= " AND s.category_id = $filter_category";
}

$query .= " ORDER BY s.date_created DESC";

$result = $conn->query($query);

// Get all categories for filter dropdown
$categories = $conn->query("SELECT * FROM tbl_category ORDER BY category_name");

// Count total species
$total_species = $conn->query("SELECT COUNT(*) as count FROM tbl_species")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildAlert Admin - All Species</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/sidebar_style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        .content-container {
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            color: #054a29;
            margin: 0;
        }

        .action-button {
            background-color: #054a29;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .action-button:hover {
            background-color: #0a5236;
        }

        .action-button i {
            margin-right: 8px;
        }

        .species-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1;
        }

        .search-box {
            flex: 1;
            min-width: 200px;
        }

        .search-box input, .filter-select select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .filter-select {
            min-width: 180px;
        }

        .search-button {
            background-color: #0e6245;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-button:hover {
            background-color: #0a5236;
        }

        .total-count {
            background-color: #f0f0f0;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .total-count i {
            color: #054a29;
            margin-right: 8px;
        }

        .species-table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .species-table {
            width: 100%;
            border-collapse: collapse;
        }

        .species-table th,
        .species-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .species-table th {
            background-color: #f8f9fa;
            color: #054a29;
            font-weight: 600;
        }

        .species-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .species-image {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
        }

        .species-actions {
            display: flex;
            gap: 8px;
        }

        .edit-btn, .delete-btn, .view-btn {
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .edit-btn {
            background-color: #3498db;
        }

        .edit-btn:hover {
            background-color: #2980b9;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .view-btn {
            background-color: #2ecc71;
        }

        .view-btn:hover {
            background-color: #27ae60;
        }

        .empty-table {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .empty-table i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ddd;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            color: white;
        }

        .category-animalia { background-color: #3498db; }
        .category-fungi { background-color: #9b59b6; }
        .category-monera { background-color: #e67e22; }
        .category-protista { background-color: #f1c40f; }
        .category-plantae { background-color: #2ecc71; }

        /* Responsive table */
        @media (max-width: 992px) {
            .species-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .species-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-filter {
                width: 100%;
            }
            
            .total-count {
                width: 100%;
                justify-content: center;
            }
        }

        /* Delete Confirmation Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .modal-title {
            margin: 0;
            color: #054a29;
            font-size: 1.2rem;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        /* View Species Modal Styles */
        #viewModal .modal-content {
            width: 700px;
            max-width: 95%;
            margin: 5% auto;
        }

        .species-detail-header {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .species-detail-image {
            width: 200px;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .species-detail-info {
            flex: 1;
        }

        .species-detail-name {
            margin: 0 0 5px 0;
            color: #054a29;
            font-size: 1.5rem;
        }

        .species-detail-scientific {
            font-style: italic;
            color: #555;
            margin: 0 0 15px 0;
            font-size: 1.1rem;
        }

        .species-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 0.8rem;
            color: #777;
            margin-bottom: 3px;
        }

        .meta-value {
            font-weight: 500;
        }

        .species-description {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .species-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-item {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #054a29;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #555;
        }

        .conservation-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
            color: white;
            margin-top: 5px;
        }

        .status-lc { background-color: #2ecc71; } /* Least Concern */
        .status-nt { background-color: #3498db; } /* Near Threatened */
        .status-vu { background-color: #f1c40f; } /* Vulnerable */
        .status-en { background-color: #e67e22; } /* Endangered */
        .status-cr { background-color: #e74c3c; } /* Critically Endangered */
        .status-ew { background-color: #9b59b6; } /* Extinct in the Wild */
        .status-ex { background-color: #34495e; } /* Extinct */

        @media (max-width: 768px) {
            .species-detail-header {
                flex-direction: column;
            }

            .species-detail-image {
                width: 100%;
                height: auto;
                max-height: 250px;
            }

            .species-meta, .species-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container">
            <div class="page-header">
                <h1>All Species</h1>
                <a href="add_species.php" class="action-button">
                    <i class="fas fa-plus"></i> Add New Species
                </a>
            </div>

            <?php
            // Display session messages if any
            if (isset($_SESSION['message'])) {
                $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
                echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>

            <div class="species-controls">
                <form action="" method="GET" class="search-filter">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="filter-select">
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php echo ($filter_category == $category['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
                <div class="total-count">
                    <i class="fas fa-list"></i> Total Species: <?php echo $total_species; ?>
                </div>
            </div>

            <div class="species-table-container">
                <table class="species-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Species Name</th>
                            <th>Scientific Name</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($species = $result->fetch_assoc()) {
                                $category_class = "category-" . strtolower($species['category_name']);
                                
                                // Check if image exists on server
                                $image_path = $species['image_path'];
                                $default_image = "/images/default_species.jpg";
                                
                                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                                    $image_path = $default_image;
                                }
                                
                                // Prepare species data for view modal (as JSON)
                                $species_data = json_encode([
                                    'id' => $species['species_id'],
                                    'name' => $species['species_name'],
                                    'scientific_name' => $species['scientific_name'],
                                    'category' => $species['category_name'],
                                    'type' => $species['type_name'],
                                    'image' => $image_path,
                                    'date_created' => date('M d, Y', strtotime($species['date_created']))
                                ]);
                                
                                echo '<tr>
                                    <td><img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($species['species_name']) . '" class="species-image"></td>
                                    <td>' . htmlspecialchars($species['species_name']) . '</td>
                                    <td><em>' . htmlspecialchars($species['scientific_name']) . '</em></td>
                                    <td><span class="category-badge ' . $category_class . '">' . htmlspecialchars($species['category_name']) . '</span></td>
                                    <td>' . htmlspecialchars($species['type_name']) . '</td>
                                    <td>' . date('M d, Y', strtotime($species['date_created'])) . '</td>
                                    <td class="species-actions">
                                        <button class="view-btn" title="View Details" onclick=\'openViewModal(' . $species_data . ')\'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="edit_species.php?id=' . $species['species_id'] . '" class="edit-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="delete-btn" title="Delete" 
                                            onclick="openDeleteModal(' . $species['species_id'] . ', \'' . addslashes($species['species_name']) . '\')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>';
                            }
                        } else {
                            echo '<tr>
                                <td colspan="7" class="empty-table">
                                    <div>
                                    <i class="fas fa-leaf"></i>
                                    <p>No species found</p>
                                    </div>
                                </td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeDeleteModal()">&times;</span>
                <h2 class="modal-title">Confirm Deletion</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the species: <strong id="speciesNameToDelete"></strong>?</p>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    <input type="hidden" name="species_id" id="speciesIdToDelete">
                    <input type="hidden" name="delete_species" value="1">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn-delete">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Species Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeViewModal()">&times;</span>
                <h2 class="modal-title">Species Details</h2>
            </div>
            <div class="modal-body">
                <div class="species-detail-header">
                    <img id="viewSpeciesImage" src="" alt="Species Image" class="species-detail-image">
                    <div class="species-detail-info">
                        <h2 id="viewSpeciesName" class="species-detail-name"></h2>
                        <p id="viewSpeciesScientific" class="species-detail-scientific"></p>
                        
                        <div class="species-meta">
                            <div class="meta-item">
                                <span class="meta-label">Category</span>
                                <span id="viewSpeciesCategory" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Type</span>
                                <span id="viewSpeciesType" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Habitat</span>
                                <span id="viewSpeciesHabitat" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Date Added</span>
                                <span id="viewSpeciesDate" class="meta-value"></span>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Conservation Status</span>
                            <span id="viewSpeciesConservationStatusContainer"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeViewModal()">Close</button>
                <a id="editSpeciesLink" href="#" class="action-button">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <script>
        // Delete confirmation modal
        const deleteModal = document.getElementById('deleteModal');
        const viewModal = document.getElementById('viewModal');
        
        function openDeleteModal(speciesId, speciesName) {
            document.getElementById('speciesIdToDelete').value = speciesId;
            document.getElementById('speciesNameToDelete').textContent = speciesName;
            deleteModal.style.display = 'block';
        }
        
        function closeDeleteModal() {
            deleteModal.style.display = 'none';
        }
        
        // View species modal
        function openViewModal(species) {
            // Set values in the modal
            document.getElementById('viewSpeciesName').textContent = species.name;
            document.getElementById('viewSpeciesScientific').textContent = species.scientific_name;
            document.getElementById('viewSpeciesImage').src = species.image;
            document.getElementById('viewSpeciesCategory').textContent = species.category;
            document.getElementById('viewSpeciesType').textContent = species.type;
            document.getElementById('viewSpeciesHabitat').textContent = species.habitat || 'Not specified';
            document.getElementById('viewSpeciesDate').textContent = species.date_created;
            
            // Set edit link
            document.getElementById('editSpeciesLink').href = 'edit_species.php?id=' + species.id;
            
            // Set conservation status with proper styling
            const statusContainer = document.getElementById('viewSpeciesConservationStatusContainer');
            let statusClass = '';
            let statusText = species.conservation_status || 'Not Specified';
            
            switch(species.conservation_status) {
                case 'LC': 
                    statusClass = 'status-lc'; 
                    statusText = 'Least Concern (LC)';
                    break;
                case 'NT': 
                    statusClass = 'status-nt'; 
                    statusText = 'Near Threatened (NT)';
                    break;
                case 'VU': 
                    statusClass = 'status-vu'; 
                    statusText = 'Vulnerable (VU)';
                    break;
                case 'EN': 
                    statusClass = 'status-en'; 
                    statusText = 'Endangered (EN)';
                    break;
                case 'CR': 
                    statusClass = 'status-cr'; 
                    statusText = 'Critically Endangered (CR)';
                    break;
                case 'EW': 
                    statusClass = 'status-ew'; 
                    statusText = 'Extinct in the Wild (EW)';
                    break;
                case 'EX': 
                    statusClass = 'status-ex'; 
                    statusText = 'Extinct (EX)';
                    break;
            }
            
            statusContainer.innerHTML = `<span class="conservation-status ${statusClass}">${statusText}</span>`;
            
            // Display the modal
            viewModal.style.display = 'block';
        }
        
        function closeViewModal() {
            viewModal.style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
            if (event.target == viewModal) {
                closeViewModal();
            }
        }
        
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 1s';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 1000);
                }, 5000);
            });
        });
    </script>
</body>
</html>