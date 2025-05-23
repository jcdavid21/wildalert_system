# WildAlert Setup Guide

## YOU MUST NEED STRONG WIFI CONNECTION

## 1. Create the Database
- Create a MySQL database named: `WildAlert_db`
- Import the SQL file located at: `backend/dbase`

## 2. Install Dependencies
Make sure you have Python installed. Then install the required dependencies:

```bash
pip install flask python-dotenv flask-cors google-generativeai
```

## 3. Application Code Dependencies
The Python app uses the following libraries:

```python
from flask import Flask, request, jsonify
import os
import base64
from dotenv import load_dotenv
import tempfile
from google import generativeai as genai
from flask_cors import CORS
```

## 4. Run the Backend
Start the Flask application with:

```bash
python app.py
```

## 5. Open the Frontend
Open `index.php` in your browser or run it via a PHP server.

---

## NOTE:
There is a folder named for_test_images inside the images directory that contains sample photos. 
You can also download additional images from Google to test with other species.


