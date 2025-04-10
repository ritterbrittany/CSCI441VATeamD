<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../../config/Database.php';
include_once '../../models/Patient.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Patient object
$patient = new Patient($db);

// Get raw input
$data = json_decode(file_get_contents("php://input"));

// Validate required field
if (!isset($data->patient_id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

$patient->patient_id = $data->patient_id;

// Check if patient exists
$query = 'SELECT patient_id FROM patients WHERE patient_id = :patient_id';
$stmt = $db->prepare($query);
$stmt->bindParam(':patient_id', $patient->patient_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo json_encode(['message' => 'patient_id Not Found']);
    exit();
}

// Attempt to delete patient
if ($patient->delete()) {
    echo json_encode(['patient_id' => $patient->patient_id]);
} else {
    echo json_encode(['message' => 'Patient Not Deleted']);
}