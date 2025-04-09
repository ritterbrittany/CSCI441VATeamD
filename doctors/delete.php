<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and doctor model
include_once '../../config/Database.php';
include_once '../../models/Doctor.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Doctor Object
$doctor = new Doctor($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->doctor_id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set doctor_id
$doctor->doctor_id = $data->doctor_id;

// Check if doctor exists
$query = 'SELECT doctor_id FROM doctors WHERE doctor_id = :doctor_id';
$stmt = $db->prepare($query);
$stmt->bindParam(':doctor_id', $doctor->doctor_id);
$stmt->execute();
if ($stmt->rowCount() == 0) {
    echo json_encode(['message' => 'doctor_id Not Found']);
    exit();
}

// Delete doctor
if ($doctor->delete()) {
    echo json_encode(['doctor_id' => $doctor->doctor_id]);
} else {
    echo json_encode(['message' => 'Doctor Not Deleted']);
}
?>