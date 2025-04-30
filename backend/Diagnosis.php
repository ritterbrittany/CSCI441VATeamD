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

// Diagnosis class
class Diagnosis {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function read() {
        $stmt = $this->pdo->prepare("SELECT id, diagnosis_code FROM diagnoses");
        $stmt->execute();
        return $stmt;
    }

    public function read_single($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM diagnoses WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO diagnoses (appointment_id, diagnosis_code, description) VALUES (:appointment_id, :diagnosis_code, :description)");
        return $stmt->execute([
            ':appointment_id' => $data['appointment_id'],
            ':diagnosis_code' => $data['diagnosis_code'],
            ':description' => $data['description']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM diagnoses WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

$diagnosis = new Diagnosis($pdo);

// Handle form submission for adding a diagnosis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['diagnosis_code'])) {
    $success = $diagnosis->create($_POST);
    if ($success) {
        header("Location: Diagnosis.php");
        exit();
    } else {
        $message = "Failed to add diagnosis.";
    }
}

// Get all diagnoses
$diagnoses_stmt = $diagnosis->read();
$diagnoses = $diagnoses_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected diagnosis if available
$single_diagnosis = null;
if (isset($_GET['id'])) {
    $stmt = $diagnosis->read_single($_GET['id']);
    $single_diagnosis = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Diagnosis Details</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Diagnosis Details</h1>
</header>

<section class="dashboard-container">
    <h2>Diagnosis Information</h2>
    <?php if ($single_diagnosis): ?>
        <p><strong>Appointment ID:</strong> <?= htmlspecialchars($single_diagnosis['appointment_id']) ?></p>
        <p><strong>Diagnosis Code:</strong> <?= htmlspecialchars($single_diagnosis['diagnosis_code']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($single_diagnosis['description']) ?></p>
    <?php else: ?>
        <p>No diagnosis selected.</p>
    <?php endif; ?>
</section>

<section class="dashboard-container">
    <h2>All Diagnoses</h2>
    <table class="patient-table">
        <thead>
            <tr><th>Code</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($diagnoses as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['diagnosis_code']) ?></td>
                <td>
                    <a href="?id=<?= $d['id'] ?>" class="btn-view">View Details</a>
                    <!-- Optional delete button -->
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="dashboard-container">
    <h2>Add Diagnosis</h2>
    <?php if (!empty($message)): ?>
        <p style="color: red;"><?= $message ?></p>
    <?php endif; ?>
    <form method="POST">
        <input name="appointment_id" required placeholder="Appointment ID">
        <input name="diagnosis_code" required placeholder="Diagnosis Code">
        <input name="description" required placeholder="Description">
        <button type="submit">Add Diagnosis</button>
    </form>
</section>

<footer>
    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
</footer>
</body>
</html>
