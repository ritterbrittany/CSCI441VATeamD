<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database and model
include_once '../../config/Database.php';
include_once '../../models/AuditLog.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate AuditLog Object
$auditlog = new AuditLog($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if all required parameters are provided
if (
    !isset($data->user_id) || 
    !isset($data->action_type) ||
    !isset($data->user_agent) ||
    !isset($data->record_type) || 
    !isset($data->record_id) ||
    !isset($data->details) ||
    !isset($data->ip_address)
) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    exit();
}

// Set audit log properties
$auditlog->user_id = $data->user_id;
$auditlog->action_type = $data->action_type;
$auditlog->record_type = $data->record_type;
$auditlog->record_id = $data->record_id;
$auditlog->details = $data->details;
$auditlog->ip_address = $data->ip_address;
$auditlog->user_agent = $data->user_agent;
$auditlog->timestamp = $data->timestamp; // Optionally, set timestamp if needed

// Attempt to create the audit log
if ($auditlog->create()) {
    echo json_encode([
        'message' => 'Audit Log Created',
        'id' => $auditlog->id,
        'user_id' => $auditlog->user_id,
        'action_type' => $auditlog->action_type,
        'record_type' => $auditlog->record_type,
        'record_id' => $auditlog->record_id,
        'details' => $auditlog->details,
        'ip_address' => $auditlog->ip_address,
        'user_agent' => $auditlog->user_agent,
        'timestamp' => $auditlog->timestamp
    ]);
} else {
    echo json_encode(['message' => 'Audit Log Not Created']);
}
?>