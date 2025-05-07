
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
    <title>Wildlife 3D Models Gallery</title>
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--primary-color);
            line-height: 1.6;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .page-title {
            margin: 2rem 0;
            text-align: center;
        }
        
        .model-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .model-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .model-card:hover {
            transform: translateY(-5px);
        }
        
        .model-viewer-container {
            height: 300px;
            background-color: #f0f0f0;
            position: relative;
        }
        
        model-viewer {
            width: 100%;
            height: 100%;
            --poster-color: transparent;
        }
        
        .model-info {
            padding: 1.5rem;
        }
        
        .model-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .model-description {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .fullscreen-button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        
        .fullscreen-button:hover {
            background-color: #2980b9;
        }
        
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: var(--accent-color);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        
        /* Full Screen Modal */
        .fullscreen-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            overflow: hidden;
        }
        
        .fullscreen-content {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .fullscreen-model-viewer {
            width: 90%;
            height: 90%;
        }
        
        .close-button {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 30px;
            color: white;
            cursor: pointer;
            z-index: 1001;
        }
        
        /* Status Message */
        .status-message {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #7f8c8d;
            text-align: center;
            display: none;
        }
        
        @media (max-width: 768px) {
            .model-gallery {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
            }
            
            .model-viewer-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1 class="page-title">Wildlife 3D Models Gallery</h1>
        <p style="text-align: center; margin-bottom: 2rem;">Explore our collection of detailed wildlife 3D models. Rotate, zoom, and view in full screen mode.</p>
        
        <div class="model-gallery">
            <!-- Bald Eagle Model -->
            <div class="model-card">
                <div class="model-viewer-container">
                    <div class="loading-spinner"></div>
                    <model-viewer
                        id="eagle-model"
                        src="../images/models_3d/bald_eagle.glb"
                        alt="A 3D model of a bald eagle"
                        auto-rotate
                        camera-controls
                        shadow-intensity="1"
                        camera-orbit="0deg 75deg 2m"
                        exposure="0.8"
                    ></model-viewer>
                </div>
                <div class="model-info">
                    <h3 class="model-title">Bald Eagle</h3>
                    <p class="model-description">The majestic national bird of the United States, known for its distinctive white head and powerful wingspan.</p>
                    <div class="controls">
                        <span>Rotate model to view details</span>
                        <button class="fullscreen-button" data-model="eagle-model">View Full Screen</button>
                    </div>
                    <div class="status-message" id="eagle-model-status"></div>
                </div>
            </div>
            
            <!-- Marine Toad Model -->
            <div class="model-card">
                <div class="model-viewer-container">
                    <div class="loading-spinner"></div>
                    <model-viewer
                        id="toad-model"
                        src="../images/models_3d/model_6_-_marine_toad_on_leaf.glb"
                        alt="A 3D model of a marine toad on a leaf"
                        auto-rotate
                        camera-controls
                        shadow-intensity="1"
                        camera-orbit="0deg 65deg 2m"
                        exposure="0.8"
                    ></model-viewer>
                </div>
                <div class="model-info">
                    <h3 class="model-title">Marine Toad</h3>
                    <p class="model-description">Also known as the cane toad, this amphibian is one of the largest toad species found in tropical environments.</p>
                    <div class="controls">
                        <span>Rotate model to view details</span>
                        <button class="fullscreen-button" data-model="toad-model">View Full Screen</button>
                    </div>
                    <div class="status-message" id="toad-model-status"></div>
                </div>
            </div>
            
            <!-- Nile Crocodile Model -->
            <div class="model-card">
                <div class="model-viewer-container">
                    <div class="loading-spinner"></div>
                    <model-viewer
                        id="crocodile-model"
                        src="../images/models_3d/nile_crocodile_swimming.glb"
                        alt="A 3D model of a Nile crocodile swimming"
                        auto-rotate
                        camera-controls
                        shadow-intensity="1"
                        camera-orbit="0deg 75deg 2.5m"
                        exposure="0.8"
                    ></model-viewer>
                </div>
                <div class="model-info">
                    <h3 class="model-title">Nile Crocodile</h3>
                    <p class="model-description">One of Africa's most fearsome predators, capable of growing up to 20 feet in length and weighing over 1,600 pounds.</p>
                    <div class="controls">
                        <span>Rotate model to view details</span>
                        <button class="fullscreen-button" data-model="crocodile-model">View Full Screen</button>
                    </div>
                    <div class="status-message" id="crocodile-model-status"></div>
                </div>
            </div>
            
            <!-- Parrot Model -->
            <div class="model-card">
                <div class="model-viewer-container">
                    <div class="loading-spinner"></div>
                    <model-viewer
                        id="parrot-model"
                        src="../images/models_3d/parrot.glb"
                        alt="A 3D model of a colorful parrot"
                        auto-rotate
                        camera-controls
                        shadow-intensity="1"
                        camera-orbit="0deg 75deg 2m"
                        exposure="0.8"
                    ></model-viewer>
                </div>
                <div class="model-info">
                    <h3 class="model-title">Colorful Parrot</h3>
                    <p class="model-description">Known for their vibrant plumage and ability to mimic human speech, parrots are among the most intelligent bird species.</p>
                    <div class="controls">
                        <span>Rotate model to view details</span>
                        <button class="fullscreen-button" data-model="parrot-model">View Full Screen</button>
                    </div>
                    <div class="status-message" id="parrot-model-status"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Full Screen Modal -->
    <div id="fullscreen-modal" class="fullscreen-modal">
        <span class="close-button" id="close-modal">&times;</span>
        <div class="fullscreen-content">
            <model-viewer
                id="fullscreen-viewer"
                camera-controls
                shadow-intensity="1"
                auto-rotate
                exposure="0.8"
            ></model-viewer>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get modal elements
            const modal = document.getElementById('fullscreen-modal');
            const fullscreenViewer = document.getElementById('fullscreen-viewer');
            const closeButton = document.getElementById('close-modal');
            
            // Hide loading spinners when models are loaded
            document.querySelectorAll('model-viewer').forEach(viewer => {
                viewer.addEventListener('load', () => {
                    const spinner = viewer.parentElement.querySelector('.loading-spinner');
                    if (spinner) {
                        spinner.style.display = 'none';
                    }
                });
            });
            
            // Add event listeners to fullscreen buttons
            document.querySelectorAll('.fullscreen-button').forEach(button => {
                button.addEventListener('click', function() {
                    const modelId = this.getAttribute('data-model');
                    const modelViewer = document.getElementById(modelId);
                    
                    if (modelViewer) {
                        try {
                            // Get model source and camera settings
                            const modelSrc = modelViewer.getAttribute('src');
                            const cameraOrbit = modelViewer.getAttribute('camera-orbit');
                            
                            // Update fullscreen viewer with the model
                            fullscreenViewer.setAttribute('src', modelSrc);
                            if (cameraOrbit) {
                                fullscreenViewer.setAttribute('camera-orbit', cameraOrbit);
                            }
                            
                            // Show the modal
                            modal.style.display = 'block';
                            
                            // Update status
                            const statusElem = this.closest('.model-info').querySelector('.status-message');
                            if (statusElem) {
                                statusElem.style.display = 'block';
                                statusElem.textContent = 'Opening in full screen...';
                                
                                // Hide status after 3 seconds
                                setTimeout(() => {
                                    statusElem.style.display = 'none';
                                }, 3000);
                            }
                        } catch (error) {
                            console.error('Full screen error:', error);
                            
                            // Update status with error
                            const statusElem = this.closest('.model-info').querySelector('.status-message');
                            if (statusElem) {
                                statusElem.style.display = 'block';
                                statusElem.textContent = 'Error opening full screen: ' + error.message;
                            }
                        }
                    }
                });
            });
            
            // Close the modal when clicking the close button
            closeButton.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close the modal when clicking outside the content
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Close modal with escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>