<?php 
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include '../backend/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Upload Image</title>
    <style>
       
        .upload-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2rem;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }
        h1:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: rgb(31, 164, 118);
            border-radius: 2px;
        }
        .upload-area {
            border: 2px dashed rgb(31, 164, 118);
            border-radius: 10px;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: rgba(31, 164, 118, 0.05);
        }
        .upload-area:hover {
            background-color: rgba(31, 164, 118, 0.1);
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(31, 164, 118, 0.15);
        }
        .upload-area.highlight {
            background-color: rgba(31, 164, 118, 0.15);
            border-color: rgb(25, 140, 100);
            box-shadow: 0 0 0 3px rgba(31, 164, 118, 0.2);
        }
        .upload-area i {
            color: rgb(31, 164, 118);
            margin-bottom: 15px;
        }
        .upload-area p {
            color: #555;
            font-size: 16px;
            margin: 8px 0;
        }
        #fileInput {
            display: none;
        }
        .btn {
            background-color: rgb(31, 164, 118);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(31, 164, 118, 0.2);
        }
        .btn:hover {
            background-color: rgb(25, 140, 100);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(31, 164, 118, 0.3);
        }
        .btn:disabled {
            background-color: #a0a0a0;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }
        .preview-area {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #imagePreview {
            max-width: 100%;
            max-height: 350px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            display: none;
            border: 3px solid rgb(31, 164, 118);
            transition: all 0.3s ease;
        }
        #imagePreview:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .form-group {
            margin-bottom: 15px;
            width: 100%;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .error-message {
            color: white;
            background-color: #e74c3c;
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            display: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .success-message {
            color: white;
            background-color: #27ae60;
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            display: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .loading {
            display: none;
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            border-radius: 10px;
            background-color: rgba(31, 164, 118, 0.05);
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid rgb(31, 164, 118);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
            box-shadow: 0 0 10px rgba(31, 164, 118, 0.2);
        }
        .loading p {
            color: #555;
            font-size: 16px;
            font-weight: 500;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="upload-container">
        <h1>Species Identifier</h1>
        
        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>
        
        <div class="upload-area" id="uploadArea">
            <p><i class="fas fa-cloud-upload-alt fa-3x"></i></p>
            <p>Drag and drop an image here or click to select</p>
            <p style="font-size: 14px; color: #777;">Supported formats: JPG, PNG, GIF</p>
            <input type="file" id="fileInput" accept="image/*" />
            <button type="button" class="btn" id="uploadBtn">Select Image</button>
        </div>
        
        <div class="preview-area">
            <img id="imagePreview" alt="Preview" />
            <button class="btn" id="identifyBtn" disabled>
                <i class="fas fa-search"></i> Identify Species
            </button>
        </div>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Analyzing image...</p>
        </div>
        
        <div class="result-container" id="resultContainer">
            <h3><i class="fas fa-leaf"></i> Identified Species:</h3>
            <div id="speciesResult"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const uploadBtn = document.getElementById('uploadBtn');
            const imagePreview = document.getElementById('imagePreview');
            const identifyBtn = document.getElementById('identifyBtn');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            const resultContainer = document.getElementById('resultContainer');
            const speciesResult = document.getElementById('speciesResult');
            
            // Handle drag and drop events
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('highlight');
            });
            
            uploadArea.addEventListener('dragleave', function() {
                uploadArea.classList.remove('highlight');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('highlight');
                
                const files = e.dataTransfer.files;
                if (files.length > 0 && files[0].type.match('image.*')) {
                    handleFileSelect(files[0]);
                } else {
                    showError('Please select a valid image file.');
                }
            });
            
            // Handle file selection via button
            uploadBtn.addEventListener('click', function() {
                fileInput.click();
            });
            
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    handleFileSelect(fileInput.files[0]);
                }
            });
            
            // Handle the selected file
            function handleFileSelect(file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    identifyBtn.disabled = false;
                    
                    // Hide previous messages
                    errorMessage.style.display = 'none';
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }
                };
                
                reader.readAsDataURL(file);
            }
            
            // API URL for the Flask backend
            const API_URL = 'http://localhost:8800/identify';
            
            identifyBtn.addEventListener('click', function() {
                if (!imagePreview.src) {
                    showError('Please select an image first.');
                    return;
                }
                
                loading.style.display = 'block';
                errorMessage.style.display = 'none';
                resultContainer.style.display = 'none';
                identifyBtn.disabled = true;
                
                // Send image to backend Flask server
                identifySpecies(imagePreview.src);
            });
            
            function identifySpecies(imageData) {
                // Show loading animation
                loading.style.display = 'block';
                
                // For better UX, scroll to the loading indicator
                loading.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Send request to Flask backend
                fetch(API_URL, { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ image: imageData })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || `API request failed with status ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Process the response
                    loading.style.display = 'none';
                    identifyBtn.disabled = false;
                    
                    if (data.result) {
                        displayResult(data.result);
                        showSuccess('Species successfully identified!');
                    } else {
                        showError('No results found in the API response.');
                    }
                })
                .catch(error => {
                    loading.style.display = 'none';
                    identifyBtn.disabled = false;
                    
                    if (error.message.includes("Failed to fetch")) {
                        showError("Could not connect to the server. Please ensure the Flask backend is running at http://localhost:8800");
                    } else {
                        showError(`Error: ${error.message}`);
                    }
                    console.error('API request error:', error);
                });
            }
            
            function displayResult(result) {
                // Display the response from the API
                speciesResult.innerHTML = result;
                resultContainer.style.display = 'block';
                
                // Scroll to the result container with animation
                setTimeout(() => {
                    resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
                
                // Add a subtle fade-in effect
                resultContainer.style.opacity = '0';
                resultContainer.style.transition = 'opacity 0.5s ease-in-out';
                
                setTimeout(() => {
                    resultContainer.style.opacity = '1';
                }, 100);
            }
            
            function showError(message) {
                // Create the error message with an icon
                errorMessage.innerHTML = `<i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>${message}`;
                errorMessage.style.display = 'block';
                
                // Scroll to the error message
                errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Auto hide after 7 seconds
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 7000);
            }
            
            function showSuccess(message) {
                if (successMessage) {
                    // Create the success message with an icon
                    successMessage.innerHTML = `<i class="fas fa-check-circle" style="margin-right: 8px;"></i>${message}`;
                    successMessage.style.display = 'block';
                    
                    // Scroll to the success message
                    successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Auto hide after 5 seconds
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 5000);
                }
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>