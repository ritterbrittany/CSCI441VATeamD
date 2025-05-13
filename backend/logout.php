  // written by: Brandon Williams,
  // tested by: Brittany Ritter, Christopher Pham, Riley Weaver
  // debugged by:
  // etc.

<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: logged_out.php");
exit();
?>
