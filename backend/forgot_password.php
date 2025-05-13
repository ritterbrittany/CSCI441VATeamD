  // written by: Brittany Ritter 
  // tested by: Brandon Williams, Christopher Pham, Riley Weaver
  // debugged by:
  // etc.

<?php
// forgot-password.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Need users table in your database with an 'email' column
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=username_db', 'root', ''); 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if email exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate a reset token (you can use something more secure in a real system)
            $reset_token = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare("UPDATE users SET reset_token = :reset_token WHERE email = :email");
            $stmt->execute(['reset_token' => $reset_token, 'email' => $email]);

            // Send an email with the reset token
            $reset_link = "http://yourdomain.com/reset-password.php?token=$reset_token";
            $subject = "Password Reset Request";
            $message = "Click on the link to reset your password: $reset_link";
            $headers = "From: no-reply@yourdomain.com";

            // Send the email
            if (mail($email, $subject, $message, $headers)) {
                $success_message = "An email has been sent to reset your password.";
            } else {
                $error_message = "There was an error sending the email. Please try again later.";
            }
        } else {
            $error_message = "No account found with that email address.";
        }
    } catch (PDOException $e) {
        $error_message = "Error connecting to the database: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/styles.css"> 
</head>
<body>
    <header>
        <h1>Fort Hays Hospital EMR</h1>
    </header>

    <div class="forgot-password-container">
        <h2>Forgot Password</h2>

        <!-- Show success or error messages -->
        <?php if (isset($success_message)): ?>
            <p style="color: green;"><?= $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p style="color: red;"><?= $error_message; ?></p>
        <?php endif; ?>

        <form action="forgot-password.php" method="POST">
            <label for="email">Enter your registered email</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email address">

            <button type="submit">Send Reset Link</button>
        </form>

        <div class="back-to-login">
        <a href="../backend/login.php">Back to Login.</a>
        </div>
    </div>
</body>
</html>
