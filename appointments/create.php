<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Includes
include_once '../../config/Database.php';
include_once '../../models/Appointment.php';

// DB connect
$database = new Database();
$db = $database->connect();

// Init Appointment object
$appointment = new Appointment($db);

// Get raw POST data
$data = json_decode(file_get_contents("php://input"));

// Validate required parameters
if (
    !isset($data->patient_id) || 
    !isset($data->doctor_id) || 
    !isset($data->scheduled_date) || 
    !isset($data->reason) || 
    !isset($data->status)
) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Assign data
$appointment->patient_id = $data->patient_id;
$appointment->doctor_id = $data->doctor_id;
$appointment->scheduled_date = $data->scheduled_date;
$appointment->reason = $data->reason;
$appointment->status = $data->status;

// Validate scheduled date
if (strtotime($appointment->scheduled_date) < time()) {
    echo json_encode(['message' => 'Cannot schedule appointment in the past']);
    exit();
}

// Validate foreign keys
if (!$appointment->patientExists()) {
    echo json_encode(['message' => 'Invalid patient_id']);
    exit();
}
if (!$appointment->doctorExists()) {
    echo json_encode(['message' => 'Invalid doctor_id']);
    exit();
}

// Check for double-booking
if ($appointment->isPatientDoubleBooked()) {
    echo json_encode(['message' => 'Patient is already booked for this time']);
    exit();
}
if ($appointment->isDoctorDoubleBooked()) {
    echo json_encode(['message' => 'Doctor is already booked for this time']);
    exit();
}

// Create appointment
if ($appointment->create()) {
    echo json_encode(['message' => 'Appointment Created']);
} else {
    echo json_encode(['message' => 'Appointment Not Created']);
}
?>