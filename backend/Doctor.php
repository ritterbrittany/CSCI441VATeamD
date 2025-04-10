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

// Define the Doctor class
class Doctor {
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all doctors
    public function read() {
        $query = "SELECT doctor_id, first_name, last_name, specialty, email, phone FROM doctors";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;  // Return the PDO statement object (not an array)
    }

    // Fetch a single doctor's detailed information by doctor_id
    public function read_single($doctor_id) {
        $query = "SELECT * FROM doctors WHERE doctor_id = :doctor_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}

// Instantiate the Doctor class
$doctor = new Doctor($pdo);

// Fetch all doctors for the list
$doctors_stmt = $doctor->read();
$doctors = [];
if ($doctors_stmt->rowCount() > 0) {
    while ($row = $doctors_stmt->fetch(PDO::FETCH_ASSOC)) {
        $doctors[] = $row;
    }
}

// Check if `doctor_id` is set in the URL, if so, fetch the doctor's details
$single_doctor = null;
if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];
    $stmt = $doctor->read_single($doctor_id);

    if ($stmt->rowCount() > 0) {
        $single_doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // If no doctor_id, show default doctor details (Dr. Emily Walker)
    $single_doctor = [
        'first_name' => 'Dr. Emily',
        'last_name' => 'Walker',
        'specialty' => 'Cardiology',
        'email' => 'dr.emily.walker@example.com',
        'phone' => '3125550101'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Check if `doctor_id` is in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const doctorId = urlParams.get("doctor_id");

            // If `doctor_id` exists in the URL, update doctor details
            if (doctorId) {
                displayDoctorDetails(doctorId);
            } else {
                displayHardcodedDoctorDetails();  // Default case: show hardcoded doctor details
            }

            // Always display all doctors in the list below the details section
            displayAllDoctors();
        });

        // Fetch the doctor details dynamically from the PHP backend
        function displayDoctorDetails(doctorId) {
            fetch(`Doctor.php?doctor_id=${doctorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        displayDoctorDetailsOnPage(data);
                    }
                })
                .catch(error => console.error('Error fetching doctor data:', error));
        }

        // Function to display doctor details dynamically
        function displayDoctorDetailsOnPage(doctor) {
            document.getElementById('firstName').textContent = doctor.first_name;
            document.getElementById('lastName').textContent = doctor.last_name;
            document.getElementById('specialty').textContent = doctor.specialty;
            document.getElementById('email').textContent = doctor.email;
            document.getElementById('phone').textContent = doctor.phone;
        }

        // Function to display hardcoded doctor details if no `doctor_id` is in the URL
        function displayHardcodedDoctorDetails() {
            const hardcodedDoctor = {
                first_name: "Robert",
                last_name: "Green",
                specialty: "Neurology",
                email: "robert.green@clinic.com",
                phone: "5552345678"
            };
            displayDoctorDetailsOnPage(hardcodedDoctor);
        }

        // Function to display all doctors in the table
        function displayAllDoctors() {
            const doctors = <?php echo json_encode($doctors); ?>;
            let doctorsTableBody = document.getElementById('doctorsTableBody');
            doctorsTableBody.innerHTML = ''; // Clear the existing rows

            if (doctors.length > 0) {
                doctors.forEach(function (doctor) {
                    let row = document.createElement('tr');
                    let nameCell = document.createElement('td');
                    nameCell.textContent = doctor.first_name + ' ' + doctor.last_name;

                    let actionCell = document.createElement('td');
                    let link = document.createElement('a');
                    link.href = `Doctor.php?doctor_id=${doctor.doctor_id}`;  // Link to view doctor details
                    link.textContent = 'View Details';
                    link.classList.add('btn-view');
                    actionCell.appendChild(link);

                    row.appendChild(nameCell);
                    row.appendChild(actionCell);
                    doctorsTableBody.appendChild(row);
                });
            } else {
                doctorsTableBody.innerHTML = '<tr><td colspan="2">No doctors found.</td></tr>';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Doctor Details</h1>
    </header>

    <section id="doctorDetails" class="dashboard-container">
        <h2>Doctor Information</h2>
        <p>Loading doctor details...</p>
        <div id="doctorInfo">
            <p><strong>First Name:</strong> <span id="firstName"></span></p>
            <p><strong>Last Name:</strong> <span id="lastName"></span></p>
            <p><strong>Specialty:</strong> <span id="specialty"></span></p>
            <p><strong>Email:</strong> <span id="email"></span></p>
            <p><strong>Phone:</strong> <span id="phone"></span></p>
        </div>
    </section>

    <section class="doctor-list-container dashboard-container">
        <h2>All Doctors</h2>
        <table class="patient-table">
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="doctorsTableBody">
                <!-- Doctor list will be loaded here dynamically via JavaScript -->
            </tbody>
        </table>
    </section>

    <footer>
        <a href="Doctor.php" class="btn-back">Back to Doctors List</a>
    </footer>
</body>
</html>
