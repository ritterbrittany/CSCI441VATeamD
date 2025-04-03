<?php
session_start();

// Database connection with error handling
try {
    $pdo = new PDO('mysql:host=localhost;dbname=username_db', 'root', ''); // Adjust this if using a different DB user/password
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to throw exceptions
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
    if ($user && password_verify($password, $user['password'])) {
        // Store session data
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit; // Make sure no further code runs after the redirect
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
    <link rel="stylesheet" href="../css/styles.css">  <!-- Correct path to styles.css -->
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
                <a href="/forgot-password">Forgot Password?</a>
            </div>
        </form>

        <!-- Optional: Display success or failure message (optional) -->
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
