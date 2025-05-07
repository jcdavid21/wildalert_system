<?php
// get_species_details.php

// Include CORS middleware
require_once 'cors_middleware.php';
enableCORS();

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the raw POST data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Check if required data is present
if (!isset($data['species_name']) || !isset($data['scientific_name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

// Extract species information
$speciesName = $data['species_name'];
$scientificName = $data['scientific_name'];
$speciesId = isset($data['species_id']) ? $data['species_id'] : '';

// Prepare data for the Python API
$requestData = json_encode([
    'species_name' => $speciesName,
    'scientific_name' => $scientificName
]);

// Set up cURL to make a request to the Python API
$ch = curl_init('http://localhost:8800/get_species_details');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($requestData)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout to 10 seconds

// Execute the cURL request
$response = curl_exec($ch);
$error = curl_error($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check for errors
if ($error) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error fetching species details',
        'details' => $error,
        'fallback' => true,
        'species_name' => $speciesName,
        'scientific_name' => $scientificName
    ]);
    exit();
}

// If we get a successful response, return it
if ($statusCode === 200) {
    // Ensure the response is valid JSON
    $decodedResponse = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $response;
    } else {
        // Invalid JSON response from the Python API
        http_response_code(500);
        echo json_encode([
            'error' => 'Invalid response from species API',
            'fallback' => true,
            'species_name' => $speciesName,
            'scientific_name' => $scientificName
        ]);
    }
} else {
    // Return an error with fallback content
    $fallbackHTML = '
        <h2>Description</h2>
        <p>Information about this species is currently being compiled. Check back soon for detailed information about ' . $speciesName . ' (' . $scientificName . ').</p>
        
        <h2>Basic Information</h2>
        <ul>
            <li><strong>Common Name:</strong> ' . $speciesName . '</li>
            <li><strong>Scientific Name:</strong> ' . $scientificName . '</li>
        </ul>
        
        <h2>Note</h2>
        <p>Our AI system is currently unable to retrieve detailed information about this species. This could be due to server load or connectivity issues. Please try again later.</p>
    ';
    
    http_response_code(200); // Still return 200 with fallback content
    echo json_encode([
        'details' => $fallbackHTML,
        'species_name' => $speciesName,
        'scientific_name' => $scientificName,
        'fallback' => true
    ]);
}