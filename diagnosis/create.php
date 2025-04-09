<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/Diagnosis.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Diagnosis Object
$diagnosis = new Diagnosis($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (
    !isset($data->appointment_id) || 
    !isset($data->diagnosis_code) || 
    !isset($data->description)
) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set Diagnosis properties
$diagnosis->appointment_id = $data->appointment_id;
$diagnosis->diagnosis_code = $data->diagnosis_code;
$diagnosis->description = $data->description;

// Create Diagnosis
if ($diagnosis->create()) {
    echo json_encode([
        'message' => 'Diagnosis Created',
        'appointment_id' => $diagnosis->appointment_id,
        'diagnosis_code' => $diagnosis->diagnosis_code,
        'description' => $diagnosis->description
    ]);
} else {
    echo json_encode(['message' => 'Diagnosis Not Created']);
}
?>