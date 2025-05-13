<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Patient.php';

class PatientIntegrationTest extends TestCase {
    private $db;
    private $patient;

    protected function setUp(): void {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->patient = new Patient($this->db);
    }

    public function testPatientCRUDLifecycle() {
        // Create
        $this->patient->first_name = 'Integration';
        $this->patient->last_name = 'Test';
        $this->patient->date_of_birth = '1980-05-10';
        $this->patient->gender = 'Other';
        $this->patient->email = 'integration.test@example.com';
        $this->patient->phone = '9999999999';
        $this->patient->address = '123 Integration Way';
        $this->patient->city = 'TestCity';
        $this->patient->state = 'TS';
        $this->patient->zip_code = '99999';
        $this->patient->ssn = '999-88-7777';

        $createSuccess = $this->patient->create();
        $this->assertTrue($createSuccess, 'Patient creation failed');

        // Read (Get newly created patient)
        $stmt = $this->patient->read();
        $found = false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['email'] === 'integration.test@example.com') {
                $found = true;
                $this->patient->patient_id = $row['patient_id'];
                break;
            }
        }
        $this->assertTrue($found, 'Created patient not found in read()');

        // Update
        $this->patient->phone = '1112223333';
        $updateSuccess = $this->patient->update();
        $this->assertTrue($updateSuccess, 'Patient update failed');

        // Delete
        $deleteSuccess = $this->patient->delete();
        $this->assertTrue($deleteSuccess, 'Patient delete failed');
    }
}
?>
