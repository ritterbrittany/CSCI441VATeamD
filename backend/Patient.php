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

// Define the Patient class
class Patient {
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all patients (only first and last names)
    public function read() {
        $query = "SELECT patient_id, first_name, last_name FROM patients";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;  // Return the PDO statement object (not an array)
    }

    // Fetch a single patient's detailed information by patient_id
    public function read_single($patient_id) {
        $query = "SELECT * FROM patients WHERE patient_id = :patient_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}

// Instantiate the Patient class
$patient = new Patient($pdo);

// Fetch all patients for the list
$patients_stmt = $patient->read();
$patients = [];
if ($patients_stmt->rowCount() > 0) {
    while ($row = $patients_stmt->fetch(PDO::FETCH_ASSOC)) {
        $patients[] = $row;
    }
}

// Check if `patient_id` is set in the URL, if so, fetch the patient's details
$single_patient = null;
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];
    $stmt = $patient->read_single($patient_id);

    if ($stmt->rowCount() > 0) {
        $single_patient = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // If no patient_id, show default patient details (Emily Walker)
    $single_patient = [
        'first_name' => 'Emily',
        'last_name' => 'Walker',
        'date_of_birth' => '1988-03-12',
        'gender' => 'Female',
        'email' => 'emily.walker@example.com',
        'phone' => '3125550101',
        'address' => '500 Elm St',
        'city' => 'Chicago',
        'state' => 'IL',
        'zip_code' => '60601'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Check if `patient_id` is in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const patientId = urlParams.get("patient_id");

            // If `patient_id` exists in the URL, update patient details
            if (patientId) {
                displayPatientDetails(patientId);
            } else {
                displayHardcodedPatientDetails();  // Default case: show hardcoded patient details
            }

            // Always display all patients in the list below the details section
            displayAllPatients();
        });

        // Fetch the patient details dynamically from the PHP backend
        function displayPatientDetails(patientId) {
            fetch(`Patient.php?patient_id=${patientId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        displayPatientDetailsOnPage(data);
                    }
                })
                .catch(error => console.error('Error fetching patient data:', error));
        }

        // Function to display patient details dynamically
        function displayPatientDetailsOnPage(patient) {
            document.getElementById('firstName').textContent = patient.first_name;
            document.getElementById('lastName').textContent = patient.last_name;
            document.getElementById('dob').textContent = patient.date_of_birth;
            document.getElementById('gender').textContent = patient.gender;
            document.getElementById('email').textContent = patient.email;
            document.getElementById('phone').textContent = patient.phone;
            document.getElementById('address').textContent = patient.address;
            document.getElementById('city').textContent = patient.city;
            document.getElementById('state').textContent = patient.state;
            document.getElementById('zipCode').textContent = patient.zip_code;
        }

        // Function to display hardcoded patient details if no `patient_id` is in the URL
        function displayHardcodedPatientDetails() {
            const hardcodedPatient = {
                first_name: "Emily",
                last_name: "Walker",
                date_of_birth: "1988-03-12",
                gender: "Female",
                email: "emily.walker@example.com",
                phone: "3125550101",
                address: "500 Elm St",
                city: "Chicago",
                state: "IL",
                zip_code: "60601"
            };
            displayPatientDetailsOnPage(hardcodedPatient);
        }

        // Function to display all patients in the table
        function displayAllPatients() {
            const patients = <?php echo json_encode($patients); ?>;
            let patientsTableBody = document.getElementById('patientsTableBody');
            patientsTableBody.innerHTML = ''; // Clear the existing rows

            if (patients.length > 0) {
                patients.forEach(function (patient) {
                    let row = document.createElement('tr');
                    let nameCell = document.createElement('td');
                    nameCell.textContent = patient.first_name + ' ' + patient.last_name;

                    let actionCell = document.createElement('td');
                    let link = document.createElement('a');
                    link.href = `Patient.php?patient_id=${patient.patient_id}`;  // Link to view patient details
                    link.textContent = 'View Details';
                    link.classList.add('btn-view');
                    actionCell.appendChild(link);

                    row.appendChild(nameCell);
                    row.appendChild(actionCell);
                    patientsTableBody.appendChild(row);
                });
            } else {
                patientsTableBody.innerHTML = '<tr><td colspan="2">No patients found.</td></tr>';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Patient Details</h1>
    </header>

    <section id="patientDetails" class="dashboard-container">
        <h2>Patient Information</h2>
        <p>Loading patient details...</p>
        <div id="patientInfo">
            <p><strong>First Name:</strong> <span id="firstName"></span></p>
            <p><strong>Last Name:</strong> <span id="lastName"></span></p>
            <p><strong>Date of Birth:</strong> <span id="dob"></span></p>
            <p><strong>Gender:</strong> <span id="gender"></span></p>
            <p><strong>Email:</strong> <span id="email"></span></p>
            <p><strong>Phone:</strong> <span id="phone"></span></p>
            <p><strong>Address:</strong> <span id="address"></span></p>
            <p><strong>City:</strong> <span id="city"></span></p>
            <p><strong>State:</strong> <span id="state"></span></p>
            <p><strong>Zip Code:</strong> <span id="zipCode"></span></p>
        </div>
    </section>

    <section class="patient-list-container dashboard-container">
        <h2>All Patients</h2>
        <table class="patient-table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="patientsTableBody">
                <!-- Patient list will be loaded here dynamically via JavaScript -->
            </tbody>
        </table>
    </section>

    <footer>
        <a href="Patient.php" class="btn-back">Back to Patients List</a>
    </footer>
</body>
</html>

