<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Appointment.php';

// Instantiate DB
$database = new Database();
$db = $database->connect();

$appointment = new Appointment($db);

// Get raw input
$data = json_decode(file_get_contents("php://input"));

// Validate required field
if (!isset($data->id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set ID
$appointment->id = $data->id;

// Check if appointment exists
$stmt = $db->prepare("SELECT id FROM appointments WHERE id = :id");
$stmt->bindParam(':id', $appointment->id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo json_encode(['message' => 'Appointment Not Found']);
    exit();
}

// Attempt to delete
if ($appointment->delete()) {
    echo json_encode(['id' => $appointment->id]);
} else {
    echo json_encode(['message' => 'Appointment Not Deleted']);
}