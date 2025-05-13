Integration Tests for EMR System
===============================

This directory contains end-to-end integration tests for the EMR system, verifying that multiple components (DB, model classes) work together correctly.

File:
-----
- PatientIntegrationTest.php : Tests create-read-update-delete lifecycle for a Patient

How to Run:
-----------
1. Make sure your local database is running and accessible.
2. Ensure you have PHPUnit installed.
3. Run the integration test using:

    phpunit integration_tests/PatientIntegrationTest.php

Expected Output:
----------------
You should see a report showing the patient lifecycle (create, read, update, delete) passes successfully.

Notes:
------
- These tests use real database entries. Ensure your test DB is separate from production.
- All test data will be created and cleaned up automatically.

