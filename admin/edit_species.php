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


// Check if species ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "No species selected for editing";
    $_SESSION['message_type'] = "danger";
    header("Location: species.php");
    exit();
}

$species_id = $_GET['id'];

// Get species data
$query = "SELECT * FROM tbl_species WHERE species_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $species_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Species not found";
    $_SESSION['message_type'] = "danger";
    header("Location: species.php");
    exit();
}

$species = $result->fetch_assoc();
$stmt->close();

// Get all categories for dropdown
$categories = $conn->query("SELECT * FROM tbl_category ORDER BY category_name");

// Get all species types for dropdown
$species_types = $conn->query("SELECT * FROM tbl_species_type ORDER BY type_name");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $species_name = trim($_POST['species_name']);
    $scientific_name = trim($_POST['scientific_name']);
    $kingdom = trim($_POST['kingdom']);
    $group_name = trim($_POST['group_name']);
    $category_id = $_POST['category_id'];
    $type_id = $_POST['type_id'];
    
    $errors = array();
    
    if (empty($species_name)) {
        $errors[] = "Species name is required";
    }
    
    if (empty($scientific_name)) {
        $errors[] = "Scientific name is required";
    }
    
    if (empty($kingdom)) {
        $errors[] = "Kingdom is required";
    }
    
    if (empty($group_name)) {
        $errors[] = "Group name is required";
    }
    
    // If no errors, proceed with update
    if (empty($errors)) {
        // Check if new image was uploaded
        $image_path = $species['image_path']; // Default to current image
        
        if (isset($_FILES['species_image']) && $_FILES['species_image']['error'] == 0) {
            $allowed_types = array('jpg', 'jpeg', 'png', 'webp', 'avif');
            $file_name = $_FILES['species_image']['name'];
            $file_size = $_FILES['species_image']['size'];
            $file_tmp = $_FILES['species_image']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($file_ext, $allowed_types)) {
                $errors[] = "Only JPG, JPEG, PNG, WEBP and AVIF files are allowed";
            }
            
            // Validate file size (5MB max)
            if ($file_size > 5000000) {
                $errors[] = "File size must be less than 5MB";
            }
            
            if (empty($errors)) {
                // Create unique filename
                $new_file_name = uniqid() . '.' . $file_ext;
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/species/';
                $upload_path = '/images/species/' . $new_file_name;
                
                // Ensure directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    // Delete old image if it exists and is not the default
                    if ($image_path != '/images/default_species.jpg' && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $image_path);
                    }
                    
                    $image_path = $upload_path;
                } else {
                    $errors[] = "Failed to upload image";
                }
            }
        }
        
        if (empty($errors)) {
            // Update species in database
            $update_query = "UPDATE tbl_species SET 
                            species_name = ?, 
                            scientific_name = ?, 
                            image_path = ?, 
                            kingdom = ?, 
                            group_name = ?, 
                            category_id = ?, 
                            type_id = ? 
                            WHERE species_id = ?";
                            
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssiis", $species_name, $scientific_name, $image_path, $kingdom, $group_name, $category_id, $type_id, $species_id);
            
            if ($stmt->execute()) {
                // Record audit trail
                $admin_email = $_SESSION['user_email'];
                $activity = "Updated species: $species_name (ID: $species_id)";
                $conn->query("INSERT INTO tbl_audit_trail (trail_username, trail_activity, trail_date) 
                              VALUES ('$admin_email', '$activity', NOW())");
                
                $_SESSION['message'] = "Species updated successfully";
                $_SESSION['message_type'] = "success";
                header("Location: species.php");
                exit();
            } else {
                $errors[] = "Error updating species: " . $conn->error;
            }
            
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildAlert Admin - Edit Species</title>
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

        .back-button {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #5a6268;
        }

        .back-button i {
            margin-right: 8px;
        }

        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .form-title {
            color: #054a29;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }


        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #054a29;
            outline: none;
        }

        .full-width {
            grid-column: span 2;
        }

        .image-preview-container {
            grid-column: span 2;
            margin-bottom: 20px;
        }

        .image-preview {
            max-width: 350px;
            max-height: 350px;
            border-radius: 5px;
            border: 1px solid #ddd;
            display: block;
            margin-top: 10px;
        }

        .btn-container {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-save {
            background-color: #054a29;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background-color 0.3s;
        }

        .btn-save:hover {
            background-color: #0a5236;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
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

        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container">
            <div class="page-header">
                <h1>Edit Species</h1>
                <a href="species.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to All Species
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <h2 class="form-title">Edit Species Information</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="species_name">Species Name</label>
                            <input type="text" id="species_name" name="species_name" class="form-control" value="<?php echo htmlspecialchars($species['species_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="scientific_name">Scientific Name</label>
                            <input type="text" id="scientific_name" name="scientific_name" class="form-control" value="<?php echo htmlspecialchars($species['scientific_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="kingdom">Kingdom</label>
                            <input type="text" id="kingdom" name="kingdom" class="form-control" value="<?php echo htmlspecialchars($species['kingdom']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="group_name">Group Name</label>
                            <input type="text" id="group_name" name="group_name" class="form-control" value="<?php echo htmlspecialchars($species['group_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php while ($category = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($species['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type_id">Species Type</label>
                            <select id="type_id" name="type_id" class="form-control" required>
                                <option value="">Select Type</option>
                                <?php while ($type = $species_types->fetch_assoc()): ?>
                                    <option value="<?php echo $type['type_id']; ?>" <?php echo ($species['type_id'] == $type['type_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['type_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group image-preview-container">
                            <label for="species_image">Species Image</label>
                            <input type="file" id="species_image" name="species_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/avif">
                            <p style="margin-top: 5px; color: #666; font-size: 0.85rem;">
                                Leave empty to keep current image. Accepted formats: JPG, JPEG, PNG, WEBP, AVIF.
                            </p>
                            <?php 
                                $image_path = $species['image_path'];
                                $default_image = "../images/default_species.jpg";

                                // If the path has /images/species/ or \images\species\ in it, extract just the filename
                                if (strpos($image_path, '/images/species/') !== false || strpos($image_path, '\\images\\species\\') !== false) {
                                    $image_path = basename($image_path);
                                }

                                // Use only the filename, don't check file existence here
                                // This avoids path separator issues on Windows
                                if (empty($image_path)) {
                                    $image_path = basename($default_image); // Use default image filename
                                }
                            ?>
                            <img src="/images/species/<?php echo htmlspecialchars($image_path); ?>" alt="Current species image" class="image-preview" id="currentImagePreview">
                        </div>

                        <div class="btn-container">
                            <a href="species.php" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-save">Update Species</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('species_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file type
                const fileType = file.type;
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/avif'];
                
                if (!validTypes.includes(fileType)) {
                    alert('Invalid file type. Please upload a JPG, JPEG, PNG, WEBP or AVIF image.');
                    e.target.value = '';
                    return;
                }
                
                // Check file size (5MB max)
                if (file.size > 5000000) {
                    alert('File is too large. Maximum size is 5MB.');
                    e.target.value = '';
                    return;
                }
                
                // Update preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('currentImagePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const speciesName = document.getElementById('species_name').value.trim();
            const scientificName = document.getElementById('scientific_name').value.trim();
            const kingdom = document.getElementById('kingdom').value.trim();
            const groupName = document.getElementById('group_name').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const typeId = document.getElementById('type_id').value;
            
            let hasError = false;
            
            // Clear previous error messages
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.remove());
            
            // Validate species name
            if (speciesName === '') {
                displayError('species_name', 'Species name is required');
                hasError = true;
            }
            
            // Validate scientific name
            if (scientificName === '') {
                displayError('scientific_name', 'Scientific name is required');
                hasError = true;
            }
            
            // Validate kingdom
            if (kingdom === '') {
                displayError('kingdom', 'Kingdom is required');
                hasError = true;
            }
            
            // Validate group name
            if (groupName === '') {
                displayError('group_name', 'Group name is required');
                hasError = true;
            }
            
            // Validate category
            if (categoryId === '') {
                displayError('category_id', 'Please select a category');
                hasError = true;
            }
            
            // Validate type
            if (typeId === '') {
                displayError('type_id', 'Please select a species type');
                hasError = true;
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
        
        function displayError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
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