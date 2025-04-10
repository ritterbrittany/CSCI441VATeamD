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

// Instantiate Patient Object
$patient = new Patient($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Set patient properties
$patient->first_name = $data->first_name;
$patient->last_name = $data->last_name;
$patient->date_of_birth = $data->date_of_birth;
$patient->gender = $data->gender;
$patient->email = $data->email;
$patient->phone = $data->phone;
$patient->address = $data->address;
$patient->city = $data->city;
$patient->state = $data->state;
$patient->zip_code = $data->zip_code;
$patient->ssn = $data->ssn;

// Create the patient
if ($patient->create()) {
    echo json_encode(['message' => 'Patient Created']);
} else {
    echo json_encode(['message' => 'Patient Not Created']);
}
?>