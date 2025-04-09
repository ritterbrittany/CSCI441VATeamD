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

// Validate required field
if (!isset($data->id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set prescription ID
$prescription->id = $data->id;

// Check if prescription exists
$result = $prescription->read_single($prescription->id);
$num = $result->rowCount();
if ($num == 0) {
    echo json_encode(['message' => 'Prescription Not Found']);
    exit();
}

// Delete Prescription
if ($prescription->delete()) {
    echo json_encode(['message' => 'Prescription Deleted']);
} else {
    echo json_encode(['message' => 'Prescription Not Deleted']);
}
?>