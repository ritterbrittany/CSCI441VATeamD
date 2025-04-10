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

// Define the MedicalRecord class
class MedicalRecord {
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all medical records
    public function read() {
        $query = "SELECT id, appointment_id, notes, created_at FROM medical_records";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;  // Return the PDO statement object (not an array)
    }

    // Fetch a single medical record's detailed information by id
    public function read_single($id) {
        $query = "SELECT * FROM medical_records WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}

// Instantiate the MedicalRecord class
$medicalRecord = new MedicalRecord($pdo);

// Fetch all medical records for the list
$medical_records_stmt = $medicalRecord->read();
$medical_records = [];
if ($medical_records_stmt->rowCount() > 0) {
    while ($row = $medical_records_stmt->fetch(PDO::FETCH_ASSOC)) {
        $medical_records[] = $row;
    }
}

// Check if `id` is set in the URL, if so, fetch the medical record's details
$single_medical_record = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $medicalRecord->read_single($id);

    if ($stmt->rowCount() > 0) {
        $single_medical_record = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // If no id, show default message
    $single_medical_record = [
        'appointment_id' => 'N/A',
        'notes' => 'No notes available',
        'created_at' => 'N/A'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Check if `id` is in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const recordId = urlParams.get("id");

            // If `id` exists in the URL, update the medical record details
            if (recordId) {
                displayMedicalRecordDetails(recordId);
            } else {
                displayDefaultMedicalRecordDetails();  // Default case: show a hardcoded message
            }

            // Always display all medical records in the list below the details section
            displayAllMedicalRecords();
        });

        // Fetch the medical record details dynamically from the PHP backend
        function displayMedicalRecordDetails(recordId) {
            fetch(`MedicalRecord.php?id=${recordId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        displayMedicalRecordDetailsOnPage(data);
                    }
                })
                .catch(error => console.error('Error fetching medical record data:', error));
        }

        // Function to display medical record details dynamically
        function displayMedicalRecordDetailsOnPage(record) {
            document.getElementById('appointmentId').textContent = record.appointment_id;
            document.getElementById('notes').textContent = record.notes;
            document.getElementById('createdAt').textContent = record.created_at;
        }

        // Function to display default medical record details if no `id` is in the URL
        function displayDefaultMedicalRecordDetails() {
            const hardcodedRecord = {
                appointment_id: "38",
                notes: "Patient reports mild chest pain. Further tests and consultations recommended. Refer to cardiologist.",
                created_at: "2025-04-03 16:45:00"
            };
            displayMedicalRecordDetailsOnPage(hardcodedRecord);
        }

        // Function to display all medical records in the table
        function displayAllMedicalRecords() {
            const medicalRecords = <?php echo json_encode($medical_records); ?>;
            let medicalRecordsTableBody = document.getElementById('medicalRecordsTableBody');
            medicalRecordsTableBody.innerHTML = ''; // Clear the existing rows

            if (medicalRecords.length > 0) {
                medicalRecords.forEach(function (record) {
                    let row = document.createElement('tr');
                    let appointmentCell = document.createElement('td');
                    appointmentCell.textContent = record.appointment_id;

                    let actionCell = document.createElement('td');
                    let link = document.createElement('a');
                    link.href = `MedicalRecord.php?id=${record.id}`;  // Link to view medical record details
                    link.textContent = 'View Details';
                    link.classList.add('btn-view');
                    actionCell.appendChild(link);

                    row.appendChild(appointmentCell);
                    row.appendChild(actionCell);
                    medicalRecordsTableBody.appendChild(row);
                });
            } else {
                medicalRecordsTableBody.innerHTML = '<tr><td colspan="2">No medical records found.</td></tr>';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Medical Record Details</h1>
    </header>

    <section id="medicalRecordDetails" class="dashboard-container">
        <h2>Medical Record Information</h2>
        <p>Loading medical record details...</p>
        <div id="medicalRecordInfo">
            <p><strong>Appointment ID:</strong> <span id="appointmentId"></span></p>
            <p><strong>Notes:</strong> <span id="notes"></span></p>
            <p><strong>Created At:</strong> <span id="createdAt"></span></p>
        </div>
    </section>

    <section class="medical-records-list-container dashboard-container">
        <h2>All Medical Records</h2>
        <table class="medical-records-table">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="medicalRecordsTableBody">
                <!-- Medical records list will be loaded here dynamically via JavaScript -->
            </tbody>
        </table>
    </section>

    <footer>
        <a href="MedicalRecord.php" class="btn-back">Back to Medical Records List</a>
    </footer>
</body>
</html>
