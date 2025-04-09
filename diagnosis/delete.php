<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../../config/Database.php';
include_once '../../models/Diagnosis.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Diagnosis Object
$diagnosis = new Diagnosis($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"), true);  // Read JSON input and decode it to an associative array

// Validate if the 'id' exists in the data
if (!isset($data['id'])) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set the diagnosis ID
$diagnosis->id = $data['id'];  // Access id using array notation

// Attempt to delete the diagnosis
if ($diagnosis->delete($diagnosis->id)) {
    echo json_encode(['message' => 'Diagnosis Deleted']);
} else {
    echo json_encode(['message' => 'Diagnosis Not Found']);
}
?>