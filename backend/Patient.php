<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once '../Database.php';
$database = new Database();
$pdo = $database->connect();

// Patient class
class Patient {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function read() {
        $stmt = $this->pdo->prepare("SELECT patient_id, first_name, last_name FROM patients");
        $stmt->execute();
        return $stmt;
    }

    public function read_single($patient_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE patient_id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO patients 
            (first_name, last_name, date_of_birth, gender, email, phone, address, city, state, zip_code, ssn)
            VALUES
            (:first_name, :last_name, :date_of_birth, :gender, :email, :phone, :address, :city, :state, :zip_code, :ssn)");
        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':date_of_birth' => $data['date_of_birth'],
            ':gender' => $data['gender'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':address' => $data['address'],
            ':city' => $data['city'],
            ':state' => $data['state'],
            ':zip_code' => $data['zip_code'],
            ':ssn' => $data['ssn']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM patients WHERE patient_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

$patient = new Patient($pdo);

// Handle form submission for adding a patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name'])) {
    $success = $patient->create($_POST);
    if ($success) {
        header("Location: Patient.php");
        exit();
    } else {
        $message = "Failed to add patient.";
    }
}

// Get all patients
$patients_stmt = $patient->read();
$patients = $patients_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get specific patient details if needed
$single_patient = null;
if (isset($_GET['patient_id'])) {
    $stmt = $patient->read_single($_GET['patient_id']);
    $single_patient = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function deletePatient(patientId) {
            if (!confirm("Are you sure you want to delete this patient?")) return;

            fetch('../patients/delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `patient_id=${patientId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Patient deleted successfully.");
                    location.reload();
                } else {
                    alert("Failed to delete patient.");
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                alert("An error occurred.");
            });
        }
    </script>
</head>
<body>
    <header>
        <h1>Patient Details</h1>
    </header>

    <section class="dashboard-container">
        <h2>Patient Information</h2>
        <div>
            <?php if ($single_patient): ?>
                <p><strong>First Name:</strong> <?= htmlspecialchars($single_patient['first_name']) ?></p>
                <p><strong>Last Name:</strong> <?= htmlspecialchars($single_patient['last_name']) ?></p>
                <p><strong>DOB:</strong> <?= htmlspecialchars($single_patient['date_of_birth']) ?></p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($single_patient['gender']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($single_patient['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($single_patient['phone']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($single_patient['address']) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($single_patient['city']) ?></p>
                <p><strong>State:</strong> <?= htmlspecialchars($single_patient['state']) ?></p>
                <p><strong>Zip:</strong> <?= htmlspecialchars($single_patient['zip_code']) ?></p>
                <p><strong>SSN:</strong> <?php echo $single_patient['ssn']; ?></p>
            <?php else: ?>
                <p>No patient selected.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-container">
        <h2>All Patients</h2>
        <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterPatients()" class="search-bar">
        <table class="patient-table">
            <thead>
                <tr><th>Name</th><th>Actions</th></tr>
            </thead>
            <tbody id="patientsTableBody">
                <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['first_name']) . ' ' . htmlspecialchars($p['last_name']) ?></td>
                    <td>
                        <a href="?patient_id=<?= $p['patient_id'] ?>" class="btn-view">View Details</a>
                        <button onclick="deletePatient(<?= $p['patient_id'] ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="dashboard-container">
        <h2>Add Patient</h2>
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?= $message ?></p>
        <?php endif; ?>
        <form method="POST" class="add-patient-form">
            <input name="first_name" required placeholder="First Name">
            <input name="last_name" required placeholder="Last Name">
            <input type="date" name="date_of_birth" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
            </select>
            <input name="email" type="email" placeholder="Email">
            <input name="phone" placeholder="Phone">
            <input name="address" placeholder="Address">
            <input name="city" placeholder="City">
            <input name="state" placeholder="State">
            <input name="zip_code" placeholder="Zip Code">
            <input name="ssn" placeholder="SSN">
            <button type="submit">Add Patient</button>
        </form>
    </section>

    <footer>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </footer>

    <script>
        function filterPatients() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#patientsTableBody tr');
            rows.forEach(row => {
                const name = row.querySelector('td').textContent.toLowerCase();
                row.style.display = name.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
