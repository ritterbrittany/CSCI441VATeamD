<?php
session_start();

// Set response headers
header('Content-Type: application/json');

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Ensure patient_id is provided
if (!isset($_POST['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing patient_id']);
    exit();
}

require_once '../Database.php';

$database = new Database();
$pdo = $database->connect();

$patient_id = intval($_POST['patient_id']);

// Check if patient exists
try {
    $checkStmt = $pdo->prepare("SELECT patient_id FROM patients WHERE patient_id = :patient_id");
    $checkStmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
        exit();
    }

    // Perform deletion
    $deleteStmt = $pdo->prepare("DELETE FROM patients WHERE patient_id = :patient_id");
    $deleteStmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Deletion failed',
        'error' => $e->getMessage()
    ]);
}
