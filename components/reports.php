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
    $status_name = $_POST['status_name'];
    $comments = $_POST['comments'] ?? null;
    // Use the user_id from session as acc_id
    $acc_id = $_SESSION['user_id'];

    // Initialize variables
    $uploadOk = 1;
    $error_message = "";
    $success_message = "";
    $image_path = "";

    // Check if image file was actually uploaded
    if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
        // Handle file upload
        $target_dir = "../images/reported_images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        $image_path = '/images/reported_images/' . $new_filename;
        $imageFileType = strtolower($file_extension);

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $error_message = "File is not an image.";
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
        } else {
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File uploaded successfully, continue with database insertion
                $uploadOk = 1;
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    } else {
        $error_message = "No image file was uploaded.";
        $uploadOk = 0;
    }

    // If image upload was successful or no image was required, insert into database
    if ($uploadOk == 1) {
        $sql = "INSERT INTO tbl_reports (category_id, reporter_name, species_name, image_path, location, date_time, status_name, comments, acc_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssi", $category_id, $reporter_name, $species_name, $image_path, $location, $date_time, $status_name, $comments, $acc_id);

        if ($stmt->execute()) {
            $success_message = "Report submitted successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Get categories for dropdown
$sql_categories = "SELECT * FROM tbl_category";
$result_categories = $conn->query($sql_categories);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <style>
        .report-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: border 0.3s ease;
            border: 2px solid transparent;
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

        .btn-refresh {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-refresh:hover {
            background-color: #5a6268;
            border-color: #545b62;
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

        #identification-results {
            display: none;
            background-color: #e8f5e9;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }

        .result-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .accuracy-chart-container {
            height: 200px;
            margin-top: 15px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .ai-badge {
            background-color: #6c757d;
            color: white;
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 4px;
            margin-left: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        /* Success Modal Styles */
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            display: block;
            margin: 0 auto 20px;
        }

        .modal-content-success {
            border-top: 5px solid #28a745;
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

                <?php if (isset($error_message) && !empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="report-form">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="reportForm" <?php if (!isset($_SESSION['user_id'])): ?> class="d-none" <?php endif; ?>>
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
                                    <label for="status_name" class="form-label">Status
                                        <div class="custom-tooltip">
                                            <i class="fas fa-question-circle text-muted"></i>
                                            <span class="tooltip-text">Current condition of the observed species</span>
                                        </div>
                                        <span class="ai-badge"><i class="fas fa-robot"></i> System Detected</span>
                                    </label>
                                    <input type="text" class="form-control" id="status_name" name="status_name" readonly
                                        placeholder="Automatically detected status">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="species_name" class="form-label">Species Name
                                    <div class="custom-tooltip">
                                        <i class="fas fa-question-circle text-muted"></i>
                                        <span class="tooltip-text">Common or scientific name of the species</span>
                                    </div>
                                    <span class="ai-badge"><i class="fas fa-robot"></i> System Detected</span>
                                </label>
                                <input type="text" class="form-control" id="species_name" name="species_name" readonly
                                    placeholder="Automatically detected species name">
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
                                    <span class="ai-badge"><i class="fas fa-robot"></i> System Assisted</span>
                                </label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Mt. Apo, Davao City, Philippines" required readonly>
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
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="handleImageUpload(this)" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearFileInput()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Supported formats: JPG, JPEG, PNG, GIF, WEBP (max 5MB)</small>
                                <div class="mt-2">
                                    <img id="imagePreview" class="preview-image" src="#" alt="Image Preview">
                                </div>

                                <div class="loading-spinner" id="loadingSpinner">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Analyzing image using XGBOOST and VIT...</p>
                                </div>
                            </div>
                        </div>
                        <div id="identification-results">
                            <h4 class="mb-3 text-success"><i class="fas fa-microscope"></i> XGBOOST Identification Results</h4>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="result-card">
                                        <h5>Species Identification</h5>
                                        <div class="d-flex align-items-center">
                                            <p id="identifiedSpecies" class="mb-0 me-2">Not identified yet</p>
                                            <span id="speciesAccuracy" class="badge bg-success">0%</span>
                                        </div>
                                        <div class="accuracy-chart-container">
                                            <canvas id="speciesChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="result-card">
                                        <h5>Status Assessment</h5>
                                        <div class="d-flex align-items-center">
                                            <p id="identifiedStatus" class="mb-0 me-2">Not assessed yet</p>
                                            <span id="statusAccuracy" class="badge bg-success">0%</span>
                                        </div>
                                        <div class="accuracy-chart-container">
                                            <canvas id="statusChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="result-card">
                                        <h5>Location Detection</h5>
                                        <div class="d-flex align-items-center">
                                            <p id="identifiedLocation" class="mb-0 me-2">Not detected yet</p>
                                            <span id="locationAccuracy" class="badge bg-success">0%</span>
                                        </div>
                                        <div class="accuracy-chart-container">
                                            <canvas id="locationChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="result-card mt-3">
                                <h5>Identified Features</h5>
                                <div id="identifiedFeatures" class="mt-2">
                                    <span class="badge bg-light text-dark me-2">No features identified yet</span>
                                </div>
                            </div> -->
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

                        <div class="form-actions">
                            <button type="submit" class="btn btn-submit btn-lg px-4" id="submitButton" disabled>
                                <i class="fas fa-paper-plane me-2"></i> Submit Report
                            </button>
                            <button type="button" class="btn btn-refresh btn-lg px-4" id="refreshButton" onclick="resetForm()">
                                <i class="fas fa-sync-alt me-2"></i> Upload New Image
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-success">
                <div class="modal-body text-center p-5">
                    <i class="fas fa-check-circle success-icon"></i>
                    <h3 class="mb-4">Report Submitted Successfully!</h3>
                    <p class="mb-4">Thank you for contributing to wildlife conservation. Your report has been successfully submitted and will be reviewed by our team.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="reports_history.php" class="btn btn-outline-success">View My Reports</a>
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="resetAfterSubmit()">Submit Another Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Check if form was submitted successfully and show modal
        <?php if (isset($success_message) && !empty($success_message)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        <?php endif; ?>

        // Initialize date-time picker
        flatpickr("#date_time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            defaultDate: new Date(),
            maxDate: new Date()
        });

        // Charts for displaying confidence levels
        let speciesChart = null;
        let statusChart = null;
        let locationChart = null;

        function handleImageUpload(input) {
            const preview = document.getElementById('imagePreview');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const resultsSection = document.getElementById('identification-results');
            const submitButton = document.getElementById('submitButton');
            const fileInput = document.getElementById('image');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Display image preview
                    preview.src = e.target.result;
                    preview.style.display = 'block';

                    // Show loading spinner
                    loadingSpinner.style.display = 'block';

                    // Send image to AI for identification
                    identifySpecies(e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                resultsSection.style.display = 'none';
                submitButton.disabled = true;
            }
        }


        // Send image to AI for species identification
        function identifySpecies(imageData) {
            // Make API call to the Python backend
            fetch('http://localhost:8800/api/identify-species', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        image: imageData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading spinner
                    document.getElementById('loadingSpinner').style.display = 'none';

                    // Process and display results
                    if (data.error) {
                        alert('Error identifying species: ' + data.error);
                    } else {
                        displayIdentificationResults(data);
                    }
                })
                .catch(error => {
                    document.getElementById('loadingSpinner').style.display = 'none';
                    console.error('Error:', error);
                    alert('Failed to process image. Please try again.');
                });
        }

        // Display identification results
        function displayIdentificationResults(data) {
            document.getElementById('submitButton').disabled = false;

            const fileInput = document.getElementById('image');
            fileInput.classList.add('has-analysis');

            // Add a click interceptor to file input
            fileInput.addEventListener('click', preventFileChange);

            // Highlight the refresh button to indicate it's the way to upload a new image
            document.getElementById('refreshButton').classList.remove('btn-refresh');
            document.getElementById('refreshButton').classList.add('btn-primary');

            // Show results section
            const resultsSection = document.getElementById('identification-results');
            resultsSection.style.display = 'block';

            // Update species name and status in form fields
            document.getElementById('species_name').value = data.species;
            document.getElementById('status_name').value = data.status;

            // Update location field if confidence is high enough (above 60%)
            if (data.location && data.locationConfidence > 60) {
                document.getElementById('location').value = data.location;
            }

            // Update display elements
            document.getElementById('identifiedSpecies').textContent = data.species;
            document.getElementById('identifiedStatus').textContent = data.status;
            document.getElementById('identifiedLocation').textContent = data.location || 'Unknown';

            // Update accuracy percentage displays
            document.getElementById('speciesAccuracy').textContent = Math.round(data.speciesConfidence) + '%';
            document.getElementById('statusAccuracy').textContent = Math.round(data.statusConfidence) + '%';
            document.getElementById('locationAccuracy').textContent = Math.round(data.locationConfidence || 0) + '%';

            // Set badge colors based on confidence
            setBadgeColor('speciesAccuracy', data.speciesConfidence);
            setBadgeColor('statusAccuracy', data.statusConfidence);
            setBadgeColor('locationAccuracy', data.locationConfidence || 0);

            // Create/update species confidence chart
            createOrUpdateChart('speciesChart', speciesChart,
                ['Identified Species', 'Other Possibilities'],
                [data.speciesConfidence, 100 - data.speciesConfidence],
                ['#28a745', '#e9ecef']
            );


            // Create/update status confidence chart
            createOrUpdateChart('statusChart', statusChart,
                ['Identified Status', 'Confidence Margin'],
                [data.statusConfidence, 100 - data.statusConfidence],
                ['#17a2b8', '#e9ecef']
            );

            // Create/update location confidence chart
            createOrUpdateChart('locationChart', locationChart,
                ['Identified Location', 'Confidence Margin'],
                [data.locationConfidence || 0, 100 - (data.locationConfidence || 0)],
                ['#fd7e14', '#e9ecef']
            );

            // Display features if available
            // if (data.features && data.features.length > 0) {
            //     const featuresElement = document.getElementById('identifiedFeatures');
            //     if (featuresElement) {
            //         featuresElement.innerHTML = '';
            //         data.features.forEach(feature => {
            //             const badge = document.createElement('span');
            //             badge.className = 'badge bg-light text-dark me-2 mb-2';
            //             badge.textContent = feature;
            //             featuresElement.appendChild(badge);
            //         });
            //     }
            // }
        }

        function preventFileChange(e) {
            if (e.currentTarget.classList.contains('has-analysis')) {
                e.preventDefault();
                e.stopPropagation();

                // Show a message to user
                alert('Please use the "Upload New Image" button to analyze a different image.');

                return false;
            }
        }

        // Set badge color based on confidence percentage
        function setBadgeColor(elementId, confidence) {
            const badge = document.getElementById(elementId);
            if (confidence >= 85) {
                badge.className = 'badge bg-success';
            } else if (confidence >= 70) {
                badge.className = 'badge bg-info';
            } else if (confidence >= 50) {
                badge.className = 'badge bg-warning text-dark';
            } else {
                badge.className = 'badge bg-danger';
            }
        }

        // Clear file input without resetting the entire form
        function clearFileInput() {
            const fileInput = document.getElementById('image');
            fileInput.value = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('submitButton').disabled = true;
        }

        // Create or update chart
        function createOrUpdateChart(canvasId, chartInstance, labels, data, bgColors) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            if (chartInstance) {
                chartInstance.data.datasets[0].data = data;
                chartInstance.update();
                return chartInstance;
            }

            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}%`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // Reset form to upload a new image
        function resetForm() {
            window.location.reload();
        }

        // Reset after successful submission
        function resetAfterSubmit() {
            window.location.reload();
        }
    </script>
</body>

</html>