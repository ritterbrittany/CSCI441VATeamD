<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../Database.php';
include_once '../backend/Diagnosis.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Diagnosis Object
$diagnosis = new Diagnosis($db);

// If an ID is provided, fetch the specific diagnosis
if (isset($_GET['id'])) {
    $id = $_GET['id']; // Get the 'id' parameter from the URL
    $result = $diagnosis->read_single($id); // Call the method for a single diagnosis
    $num = $result->rowCount();

    if ($num > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        extract($row);

        $diagnosis_item = [
            'id' => $id,
            'appointment_id' => $appointment_id,
            'diagnosis_code' => $diagnosis_code,
            'description' => $description
        ];

        echo json_encode($diagnosis_item);
    } else {
        echo json_encode(['message' => 'Diagnosis Not Found']);
    }
} else {
    // If no 'id' is provided, fetch all diagnoses
    $result = $diagnosis->read();
    $num = $result->rowCount();

    if ($num > 0) {
        $diagnoses_arr = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $diagnoses_arr[] = [
                'id' => $id,
                'appointment_id' => $appointment_id,
                'diagnosis_code' => $diagnosis_code,
                'description' => $description
            ];
        }
        echo json_encode($diagnoses_arr);
    } else {
        echo json_encode(['message' => 'No Diagnoses Found']);
    }
}
?>