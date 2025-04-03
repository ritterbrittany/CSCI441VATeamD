<?php
// Include the database connection
require_once 'db.php'; 

// Define the username, password, and role for the user
$username = "admin"; // Desired username
$password = "password123"; // Plain text password (you will hash it)
$role = "admin"; // Role of the user (admin, doctor, etc.)

// Hash the password using PHP's password_hash function
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert the new user into the users table
$sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username, $hashed_password, $role]);

echo "User created successfully!";
?>