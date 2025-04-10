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

// Get 'id' from query param
$id = isset($_GET['id']) ? $_GET['id'] : die(json_encode(['message' => 'Audit Log Not Found']));

// Query single audit log based on 'id'
$result = $auditlog->read_single($id);
$num = $result->rowCount();

if ($num > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $auditlog_item = array(
        'id' => $id,
        'user_id' => $user_id,
        'action_type' => $action_type,
        'record_type' => $record_type,
        'record_id' => $record_id,
        'timestamp' => $timestamp,
        'details' => $details,
        'ip_address' => $ip_address,
        'user_agent' => $user_agent
    );

    // Output the specific audit log
    echo json_encode($auditlog_item);
} else {
    // If not found, return an error message
    echo json_encode(['message' => 'Audit Log Not Found']);
}
?>