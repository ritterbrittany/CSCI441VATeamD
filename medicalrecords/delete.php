<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model files
include_once '../../config/Database.php';
include_once '../../models/MedicalRecord.php';

// Instantiate database connection and medical record object
$database = new Database();
$db = $database->connect();
$medical_record = new MedicalRecord($db);

// Get raw data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set medical record ID
$medical_record->id = $data->id;

// Delete medical record
if ($medical_record->delete()) {
    echo json_encode(['message' => 'Medical Record Deleted']);
} else {
    echo json_encode(['message' => 'Medical Record Not Deleted']);
}
?>