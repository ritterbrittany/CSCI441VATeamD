<?php
// Headers to allow CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/Prescription.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Prescription Object
$prescription = new Prescription($db);

// Get all prescriptions
$result = $prescription->read();
$num = $result->rowCount();

// Check if any prescriptions exist
if ($num > 0) {
    $prescriptions_arr = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $prescription_item = [
            'id' => $id,
            'appointment_id' => $appointment_id,
            'medication_name' => $medication_name,
            'dosage' => $dosage,
            'instructions' => $instructions
        ];

        // Push to "data"
        array_push($prescriptions_arr, $prescription_item);
    }

    echo json_encode($prescriptions_arr);
} else {
    // No Prescriptions Found
    echo json_encode(['message' => 'No Prescriptions Found']);
}
?>