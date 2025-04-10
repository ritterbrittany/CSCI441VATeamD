<?php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the Database class for the connection
require_once '../Database.php'; // Adjust the path if necessary

// Include the Prescription class
require_once '../backend/Prescription.php'; // Adjust the path if necessary

// Create a Database object and get the connection
$database = new Database();
$pdo = $database->connect();

// Define the Prescription class
class Prescription {
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all prescriptions
    public function read() {
        $query = "SELECT id, appointment_id, medication_name, dosage, instructions FROM prescriptions";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;  // Return the PDO statement object (not an array)
    }

    // Fetch a single prescription's detailed information by id
    public function read_single($id) {
        $query = "SELECT * FROM prescriptions WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}

// Instantiate the Prescription class
$prescription = new Prescription($pdo);

// Fetch all prescriptions for the list
$prescriptions_stmt = $prescription->read();
$prescriptions = [];
if ($prescriptions_stmt->rowCount() > 0) {
    while ($row = $prescriptions_stmt->fetch(PDO::FETCH_ASSOC)) {
        $prescriptions[] = $row;
    }
}

// Check if `id` is set in the URL, if so, fetch the prescription's details
$single_prescription = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $prescription->read_single($id);

    if ($stmt->rowCount() > 0) {
        $single_prescription = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // If no id, show default prescription details
    $single_prescription = [
        'appointment_id' => 'N/A',
        'medication_name' => 'None',
        'dosage' => 'N/A',
        'instructions' => 'No instructions available'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Check if `id` is in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const prescriptionId = urlParams.get("id");

            // If `id` exists in the URL, update prescription details
            if (prescriptionId) {
                displayPrescriptionDetails(prescriptionId);
            } else {
                displayHardcodedPrescriptionDetails();  // Default case: show hardcoded prescription details
            }

            // Always display all prescriptions in the list below the details section
            displayAllPrescriptions();
        });

        // Fetch the prescription details dynamically from the PHP backend
        function displayPrescriptionDetails(prescriptionId) {
            fetch(`Prescription.php?id=${prescriptionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        displayPrescriptionDetailsOnPage(data);
                    }
                })
                .catch(error => console.error('Error fetching prescription data:', error));
        }

        // Function to display prescription details dynamically
        function displayPrescriptionDetailsOnPage(prescription) {
            document.getElementById('appointmentId').textContent = prescription.appointment_id;
            document.getElementById('medicationName').textContent = prescription.medication_name;
            document.getElementById('dosage').textContent = prescription.dosage;
            document.getElementById('instructions').textContent = prescription.instructions;
        }

        // Function to display hardcoded prescription details if no `id` is in the URL
        function displayHardcodedPrescriptionDetails() {
            const hardcodedPrescription = {
                appointment_id: "36",
                medication_name: "Amoxicillin",
                dosage: "500mg",
                instructions: "Take one pill every 8 hours."
            };
            displayPrescriptionDetailsOnPage(hardcodedPrescription);
        }

        // Function to display all prescriptions in the table
        function displayAllPrescriptions() {
            const prescriptions = <?php echo json_encode($prescriptions); ?>;
            let prescriptionsTableBody = document.getElementById('prescriptionsTableBody');
            prescriptionsTableBody.innerHTML = ''; // Clear the existing rows

            if (prescriptions.length > 0) {
                prescriptions.forEach(function (prescription) {
                    let row = document.createElement('tr');
                    let appointmentCell = document.createElement('td');
                    appointmentCell.textContent = prescription.appointment_id;

                    let actionCell = document.createElement('td');
                    let link = document.createElement('a');
                    link.href = `Prescription.php?id=${prescription.id}`;  // Link to view prescription details
                    link.textContent = 'View Details';
                    link.classList.add('btn-view');
                    actionCell.appendChild(link);

                    row.appendChild(appointmentCell);
                    row.appendChild(actionCell);
                    prescriptionsTableBody.appendChild(row);
                });
            } else {
                prescriptionsTableBody.innerHTML = '<tr><td colspan="2">No prescriptions found.</td></tr>';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Prescription Details</h1>
    </header>

    <section id="prescriptionDetails" class="dashboard-container">
        <h2>Prescription Information</h2>
        <p>Loading prescription details...</p>
        <div id="prescriptionInfo">
            <p><strong>Appointment ID:</strong> <span id="appointmentId"></span></p>
            <p><strong>Medication Name:</strong> <span id="medicationName"></span></p>
            <p><strong>Dosage:</strong> <span id="dosage"></span></p>
            <p><strong>Instructions:</strong> <span id="instructions"></span></p>
        </div>
    </section>

    <section class="prescription-list-container dashboard-container">
        <h2>All Prescriptions</h2>
        <table class="prescription-table">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="prescriptionsTableBody">
                <!-- Prescription list will be loaded here dynamically via JavaScript -->
            </tbody>
        </table>
    </section>

    <footer>
        <a href="Prescription.php" class="btn-back">Back to Prescriptions List</a>
    </footer>
</body>
</html>
