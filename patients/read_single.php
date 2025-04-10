<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../Database.php';
include_once '../backend/Patient.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Patient Object
$patient = new Patient($db);

// Check if 'patient_id' is passed in the query parameter
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];  // Read 'patient_id' from the URL
} else {
    // If not found, return an error message
    echo json_encode(['message' => 'patient_id Not Found']);
    exit();
}

// Query single patient based on 'patient_id'
$result = $patient->read_single($patient_id);
$num = $result->rowCount();

if ($num > 0) {
    // Patient found
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $patient_item = array(
        'patient_id' => $patient_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'date_of_birth' => $date_of_birth,
        'gender' => $gender,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'state' => $state,
        'zip_code' => $zip_code
    );

    echo json_encode($patient_item);  // Return patient info in JSON format
} else {
    // No patient found with the given 'patient_id'
    echo json_encode(['message' => 'patient_id Not Found']);
}
?>