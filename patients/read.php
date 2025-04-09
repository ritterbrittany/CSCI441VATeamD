<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/Patient.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate Patient Object
$patient = new Patient($db);

// Get patient_id from URL
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

// If a patient_id is provided, fetch that patient
if ($patient_id) {
    $result = $patient->read_single($patient_id);
    $num = $result->rowCount();

    if ($num > 0) {
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

        echo json_encode($patient_item);
    } else {
        echo json_encode(['message' => 'patient_id Not Found']);
    }
} else {
    // If no patient_id, return all patients
    $result = $patient->read();
    $num = $result->rowCount();

    if ($num > 0) {
        $patients_arr = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $patients_arr[] = [
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
            ];
        }
        echo json_encode($patients_arr);
    } else {
        echo json_encode(['message' => 'No Patients Found']);
    }
}
?>