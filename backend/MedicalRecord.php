<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once '../Database.php';
$database = new Database();
$pdo = $database->connect();

// MedicalRecord class
class MedicalRecord {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function read() {
        $stmt = $this->pdo->prepare("SELECT id, appointment_id FROM medical_records");
        $stmt->execute();
        return $stmt;
    }

    public function read_single($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM medical_records WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO medical_records (appointment_id, notes, created_at) VALUES (:appointment_id, :notes, NOW())");
        return $stmt->execute([
            ':appointment_id' => $data['appointment_id'],
            ':notes' => $data['notes']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM medical_records WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

$record = new MedicalRecord($pdo);

// Handle form submission for adding a record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $success = $record->create($_POST);
    if ($success) {
        header("Location: MedicalRecord.php");
        exit();
    } else {
        $message = "Failed to add medical record.";
    }
}

// Fetch all records
$records_stmt = $record->read();
$records = $records_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch single record if requested
$single_record = null;
if (isset($_GET['id'])) {
    $stmt = $record->read_single($_GET['id']);
    $single_record = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Record Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function deleteRecord(id) {
            if (!confirm("Are you sure you want to delete this record?")) return;

            fetch('../medical_records/delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Record deleted successfully.");
                    location.reload();
                } else {
                    alert("Failed to delete record.");
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
        <h1>Medical Record Details</h1>
    </header>

    <section class="dashboard-container">
        <h2>Record Information</h2>
        <div>
            <?php if ($single_record): ?>
                <p><strong>Appointment ID:</strong> <?= htmlspecialchars($single_record['appointment_id']) ?></p>
                <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($single_record['notes'])) ?></p>
                <p><strong>Created At:</strong> <?= htmlspecialchars($single_record['created_at']) ?></p>
            <?php else: ?>
                <p>No record selected.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-container">
        <h2>All Medical Records</h2>
        <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterRecords()" class="search-bar">
        <table class="medical-records-table">
            <thead>
                <tr><th>Appointment ID</th><th>Actions</th></tr>
            </thead>
            <tbody id="recordsTableBody">
                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['appointment_id']) ?></td>
                    <td>
                        <a href="?id=<?= $r['id'] ?>" class="btn-view">View Details</a>
                        <button onclick="deleteRecord(<?= $r['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="dashboard-container">
        <h2>Add Medical Record</h2>
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?= $message ?></p>
        <?php endif; ?>
        <form method="POST" class="add-record-form">
            <input name="appointment_id" required placeholder="Appointment ID">
            <textarea name="notes" required placeholder="Notes"></textarea>
            <button type="submit">Add Record</button>
        </form>
    </section>

    <footer>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </footer>

    <script>
        function filterRecords() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#recordsTableBody tr');
            rows.forEach(row => {
                const idText = row.querySelector('td').textContent.toLowerCase();
                row.style.display = idText.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
