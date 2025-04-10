<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../Database.php';
include_once '../backend/Prescription.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Prescription Object
$prescription = new Prescription($db);

// Get prescription ID from URL query parameter
$id = isset($_GET['id']) ? $_GET['id'] : die(json_encode(['message' => 'Prescription ID Not Found']));

// Retrieve single prescription
$result = $prescription->read_single($id);
$num = $result->rowCount();

if ($num > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $prescription_item = [
        'id' => $id,
        'appointment_id' => $appointment_id,
        'medication_name' => $medication_name,
        'dosage' => $dosage,
        'instructions' => $instructions
    ];

    echo json_encode($prescription_item);
} else {
    echo json_encode(['message' => 'Prescription Not Found']);
}
?>