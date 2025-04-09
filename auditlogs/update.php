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

// Check if required parameters are provided
if (
    !isset($data->id) ||
    !isset($data->user_id) ||
    !isset($data->action_type) ||
    !isset($data->record_type) ||
    !isset($data->record_id) ||
    !isset($data->timestamp) ||
    !isset($data->details) ||
    !isset($data->ip_address) ||
    !isset($data->user_agent)
) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set the audit log properties
$auditlog->id = $data->id;
$auditlog->user_id = $data->user_id;
$auditlog->action_type = $data->action_type;
$auditlog->record_type = $data->record_type;
$auditlog->record_id = $data->record_id;
$auditlog->timestamp = $data->timestamp;
$auditlog->details = $data->details;
$auditlog->ip_address = $data->ip_address;
$auditlog->user_agent = $data->user_agent;

// Attempt to update the audit log
if ($auditlog->update()) {
    echo json_encode(['message' => 'Audit Log Updated Successfully']);
} else {
    echo json_encode(['message' => 'Audit Log Not Updated']);
}
?>