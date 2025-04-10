<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/MedicalRecord.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate MedicalRecord Object
$medicalRecord = new MedicalRecord($db);

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    // Redirect to read_single.php to handle a single medical record request
    require 'read_single.php';
    exit();
}

// Retrieve all medical records
$result = $medicalRecord->read();
$num = $result->rowCount();

// Check if any records exist
if ($num > 0) {
    $medical_records_arr = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $medical_record_item = array(
            'id' => $id,
            'appointment_id' => $appointment_id,
            'notes' => $notes,
            'created_at' => $created_at
        );

        array_push($medical_records_arr, $medical_record_item);
    }

    echo json_encode($medical_records_arr);
} else {
    echo json_encode(
        array('message' => 'No medical records found')
    );
}
?>