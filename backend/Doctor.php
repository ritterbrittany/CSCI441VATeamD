<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once '../Database.php';
$database = new Database();
$pdo = $database->connect();

// Doctor class
class Doctor {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function read() {
        $stmt = $this->pdo->prepare("SELECT doctor_id, first_name, last_name, specialty FROM doctors");
        $stmt->execute();
        return $stmt;
    }

    public function read_single($doctor_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM doctors WHERE doctor_id = :doctor_id");
        $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO doctors (first_name, last_name, specialty, email, phone)
                                     VALUES (:first_name, :last_name, :specialty, :email, :phone)");
        return $stmt->execute([ 
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':specialty' => $data['specialty'],
            ':email' => $data['email'],
            ':phone' => $data['phone']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM doctors WHERE doctor_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

$doctor = new Doctor($pdo);

// Handle form submission to add a doctor
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name'])) {
    $success = $doctor->create($_POST);
    if ($success) {
        $message = "Doctor added successfully!";
        header("Refresh:0"); // Refresh to show the new doctor in the list
    } else {
        $message = "Failed to add doctor.";
    }
}

// Handle doctor deletion through POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && $_POST['delete'] === 'true' && isset($_POST['doctor_id'])) {
    $doctor_id = $_POST['doctor_id'];
    $success = $doctor->delete($doctor_id);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Doctor deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete doctor']);
    }
    exit();  // Ensure the script stops here and doesn't process the rest
}

$doctors_stmt = $doctor->read();
$doctors = $doctors_stmt->fetchAll(PDO::FETCH_ASSOC);

// Single doctor view
$single_doctor = null;
if (isset($_GET['doctor_id'])) {
    $stmt = $doctor->read_single($_GET['doctor_id']);
    $single_doctor = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        // Delete doctor function
        function deleteDoctor(doctorId) {
            if (!confirm("Are you sure you want to delete this doctor?")) return;

            fetch('Doctor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `doctor_id=${doctorId}&delete=true`
            })
            .then(res => res.json())  // Ensure we parse the response as JSON
            .then(data => {
                if (data.success) {
                    alert("Doctor deleted successfully.");
                    location.reload();  // Reload the page to reflect changes
                } else {
                    alert("Failed to delete doctor: " + data.message);
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                alert("An error occurred while deleting.");
            });
        }

        // Filter doctors by name
        function filterDoctors() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#doctorsTableBody tr');
            rows.forEach(row => {
                const name = row.querySelector('td').textContent.toLowerCase();
                row.style.display = name.includes(input) ? '' : 'none';
            });
        }
    </script>
</head>
<body>
    <header>
        <h1>Doctor Details</h1>
    </header>

    <section class="dashboard-container">
        <h2>Doctor Information</h2>
        <div>
            <?php if ($single_doctor): ?>
                <p><strong>First Name:</strong> <?= htmlspecialchars($single_doctor['first_name']) ?></p>
                <p><strong>Last Name:</strong> <?= htmlspecialchars($single_doctor['last_name']) ?></p>
                <p><strong>Specialty:</strong> <?= htmlspecialchars($single_doctor['specialty']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($single_doctor['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($single_doctor['phone']) ?></p>
            <?php else: ?>
                <p>No doctor selected.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-container">
        <h2>All Doctors</h2>
        <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterDoctors()" class="search-bar">
        <table class="patient-table">
            <thead>
                <tr><th>Name</th><th>Specialty</th><th>Actions</th></tr>
            </thead>
            <tbody id="doctorsTableBody">
                <?php foreach ($doctors as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['first_name']) . ' ' . htmlspecialchars($d['last_name']) ?></td>
                    <td><?= htmlspecialchars($d['specialty']) ?></td>
                    <td>
                        <a href="?doctor_id=<?= $d['doctor_id'] ?>" class="btn-view">View Details</a>
                        <button onclick="deleteDoctor(<?= $d['doctor_id'] ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="dashboard-container">
        <h2>Add Doctor</h2>
        <?php if ($message): ?>
            <p style="color: green;"><?= $message ?></p>
        <?php endif; ?>
        <form method="POST" class="add-patient-form">
            <input name="first_name" required placeholder="First Name">
            <input name="last_name" required placeholder="Last Name">
            <input name="specialty" required placeholder="Specialty">
            <input name="email" type="email" placeholder="Email">
            <input name="phone" placeholder="Phone">
            <button type="submit">Add Doctor</button>
        </form>
    </section>

    <footer>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </footer>
</body>
</html>
