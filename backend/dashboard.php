<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the user's role from the session
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMR Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Fort Hays Hospital EMR</h1>
    </header>

    <div class="dashboard-container">
        <h2>Welcome to Your EMR Dashboard</h2>
        <div class="user-info">
            <span>User: <strong><?php echo $_SESSION['username']; ?></strong></span>
            <span>Role: <strong><?php echo $role; ?></strong></span>
            <span>Access: <strong>Full Access</strong></span>
        </div>

        <section class="main-content">
            <h3>Dashboard Overview</h3>
            <p>Welcome, <?php echo $_SESSION['username']; ?>. Here are your quick actions:</p>

            <ul>
                
                <?php if ($role == 'admin'): ?>
                    <!-- Admin specific links -->
                    <li><a href="RoleManagementPage.php">Assign Permissions</a></li>
                <?php endif; ?>

                <!-- Common link for both admin and doctor roles -->
                <li><a href="http://localhost/SWEPROJECTTEAMD/CSCI441VATeamD/backend/patientlistpage.php">View Patient Records</a>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </section>
    </div>
</body>
</html>

