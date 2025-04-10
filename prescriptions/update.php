<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../../config/Database.php';
include_once '../../models/Prescription.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Prescription Object
$prescription = new Prescription($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->id) || !isset($data->medication_name) || !isset($data->dosage) || !isset($data->instructions)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set prescription properties
$prescription->id = $data->id;
$prescription->medication_name = $data->medication_name;
$prescription->dosage = $data->dosage;
$prescription->instructions = $data->instructions;

// Check if prescription exists
$result = $prescription->read_single($prescription->id);
$num = $result->rowCount();
if ($num == 0) {
    echo json_encode(['message' => 'Prescription Not Found']);
    exit();
}

// Update Prescription
if ($prescription->update()) {
    echo json_encode(['message' => 'Prescription Updated']);
} else {
    echo json_encode(['message' => 'Prescription Not Updated']);
}
?>