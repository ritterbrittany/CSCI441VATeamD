# CSCI441VATeamD

Github URL for CSCI441 Project:
Implementation of A Healthcare Management System
"Creating an electronic medical record" 

Team Members: 
Chris Pham 
Brandon Williams 
Riley Weaver
Brittany Ritter 

This is our final project file. 
To run the code: 

This code was ran locally on our PC's. The code was ran with XXAMP downloaded and running in the background. The the url of the code was : http://localhost/SWEPROJECTTEAMD/CSCI441VATeamD/backend/login.php when I ran this code. The file path always started with the backend/login.php file. The database was we used was created on Render.com and was an active database which is required to run the code. 
The login for in order to get to the next screen in our project consisted of two seperate logins 
user name: admin 
password: hashedpassword123
or 
user name: doctor_alice
password: hashedpassword456
once logged in the user can access each screen like we had in the presentation. 

Tests that can be run: 
Adding a patient in the "Manage Patients" page
Scroll to the bottom of the page -> Type in the designated fields (first name, last name, DOB, email, etc) -> click Add Patient
Once the patient is added you will see the patient in the database of names. 
You can also delete this patient with the Delete button. Once deleted the patient will no longer be available for viewing. 
The same actions of adding and deleting are capable for the "Manage Doctors" page as well. 

CSCI441VATEAMD
---+-> backend
   |
   +------------ PQC_Implementation ( written, tested, and debugged by Brandon Williams)
   +------------ dashboard.php, Diagnosis.php, Doctor.php, forgot_password.php, hashPassword.php, logged_out.php, login.php, logout.php, MedicalRecord.php, Patient.php, Prescription.php, rolemanangement.php (written, tested, and debugged by Brittany Ritter)
---+-> css
   |
   +----- styles.css (written, tested, and debugged by Brittany Ritter)
----+> diagnosis , doctors, medicalrecords, patients, prescriptions  
    |
    + create.php, delete.php, index.php, read_single.php, read.php, update.php (written, tested, and debugged by Chris Pham) 
-----> Database.php (written by Chris Pham, tested and debugged by Brittany Ritter) 
    
