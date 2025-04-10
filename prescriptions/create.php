<?php
// Headers to allow CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../Database.php';
include_once '../backend/Prescription.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Prescription Object
$prescription = new Prescription($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if all required fields are present
if (!isset($data->appointment_id) || !isset($data->medication_name) || !isset($data->dosage) || !isset($data->instructions)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set the prescription properties
$prescription->appointment_id = $data->appointment_id;
$prescription->medication_name = $data->medication_name;
$prescription->dosage = $data->dosage;
$prescription->instructions = $data->instructions;

// Create the prescription in the database
if ($prescription->create()) {
    echo json_encode([
        'message' => 'Prescription Created',
        'id' => $db->lastInsertId(),
        'appointment_id' => $prescription->appointment_id,
        'medication_name' => $prescription->medication_name,
        'dosage' => $prescription->dosage,
        'instructions' => $prescription->instructions
    ]);
} else {
    echo json_encode(['message' => 'Prescription Not Created']);
}
?>