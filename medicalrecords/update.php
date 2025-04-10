<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model files
include_once '../Database.php';
include_once '../backend/MedicalRecord.php';

// Instantiate database connection and medical record object
$database = new Database();
$db = $database->connect();
$medical_record = new MedicalRecord($db);

// Get raw data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->id) || !isset($data->appointment_id) || !isset($data->notes) || !isset($data->created_at)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set medical record properties
$medical_record->id = $data->id;
$medical_record->appointment_id = $data->appointment_id;
$medical_record->notes = $data->notes;
$medical_record->created_at = $data->created_at;

// Update medical record
if ($medical_record->update()) {
    echo json_encode(['message' => 'Medical Record Updated']);
} else {
    echo json_encode(['message' => 'Medical Record Not Updated']);
}
?>