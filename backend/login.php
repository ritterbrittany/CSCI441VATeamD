  // written by: Brandon Williams,
  // tested by: Brittany Ritter, Christopher Pham, Riley Weaver
  // debugged by:
  // etc.

<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection with error handling
try {
    $pdo = new PDO('pgsql:host=dpg-cvqn0he3jp1c73dsfnvg-a.ohio-postgres.render.com;port=5432;dbname=emr_platform', 'emr_platform_user', 'rBirGywJYnVMuJHFFuc8pYvTJIyrJXik');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Store session data
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid credentials! Please try again.";
    }
}
?>

<!-- HTML for the login page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMR Login</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Fort Hays Hospital EMR</h1>
    </header>

    <div class="login-container">
        <h2>Login to EMR</h2>

        <!-- Show error message if credentials are invalid -->
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?= $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Enter your username">
    
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">

            <button type="submit">Login</button>

            <div class="forgot-password">
            <a href="../backend/forgot_password.php">Forgot Password?</a>
            </div>
        </form>

        <!-- Display success or failure message -->
        <div id="login-message" class="message"></div>
    </div>

    <script>
        // Check URL parameters for error messages
        const params = new URLSearchParams(window.location.search);
        if (params.has('error')) {
            const errorMsg = params.get('error');
            if (errorMsg === 'invalid') {
                document.getElementById('login-message').innerText = 'Invalid username or password. Please try again.';
                document.getElementById('login-message').classList.add('red');
            } else if (errorMsg === 'reset') {
                document.getElementById('login-message').innerText = 'Your password has been successfully reset. You can now login with your new password.';
                document.getElementById('login-message').classList.add('green');
            }
        }
    </script>
</body>
</html>
