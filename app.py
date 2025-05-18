from flask import Flask, request, jsonify
import os
import base64
from dotenv import load_dotenv
import tempfile
from google import generativeai as genai
from flask_cors import CORS
import json

# Load environment variables
load_dotenv()

app = Flask(__name__)
# Configure CORS properly - allow requests from any origin
CORS(app, resources={r"/*": {"origins": "*"}})

# Use the API key from environment variables or fallback to the hardcoded one
API_KEY = os.getenv("GOOGLE_API_KEY", "AIzaSyCzVwiwU-DBYVlgcXOgg9-4NUpd9Hjw5iA")
genai.configure(api_key=API_KEY)

@app.route('/identify', methods=['POST', 'OPTIONS'])
def identify_species():
    # Handle preflight OPTIONS request
    if request.method == 'OPTIONS':
        return '', 204

    try:
        image_data = request.json.get('image')
        if not image_data or ',' not in image_data:
            return jsonify({'error': 'Invalid image data'}), 400

        base64_image = image_data.split(',')[1]
        binary_image = base64.b64decode(base64_image)

        with tempfile.NamedTemporaryFile(delete=False, suffix='.jpg') as temp_file:
            temp_file.write(binary_image)
            temp_file_path = temp_file.name

        try:
            model = genai.GenerativeModel('gemini-2.0-flash')
            with open(temp_file_path, 'rb') as f:
                # Specify the mime type when uploading the file
                uploaded_file = genai.upload_file(f, mime_type="image/jpeg")

            response = model.generate_content([
                uploaded_file,
                """
                Identify the species in this image including scientific and common names.
                
                IF THE IMAGE DOES NOT CONTAIN A BIOLOGICAL SPECIES, RESPOND WITH 'NOT A SPECIES'.
                
                If a species is identified, provide:
                - Common name(s)
                - Scientific name
                - Key features
                - Interesting facts
                
                Format the response as clean HTML that can be directly inserted into a webpage.
                The HTML should be complete but without <html> or <body> tags.
                Do not include any introductory text or conversational responses - only provide the formatted content.
                """
            ])

            response_text = response.text.replace("```html", "").replace("```", "").strip()

            return jsonify({'result': response_text})
        finally:
            if os.path.exists(temp_file_path):
                os.unlink(temp_file_path)

    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/identify-species', methods=['POST', 'OPTIONS'])
