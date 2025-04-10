<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and doctor model
include_once '../Database.php';
include_once '../backend/Doctor.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Doctor Object
$doctor = new Doctor($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->doctor_id) || !isset($data->first_name) || !isset($data->last_name) || !isset($data->specialty) || !isset($data->email) || !isset($data->phone)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set doctor properties
$doctor->doctor_id = $data->doctor_id;
$doctor->first_name = $data->first_name;
$doctor->last_name = $data->last_name;
$doctor->specialty = $data->specialty;
$doctor->email = $data->email;
$doctor->phone = $data->phone;

// Update doctor
if ($doctor->update()) {
    echo json_encode([
        'doctor_id' => $doctor->doctor_id,
        'first_name' => $doctor->first_name,
        'last_name' => $doctor->last_name,
        'specialty' => $doctor->specialty,
        'email' => $doctor->email,
        'phone' => $doctor->phone
    ]);
} else {
    echo json_encode(['message' => 'Doctor Not Updated']);
}
?>