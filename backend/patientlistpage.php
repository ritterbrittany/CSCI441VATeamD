<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Mock patient data (for demo purposes)
$patients = [
    ['id' => 1, 'name' => 'John Doe'],
    ['id' => 2, 'name' => 'Jane Smith'],
    ['id' => 3, 'name' => 'Samuel Green'],
    // This is to be replaced with the actual database ** 
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List</title>
    <link rel="stylesheet" href="../css/styles.css"> 
</head>
<body>
    <header>
        <h1>Patient List</h1>
    </header>

    <section class="patient-list-container">
        <h2>Select a patient to update their medical records</h2>
        <ul class="patient-list">
            <?php foreach ($patients as $patient): ?>
                <li><a href="../backend/updatemedicalrecord.php?id=<?php echo $patient['id']; ?>"><?php echo $patient['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>
        <li><a href="../backend/dashboard.php">Back to Dashboard</a>
    </section>
</body>
</html>