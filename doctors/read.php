<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/Doctor.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Doctor Object
$doctor = new Doctor($db);

// Check if an ID is provided in the URL
if (isset($_GET['doctor_id'])) {
    // Redirect to read_single.php to handle a single doctor request
    require 'read_single.php';
    exit();
}

// Retrieve all doctors
$result = $doctor->read();
$num = $result->rowCount();

// Check if any doctors exist
if ($num > 0) {
    $doctors_arr = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $doctor_item = array(
            'doctor_id' => $doctor_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'specialty' => $specialty,
            'email' => $email,
            'phone' => $phone
        );

        // Push to array
        array_push($doctors_arr, $doctor_item);
    }

    echo json_encode($doctors_arr);
} else {
    echo json_encode(array('message' => 'No Doctors Found'));
}
?>