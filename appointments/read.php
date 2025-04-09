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

// Get all appointments
$result = $appointment->read();
$num = $result->rowCount();

// Check if any appointments exist
if ($num > 0) {
    $appointments_arr = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $appointments_arr[] = [
            'id' => $id,
            'scheduled_date' => $scheduled_date,
            'reason' => $reason,
            'status' => $status,
            'patient_name' => $patient_name,
            'doctor_name' => $doctor_name
        ];
    }

    echo json_encode($appointments_arr);
} else {
    echo json_encode(['message' => 'No Appointments Found']);
}