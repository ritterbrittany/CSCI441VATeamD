<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../Database.php';
include_once '../backend/Doctor.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Doctor Object
$doctor = new Doctor($db);

// Check if POST data exists
if (isset($_POST['doctor_id'])) {
    $doctor_id = $_POST['doctor_id'];

    // Check if doctor exists
    $stmt = $db->prepare("SELECT doctor_id FROM doctors WHERE doctor_id = :doctor_id");
    $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Doctor not found']);
        exit();
    }

    // Delete the doctor
    if ($doctor->delete($doctor_id)) {
        echo json_encode(['success' => true, 'message' => 'Doctor deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete doctor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing doctor_id']);
}
?>
