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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name'])) {
    $data = $_POST;

    // Create doctor
    $success = $doctor->create($data);
    if ($success) {
        header("Location: Doctor.php");
        exit();
    } else {
        $message = "Failed to add doctor.";
    }
}
?>
