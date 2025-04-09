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

// Get raw PUT data
$data = json_decode(file_get_contents("php://input"));

// Validate required parameters
if (
    !isset($data->id) || 
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
$appointment->id = $data->id;
$appointment->patient_id = $data->patient_id;
$appointment->doctor_id = $data->doctor_id;
$appointment->scheduled_date = $data->scheduled_date;
$appointment->reason = $data->reason;
$appointment->status = $data->status;

// Check scheduled date
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

// Check double-booking (excluding current appointment id)
$query = 'SELECT id FROM appointments 
          WHERE (patient_id = :patient_id OR doctor_id = :doctor_id) 
          AND scheduled_date = :scheduled_date 
          AND id != :id';

$stmt = $db->prepare($query);
$stmt->bindParam(':patient_id', $appointment->patient_id);
$stmt->bindParam(':doctor_id', $appointment->doctor_id);
$stmt->bindParam(':scheduled_date', $appointment->scheduled_date);
$stmt->bindParam(':id', $appointment->id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['message' => 'Time slot is already taken (by patient or doctor)']);
    exit();
}

// Update appointment
if ($appointment->update()) {
    echo json_encode(['message' => 'Appointment Updated']);
} else {
    echo json_encode(['message' => 'Appointment Not Updated']);
}