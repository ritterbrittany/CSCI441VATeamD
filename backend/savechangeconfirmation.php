<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changes Saved</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Fort Hays Hospital EMR</h1>
    </header>

    <div class="confirmation-container">
        <h2>Your changes have been saved successfully!</h2>
        <p>Click below to return to the dashboard or the patient records list.</p>

        <!-- Buttons to go back to Dashboard or View Patient Records -->
        <li><a href="http://localhost/SWEPROJECTTEAMD/CSCI441VATeamD/backend/dashboard.php">Back to Dashboard</a>
        <li><a href="http://localhost/SWEPROJECTTEAMD/CSCI441VATeamD/backend/patientlistpage.php">View Patient Records</a>
    </div>
</body>
</html>