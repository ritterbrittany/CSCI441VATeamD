<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get patient ID from URL
$patientId = isset($_GET['id']) ? $_GET['id'] : null;

// Mock patient data for demo purposes
$patientData = [
    1 => ['name' => 'John Doe', 'dob' => '1985-05-15', 'gender' => 'Male', 'medications' => 'Aspirin, Metformin', 'notes' => 'Patient is recovering well from recent surgery.'],
    2 => ['name' => 'Jane Smith', 'dob' => '1990-06-20', 'gender' => 'Female', 'medications' => 'Lipitor', 'notes' => 'Undergoing routine check-up.'],
    3 => ['name' => 'Samuel Green', 'dob' => '1980-01-10', 'gender' => 'Male', 'medications' => 'Insulin', 'notes' => 'New diagnosis of Type 2 diabetes.'],
];

// Fetch patient details from the mock data
if ($patientId && isset($patientData[$patientId])) {
    $patient = $patientData[$patientId];
} else {
    // Handle error if no patient found
    echo "Patient not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Medical Record</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Update Medical Record for <?php echo $patient['name']; ?></h1>
    </header>

    <div class="update-record-container">
        <h2>Update Patient Information</h2>
        <!-- Form for updating the patient's data -->
        <form action="updatemedicalrecord.php" method="POST">
            <input type="hidden" name="patient-id" value="<?php echo $patientId; ?>">

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo $patient['name']; ?>" required>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" value="<?php echo $patient['dob']; ?>" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo $patient['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $patient['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo $patient['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>

            <label for="medications">Current Medications</label>
            <textarea id="medications" name="medications" required><?php echo $patient['medications']; ?></textarea>

            <label for="notes">Additional Notes</label>
            <textarea id="notes" name="notes"><?php echo $patient['notes']; ?></textarea>

            <button type="submit">Save Changes</button>
        </form>

        
        <li><a href="http://localhost/SWEPROJECTTEAMD/CSCI441VATeamD/backend/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>