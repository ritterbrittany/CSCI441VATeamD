<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../../config/Database.php';
include_once '../../models/Appointment.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Appointment Object
$appointment = new Appointment($db);

// Get appointment ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : die(json_encode(['message' => 'Appointment ID Not Found']));

// Retrieve single appointment
$result = $appointment->read_single($id);
$num = $result->rowCount();

// Check if appointment exists
if ($num > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $appointment_item = [
        'id' => $id,
        'scheduled_date' => $scheduled_date,
        'reason' => $reason,
        'status' => $status,
        'patient_name' => $patient_name,
        'doctor_name' => $doctor_name
    ];

    echo json_encode($appointment_item);
} else {
    echo json_encode(['message' => 'Appointment Not Found']);
}