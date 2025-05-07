<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message
    $_SESSION['login_required'] = "You must login to submit a wildlife report";
    header("Location: login.php");
    exit();
}

include '../backend/config/config.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $category_id = $_POST['category_id'];
    $reporter_name = $_POST['reporter_name'];
    $species_name = $_POST['species_name'];
    $location = $_POST['location'];
    $date_time = $_POST['date_time'];
    $status_id = $_POST['status_id'];
    $comments = $_POST['comments'] ?? null;
    // Use the user_id from session as acc_id
    $acc_id = $_SESSION['user_id'];

    // Handle file upload
    $target_dir = "../images/reported_images/";
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $image_path = '/images/reported_images/' . $new_filename;

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $error_message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (5MB max)
    if ($_FILES["image"]["size"] > 5000000) {
        $error_message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "webp"
    ) {
        $error_message = "Sorry, only JPG, JPEG, PNG, WEBP & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error_message = "Sorry, your file was not uploaded. " . $error_message;
        // if everything is ok, try to upload file
    } else {
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now insert into database
            $sql = "INSERT INTO tbl_reports (category_id, reporter_name, species_name, image_path, location, date_time, status_id, comments, acc_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Initial report status is set to 1 (Pending) by database default

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssisi", $category_id, $reporter_name, $species_name, $image_path, $location, $date_time, $status_id, $comments, $acc_id);

            if ($stmt->execute()) {
                $success_message = "Report submitted successfully!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }
}

// Get categories for dropdown
$sql_categories = "SELECT * FROM tbl_category";
$result_categories = $conn->query($sql_categories);

// Get species types for dropdown
$sql_types = "SELECT * FROM tbl_species_type";
$result_types = $conn->query($sql_types);

// Get status options
$sql_status = "SELECT * FROM tbl_status";
$result_status = $conn->query($sql_status);

// Get existing species for suggestions
$sql_species = "SELECT species_name FROM tbl_species";
$result_species = $conn->query($sql_species);
$species_list = [];
if ($result_species->num_rows > 0) {
    while ($row = $result_species->fetch_assoc()) {
        $species_list[] = $row['species_name'];
    }
}
$species_json = json_encode($species_list);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Wildlife Report - WildAlert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .report-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            display: none;
        }

        .custom-file-upload {
            border: 1px solid #ccc;
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        .section-title {
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        .btn-submit {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-submit:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .custom-tooltip {
            position: relative;
            display: inline-block;
            margin-left: 5px;
        }

        .custom-tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .custom-tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4 section-title">Submit Wildlife Report</h1>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-warning" role="alert">
                        <strong>Please log in to submit a report.</strong>
                        <a href="login.php" class="alert-link">Click here to login</a>
                    </div>
                <?php endif; ?>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="report-form">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" <?php if (!isset($_SESSION['user_id'])): ?> class="d-none" <?php endif; ?>>
                        <div class="mb-4">
                            <h4 class="mb-3 text-success"><i class="fas fa-info-circle"></i> Basic Information</h4>

                            <div class="mb-3">
                                <label for="reporter_name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="reporter_name" name="reporter_name" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category
                                        <div class="custom-tooltip">
                                            <i class="fas fa-question-circle text-muted"></i>
                                            <span class="tooltip-text">Select the biological category of the species</span>
                                        </div>
                                    </label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>Choose category</option>
                                        <?php
                                        if ($result_categories->num_rows > 0) {
                                            while ($row = $result_categories->fetch_assoc()) {
                                                echo "<option value='" . $row["category_id"] . "'>" . $row["category_name"] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status_id" class="form-label">Status
                                        <div class="custom-tooltip">
                                            <i class="fas fa-question-circle text-muted"></i>
                                            <span class="tooltip-text">Current condition of the observed species</span>
                                        </div>
                                    </label>
                                    <select class="form-select" id="status_id" name="status_id" required>
                                        <option value="" selected disabled>Choose status</option>
                                        <?php
                                        if ($result_status->num_rows > 0) {
                                            while ($row = $result_status->fetch_assoc()) {
                                                echo "<option value='" . $row["status_id"] . "'>" . $row["status_name"] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="species_name" class="form-label">Species Name
                                    <div class="custom-tooltip">
                                        <i class="fas fa-question-circle text-muted"></i>
                                        <span class="tooltip-text">Common or scientific name of the species</span>
                                    </div>
                                </label>
                                <input type="text" class="form-control" id="species_name" name="species_name" list="speciesList" required>
                                <datalist id="speciesList">
                                    <?php foreach ($species_list as $species): ?>
                                        <option value="<?php echo $species; ?>">
                                        <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="mb-3 text-success"><i class="fas fa-map-marker-alt"></i> Location & Time</h4>

                            <div class="mb-3">
                                <label for="location" class="form-label">Location
                                    <div class="custom-tooltip">
                                        <i class="fas fa-question-circle text-muted"></i>
                                        <span class="tooltip-text">Specific location where you observed the species</span>
                                    </div>
                                </label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Mt. Apo, Davao City, Philippines" required>
                            </div>

                            <div class="mb-3">
                                <label for="date_time" class="form-label">Date & Time of Observation</label>
                                <input type="text" class="form-control" id="date_time" name="date_time" placeholder="YYYY-MM-DD HH:MM:SS" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="mb-3 text-success"><i class="fas fa-camera"></i> Photo Upload</h4>

                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image
                                    <div class="custom-tooltip">
                                        <i class="fas fa-question-circle text-muted"></i>
                                        <span class="tooltip-text">Clear photo of the species (max 5MB)</span>
                                    </div>
                                </label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)" required>
                                </div>
                                <small class="text-muted">Supported formats: JPG, JPEG, PNG, GIF, WEBP (max 5MB)</small>
                                <div class="mt-2">
                                    <img id="imagePreview" class="preview-image" src="#" alt="Image Preview">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="mb-3 text-success"><i class="fas fa-comment"></i> Additional Information</h4>

                            <div class="mb-3">
                                <label for="comments" class="form-label">Comments
                                    <div class="custom-tooltip">
                                        <i class="fas fa-question-circle text-muted"></i>
                                        <span class="tooltip-text">Any additional observations or details you'd like to share</span>
                                    </div>
                                </label>
                                <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Describe behavior, condition, or any other relevant information..."></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-submit btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i> Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize date-time picker
        flatpickr("#date_time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            defaultDate: new Date(),
            maxDate: new Date()
        });

        // Image preview function
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>

</html>