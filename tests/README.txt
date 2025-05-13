EMR API Test Suite (Postman Edition)
====================================

This project includes a Postman collection to test the Patient API endpoints in your Electronic Medical Record (EMR) backend.

Included API Tests:
-------------------
- Create a Patient (POST)
- Read All Patients (GET)
- Read Single Patient by ID (GET)
- Delete Patient by ID (DELETE)

Files:
------
- emr_patient_api_tests.postman_collection.json  # Postman collection with pre-configured tests

How to Use:
-----------

1. **Install Postman**
   - Download and install Postman from https://www.postman.com/downloads/

2. **Import the Collection**
   - Open Postman
   - Click **Import** (top left)
   - Choose **Upload Files** and select `emr_patient_api_tests.postman_collection.json`

3. **Update the Base URL (if needed)**
   - Default URL: `http://localhost/api/patients/`
   - Change this if your server is hosted elsewhere

4. **Run Requests**
   - Open the collection: "EMR Patient API Tests"
   - Click on each request (e.g., "Create Patient") and hit **Send**

5. **View Test Results**
   - Go to the **Tests** tab in the response panel
   - See whether status and response structure tests passed

6. **Optional: Automate with Newman**
   - Install: `npm install -g newman`
   - Run: `newman run emr_patient_api_tests.postman_collection.json`

Notes:
------
- Make sure your local server is running and accessible at `http://localhost`
- CORS and OPTIONS headers should be handled properly in your API code
- The test data in Create/Delete operations assumes patient ID 1 is available; modify if needed

