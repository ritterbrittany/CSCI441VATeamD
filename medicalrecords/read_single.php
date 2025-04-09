<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/MedicalRecord.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate MedicalRecord Object
$medicalRecord = new MedicalRecord($db);

// Get medical record ID from query parameter
$id = isset($_GET['id']) ? $_GET['id'] : die(json_encode(['message' => 'patient_id Not Found']));

// Retrieve a single medical record
$result = $medicalRecord->read_single($id);
$num = $result->rowCount();

// Check if the medical record exists
if ($num > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $medical_record_item = array(
        'id' => $id,
        'appointment_id' => $appointment_id,
        'notes' => $notes,
        'created_at' => $created_at
    );

    echo json_encode($medical_record_item);
} else {
    echo json_encode(['message' => 'patient_id Not Found']);
}
?>