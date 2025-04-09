<?php
// Headers to allow CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Handle OPTIONS request for CORS preflight
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Route based on HTTP method
switch ($method) {
    case 'GET':
        require 'read.php';
        break;
    case 'POST':
        require 'create.php';
        break;
    case 'PUT':
        require 'update.php';
        break;
    case 'DELETE':
        require 'delete.php';
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
}
?>