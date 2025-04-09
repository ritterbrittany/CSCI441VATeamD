<?php
// Headers
// Start output buffering
ob_start();
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

// Get doctor_id from query param
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : die(json_encode(['message' => 'doctor_id Not Found']));

// Query single doctor
$result = $doctor->read_single($doctor_id);
$num = $result->rowCount();

if ($num > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $doctor_item = array(
        'doctor_id' => $doctor_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'specialty' => $specialty,
        'email' => $email,
        'phone' => $phone
    );

    echo json_encode($doctor_item);
} else {
    echo json_encode(['message' => 'doctor_id Not Found']);
}
// Flush buffer
ob_end_flush();
?>