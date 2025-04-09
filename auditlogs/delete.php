<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/AuditLog.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->connect();

// Instantiate AuditLog Object
$auditlog = new AuditLog($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if 'id' is provided
if (!isset($data->id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set the audit log ID
$auditlog->id = $data->id;

// Attempt to delete the audit log
if ($auditlog->delete()) {
    echo json_encode(['message' => 'Audit Log Deleted']);
} else {
    echo json_encode(['message' => 'Audit Log Not Deleted']);
}
?>