<?php
require_once '../Database.php';
require_once '../backend/Patient.php';

$database = new Database();
$db = $database->connect();

$patient = new Patient($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient->first_name = $_POST['first_name'] ?? '';
    $patient->last_name = $_POST['last_name'] ?? '';
    $patient->date_of_birth = $_POST['date_of_birth'] ?? '';
    $patient->gender = $_POST['gender'] ?? '';
    $patient->email = $_POST['email'] ?? '';
    $patient->phone = $_POST['phone'] ?? '';
    $patient->address = $_POST['address'] ?? '';
    $patient->city = $_POST['city'] ?? '';
    $patient->state = $_POST['state'] ?? '';
    $patient->zip_code = $_POST['zip_code'] ?? '';
    $patient->ssn = $_POST['ssn'] ?? '';

    if ($patient->create()) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Patient Created']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create patient']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
