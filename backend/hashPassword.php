<?php
// Define plain-text passwords
$plain_password_doctor = 'hashedpassword456'; // For doctor_alice
$plain_password_admin = 'hashedpassword123';  // For admin

// Hash the passwords using password_hash
$hashed_password_doctor = password_hash($plain_password_doctor, PASSWORD_DEFAULT);
$hashed_password_admin = password_hash($plain_password_admin, PASSWORD_DEFAULT);

// Set up the database connection
try {
    $pdo = new PDO('pgsql:host=dpg-cvqn0he3jp1c73dsfnvg-a.ohio-postgres.render.com;port=5432;dbname=emr_platform', 'emr_platform_user', 'rBirGywJYnVMuJHFFuc8pYvTJIyrJXik');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Update the password for doctor_alice
$stmt_doctor = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE username = 'doctor_alice'");
$stmt_doctor->execute(['password_hash' => $hashed_password_doctor]);

// Update the password for admin
$stmt_admin = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE username = 'admin'");
$stmt_admin->execute(['password_hash' => $hashed_password_admin]);

echo "Password hashes for doctor_alice and admin have been updated successfully!";
?>
