<?php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the Database class for the connection
require_once '../Database.php'; // Adjust the path if necessary

// Create a Database object and get the connection
$database = new Database();
$pdo = $database->connect();

// Define the Diagnosis class
class Diagnosis {
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all diagnoses
    public function read() {
        $query = "SELECT id, appointment_id, diagnosis_code, description FROM diagnoses";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;  // Return the PDO statement object (not an array)
    }

    // Fetch a single diagnosis's detailed information by id
    public function read_single($id) {
        $query = "SELECT * FROM diagnoses WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}

// Instantiate the Diagnosis class
$diagnosis = new Diagnosis($pdo);

// Fetch all diagnoses for the list
$diagnoses_stmt = $diagnosis->read();
$diagnoses = [];
if ($diagnoses_stmt->rowCount() > 0) {
    while ($row = $diagnoses_stmt->fetch(PDO::FETCH_ASSOC)) {
        $diagnoses[] = $row;
    }
}

// Check if `id` is set in the URL, if so, fetch the diagnosis details
$single_diagnosis = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $diagnosis->read_single($id);

    if ($stmt->rowCount() > 0) {
        $single_diagnosis = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // Default: Show a hardcoded diagnosis
    $single_diagnosis = [
        'appointment_id' => '12345',
        'diagnosis_code' => 'A123',
        'description' => 'Example description for a diagnosis'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosis Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Check if `id` is in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const diagnosisId = urlParams.get("id");

            // If `id` exists in the URL, update diagnosis details
            if (diagnosisId) {
                displayDiagnosisDetails(diagnosisId);
            } else {
                displayHardcodedDiagnosisDetails();  // Default case: show hardcoded diagnosis details
            }

            // Always display all diagnoses in the list below the details section
            displayAllDiagnoses();
        });

        // Fetch diagnosis details dynamically from the PHP backend
        function displayDiagnosisDetails(diagnosisId) {
            fetch(`Diagnosis.php?id=${diagnosisId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        displayDiagnosisDetailsOnPage(data);
                    }
                })
                .catch(error => console.error('Error fetching diagnosis data:', error));
        }

        // Function to display diagnosis details dynamically
        function displayDiagnosisDetailsOnPage(diagnosis) {
            document.getElementById('appointmentId').textContent = diagnosis.appointment_id;
            document.getElementById('diagnosisCode').textContent = diagnosis.diagnosis_code;
            document.getElementById('description').textContent = diagnosis.description;
        }

        // Function to display hardcoded diagnosis details if no `id` is in the URL
        function displayHardcodedDiagnosisDetails() {
            const hardcodedDiagnosis = {
                appointment_id: "38",
                diagnosis_code: "B02",
                description: "Chickenpox"
            };
            displayDiagnosisDetailsOnPage(hardcodedDiagnosis);
        }

        // Function to display all diagnoses in the table
        function displayAllDiagnoses() {
            const diagnoses = <?php echo json_encode($diagnoses); ?>;
            let diagnosesTableBody = document.getElementById('diagnosesTableBody');
            diagnosesTableBody.innerHTML = ''; // Clear the existing rows

            if (diagnoses.length > 0) {
                diagnoses.forEach(function (diagnosis) {
                    let row = document.createElement('tr');
                    let codeCell = document.createElement('td');
                    codeCell.textContent = diagnosis.diagnosis_code;

                    let actionCell = document.createElement('td');
                    let link = document.createElement('a');
                    link.href = `Diagnosis.php?id=${diagnosis.id}`;  // Link to view diagnosis details
                    link.textContent = 'View Details';
                    link.classList.add('btn-view');
                    actionCell.appendChild(link);

                    row.appendChild(codeCell);
                    row.appendChild(actionCell);
                    diagnosesTableBody.appendChild(row);
                });
            } else {
                diagnosesTableBody.innerHTML = '<tr><td colspan="2">No diagnoses found.</td></tr>';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Diagnosis Details</h1>
    </header>

    <section id="diagnosisDetails" class="dashboard-container">
        <h2>Diagnosis Information</h2>
        <p>Loading diagnosis details...</p>
        <div id="diagnosisInfo">
            <p><strong>Appointment ID:</strong> <span id="appointmentId"></span></p>
            <p><strong>Diagnosis Code:</strong> <span id="diagnosisCode"></span></p>
            <p><strong>Description:</strong> <span id="description"></span></p>
        </div>
    </section>

    <section class="diagnosis-list-container dashboard-container">
        <h2>All Diagnoses</h2>
        <table class="patient-table">
            <thead>
                <tr>
                    <th>Diagnosis Code</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="diagnosesTableBody">
                <!-- Diagnosis list will be loaded here dynamically via JavaScript -->
            </tbody>
        </table>
    </section>

    <footer>
        <a href="Diagnosis.php" class="btn-back">Back to Diagnoses List</a>
    </footer>
</body>
</html>

