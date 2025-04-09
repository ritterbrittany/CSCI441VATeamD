<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../../config/Database.php';
include_once '../../models/Doctor.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Doctor Object
$doctor = new Doctor($db);

// Get the raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate the data
if (!isset($data->first_name) || !isset($data->last_name) || !isset($data->specialty) || !isset($data->email) || !isset($data->phone)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set the properties of the doctor
$doctor->first_name = $data->first_name;
$doctor->last_name = $data->last_name;
$doctor->specialty = $data->specialty;
$doctor->email = $data->email;
$doctor->phone = $data->phone;

// Create the doctor
if ($doctor->create()) {
    echo json_encode([
        'message' => 'Doctor Added',
        'doctor_id' => $doctor->doctor_id,  // Assuming `create()` method sets the doctor_id after insertion
        'first_name' => $doctor->first_name,
        'last_name' => $doctor->last_name
    ]);
} else {
    echo json_encode(['message' => 'Doctor Not Created']);
}