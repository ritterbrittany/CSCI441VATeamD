<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../Database.php';
include_once '../backend/Diagnosis.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Diagnosis Object
$diagnosis = new Diagnosis($db);

// Get raw posted data (PUT request)
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->id) || !isset($data->diagnosis_code) || !isset($data->description) || !isset($data->appointment_id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set diagnosis properties
$diagnosis->id = $data->id;
$diagnosis->diagnosis_code = $data->diagnosis_code;
$diagnosis->description = $data->description;
$diagnosis->appointment_id = $data->appointment_id;

// Check if the diagnosis exists
$existing_diagnosis = $diagnosis->read_single($diagnosis->id);
if ($existing_diagnosis->rowCount() == 0) {
    echo json_encode(['message' => 'Diagnosis Not Found']);
    exit();
}

// Update Diagnosis
if ($diagnosis->update()) {
    echo json_encode([
        'id' => $diagnosis->id,
        'diagnosis_code' => $diagnosis->diagnosis_code,
        'description' => $diagnosis->description,
        'appointment_id' => $diagnosis->appointment_id
    ]);
} else {
    echo json_encode(['message' => 'Diagnosis Not Updated']);
}
?>