<?php
session_start();

// If the user is not logged in, redirect to login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the Database class
require_once '../Database.php';

// Define the Prescription class directly in this file
class Prescription {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function read() {
        $query = "SELECT id, appointment_id, medication_name, dosage, instructions FROM prescriptions";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function read_single($id) {
        $query = "SELECT * FROM prescriptions WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}

// Set up database and class
$database = new Database();
$pdo = $database->connect();
$prescription = new Prescription($pdo);

// Handle JSON request for single prescription
if (isset($_GET['id']) && isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
    $id = $_GET['id'];
    $stmt = $prescription->read_single($id);
    if ($stmt->rowCount() > 0) {
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        echo json_encode(['message' => 'Prescription not found']);
    }
    exit();
}

// Get all prescriptions for HTML view
$prescriptions_stmt = $prescription->read();
$prescriptions = $prescriptions_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            const prescriptionId = urlParams.get("id");

            if (prescriptionId) {
                fetch(`Prescription.php?id=${prescriptionId}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && !data.message) {
                        displayPrescription(data);
                    } else {
                        displayDefault();
                    }
                })
                .catch(() => displayDefault());
            } else {
                displayDefault();
            }

            const prescriptions = <?php echo json_encode($prescriptions); ?>;
            const tbody = document.getElementById('prescriptionsTableBody');
            prescriptions.forEach(p => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${p.appointment_id}</td>
                    <td><a href="Prescription.php?id=${p.id}" class="btn-view">View Details</a></td>
                `;
                tbody.appendChild(row);
            });
        });

        function displayPrescription(data) {
            document.getElementById('appointmentId').textContent = data.appointment_id;
            document.getElementById('medicationName').textContent = data.medication_name;
            document.getElementById('dosage').textContent = data.dosage;
            document.getElementById('instructions').textContent = data.instructions;
        }

        function displayDefault() {
            document.getElementById('appointmentId').textContent = "N/A";
            document.getElementById('medicationName').textContent = "None";
            document.getElementById('dosage').textContent = "N/A";
            document.getElementById('instructions').textContent = "No instructions available";
        }
    </script>
</head>
<body>
    <header>
        <h1>Prescription Details</h1>
    </header>

    <section class="dashboard-container">
        <h2>Prescription Information</h2>
        <div id="prescriptionInfo">
            <p><strong>Appointment ID:</strong> <span id="appointmentId"></span></p>
            <p><strong>Medication Name:</strong> <span id="medicationName"></span></p>
            <p><strong>Dosage:</strong> <span id="dosage"></span></p>
            <p><strong>Instructions:</strong> <span id="instructions"></span></p>
        </div>
    </section>

    <section class="dashboard-container">
        <h2>All Prescriptions</h2>
        <table class="prescription-table">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="prescriptionsTableBody"></tbody>
        </table>
    </section>

    <footer>
        <a href="Dashboard.php" class="btn-back">Back to Dashboard</a>
    </footer>
</body>
</html>
