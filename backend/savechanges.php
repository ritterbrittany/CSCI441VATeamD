<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection (replace with your actual DB credentials)
$host = 'localhost';
$username = 'root'; // replace with your DB username
$password = ''; // replace with your DB password
$dbname = 'emr_system'; // replace with your DB name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the patient data from the form
$patientId = $_POST['patient-id'];
$name = $_POST['name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$medications = $_POST['medications'];
$notes = $_POST['notes'];

// SQL query to update patient record
$sql = "UPDATE patients SET name=?, dob=?, gender=?, medications=?, notes=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $name, $dob, $gender, $medications, $notes, $patientId);

if ($stmt->execute()) {
    // Successfully updated
    header("Location: changes-confirmation.php"); 
    exit();
} else {
    // Error updating
    echo "Error: " . $stmt->error;
}

$conn->close();
?>