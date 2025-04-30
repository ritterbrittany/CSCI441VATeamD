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

// Get diagnosis ID from query param
$id = isset($_GET['id']) ? $_GET['id'] : die(json_encode(['message' => 'Diagnosis ID Not Found']));

// Retrieve single diagnosis
$result = $diagnosis->read_single($id);
if ($result === false) {
    echo json_encode(['message' => 'Diagnosis Not Found']);
    exit();
}

$row = $result->fetch(PDO::FETCH_ASSOC);
extract($row);

$diagnosis_item = [
    'id' => $id,
    'appointment_id' => $appointment_id,
    'diagnosis_code' => $diagnosis_code,
    'description' => $description
];

echo json_encode($diagnosis_item);
?>