def identify_species_detailed():
    # Handle preflight OPTIONS request
    if request.method == 'OPTIONS':
        return '', 204
    
    try:
        # Get image data from request
        image_data = request.json.get('image')
        if not image_data or ',' not in image_data:
            return jsonify({'error': 'Invalid image data'}), 400
        
        # Extract base64 image data
        base64_image = image_data.split(',')[1]
        binary_image = base64.b64decode(base64_image)
        
        # Create temporary file for the image
        with tempfile.NamedTemporaryFile(delete=False, suffix='.jpg') as temp_file:
            temp_file.write(binary_image)
            temp_file_path = temp_file.name
        
        try:
            # Initialize Gemini model
            model = genai.GenerativeModel('gemini-2.0-flash')
            
            # Upload image file
            with open(temp_file_path, 'rb') as f:
                uploaded_file = genai.upload_file(f, mime_type="image/jpeg")
            
            # Comprehensive analysis prompt for species, status, and location
            analysis_prompt = """
            Analyze this wildlife image comprehensively and provide detailed information in JSON format:

            1. Identify the species (common name and scientific name)
            2. Assess the status/condition of the animal
            3. Identify the likely habitat/location based on visual cues in the image
            4. If possible, estimate geographical coordinates based on visible environmental features

            For each identification, provide a confidence score that accurately reflects your certainty:
            - 90-100%: Very high confidence with clear visual evidence
            - 70-89%: Good confidence with some distinctive features visible
            - 50-69%: Moderate confidence with partial or unclear features
            - 30-49%: Low confidence, educated guess based on limited visual cues
            - Below 30%: Very uncertain, minimal distinguishing features visible

            Format your response as a JSON object with these fields:
            {
                "species": "Common name (Scientific name)",
                "status": "One of: [Healthy, Injured, Endangered, Invasive]",
                "location": "Habitat/location description based on visual cues",
                "coordinates": {
                    "latitude": 0.0,
                    "longitude": 0.0
                },
                "features": ["feature1", "feature2", "feature3"],
                "speciesConfidence": numerical value between 1-100,
                "statusConfidence": numerical value between 1-100,
                "locationConfidence": numerical value between 1-100,
                "coordinatesConfidence": numerical value between 1-100
            }

            If you cannot identify geographical coordinates, use null values for latitude and longitude and set coordinatesConfidence to 0.

            If you cannot identify a species in the image, respond with:
            {
                "species": "Unknown",
                "status": "Unknown",
                "location": "Unknown",
                "coordinates": {
                    "latitude": null,
                    "longitude": null
                },
                "features": ["Not a clear wildlife image"],
                "speciesConfidence": 0,
                "statusConfidence": 0,
                "locationConfidence": 0,
                "coordinatesConfidence": 0
            }

            Return ONLY the JSON object, nothing else.
            """
            
            analysis_response = model.generate_content([uploaded_file, analysis_prompt])
            analysis_text = analysis_response.text
            
            # Clean up the response text to extract only the JSON
            analysis_text = analysis_text.strip()
            if analysis_text.startswith("```json"):
                analysis_text = analysis_text[7:]
            if analysis_text.endswith("```"):
                analysis_text = analysis_text[:-3]
            
            analysis_data = json.loads(analysis_text.strip())
            
            # Construct the result with all fields
            result = {
                'species': analysis_data['species'],
                'status': analysis_data['status'],
                'location': analysis_data['location'],
                'features': analysis_data['features'],
                'speciesConfidence': analysis_data['speciesConfidence'],
                'statusConfidence': analysis_data['statusConfidence'],
                'locationConfidence': analysis_data['locationConfidence'],
                'coordinates': {
                    'latitude': analysis_data.get('coordinates', {}).get('latitude'),
                    'longitude': analysis_data.get('coordinates', {}).get('longitude')
                },
                'coordinatesConfidence': analysis_data.get('coordinatesConfidence', 0)
            }
            
            return jsonify(result)
            
        finally:
            # Clean up temporary file
            if os.path.exists(temp_file_path):
                os.unlink(temp_file_path)
                
    except Exception as e:
        print(f"Error processing image: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/get_species_details', methods=['POST', 'OPTIONS'])
def get_species_details():
    # Handle preflight OPTIONS request
    if request.method == 'OPTIONS':
        return '', 204
    
    try:
        # Get species information from request
        data = request.json
        species_name = data.get('species_name', '')
        scientific_name = data.get('scientific_name', '')
        
        if not species_name or not scientific_name:
            return jsonify({'error': 'Missing species information'}), 400
        
        # Create a prompt for detailed species information
        prompt = f"""
        Provide detailed information about the species:
        Common Name: {species_name}
        Scientific Name: {scientific_name}

        IF THE COMMON NAME OR SCIENTIFIC NAME IS NOT VALID, RESPOND WITH 'NOT A SPECIES'.
        
        Include the following information in HTML format with appropriate headings:
        
        1. Description: Physical characteristics, size, coloration, and distinctive features
        2. Habitat: Where this species typically lives and its preferred environment
        3. Distribution: Geographic regions where this species can be found
        4. Feautures: Unique traits or behaviors that set this species apart (in bullet points)
        
        Format the response as clean HTML that can be directly inserted into a webpage.
        Do not include introductory text like "Here is information about..." just provide the formatted content.
        """
        

        model = genai.GenerativeModel('gemini-2.0-flash')
        response = model.generate_content(prompt)

        response_text = response.text.replace("```html", "").replace("```", "").strip()
        
        # Return the result
        return jsonify({
            'details': response_text,
            'species_name': species_name,
            'scientific_name': scientific_name
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    port = 8800
    print(f"Starting server on http://localhost:{port}")
    app.run(debug=True, port=port)