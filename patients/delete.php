<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../Database.php';
include_once '../backend/Patient.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Patient object
$patient = new Patient($db);

// Check for POST data
if (!isset($_POST['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing patient_id']);
    exit();
}

$patient_id = $_POST['patient_id'];

// Check if patient exists
$query = 'SELECT patient_id FROM patients WHERE patient_id = :patient_id';
$stmt = $db->prepare($query);
$stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Patient not found']);
    exit();
}

// Delete patient
try {
    $deleteQuery = 'DELETE FROM patients WHERE patient_id = :patient_id';
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Patient deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Delete failed', 'error' => $e->getMessage()]);
}
