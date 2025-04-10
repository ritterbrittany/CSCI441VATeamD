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

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    // Redirect to read_single.php to handle a single audit log request
    require 'read_single.php';
    exit();
}

// Retrieve all audit logs
$result = $auditlog->read();
$num = $result->rowCount();

// Check if any logs exist
if ($num > 0) {
    $auditlogs_arr = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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

        // Push to array
        array_push($auditlogs_arr, $auditlog_item);
    }

    echo json_encode($auditlogs_arr);
} else {
    echo json_encode(array('message' => 'No Audit Logs Found'));
}
?>