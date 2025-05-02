<?php
session_start();

// If the user is not logged in or is not an admin, redirect to login page
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include the Database class for the connection
require_once '../Database.php'; // Adjust the path if necessary

// Create a Database object and get the connection
$database = new Database();
$pdo = $database->connect();

// Fetch all users from the database
$query = "SELECT user_id, username, role FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Handle role update when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['new_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];

    // Update the user's role in the database
    $updateQuery = "UPDATE users SET role = :new_role WHERE user_id = :user_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute(['new_role' => $newRole, 'user_id' => $userId]);

    // Redirect to reload the page and see the changes
    header("Location: RoleManagementPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="role-management-container">
        <header>
            <div class="header-left">
                <a href="/dashboard" class="back-to-dashboard-btn">Back to Dashboard</a> <!-- Back to Dashboard link -->
            </div>
            <h1>Role Management</h1>
        </header>

        <section class="role-list">
            <h2>Manage User Roles</h2>
            
            <input type="text" id="searchRoles" placeholder="Search Roles..." onkeyup="searchRoles()" class="search-bar">
            
            
            <button class="add-role-btn" onclick="openRoleForm()">Add New Role</button>
            
          
            <div id="roleForm" class="role-form" style="display: none;">
                <h3>Create or Edit Role</h3>
                <form id="roleFormElement" action="/submit-role" method="POST">
                    <label for="roleName">Role Name:</label>
                    <input type="text" id="roleName" name="roleName" required placeholder="Enter role name">
                    
                    <label for="roleDescription">Description:</label>
                    <input type="text" id="roleDescription" name="roleDescription" required placeholder="Enter role description">

                    <label for="rolePermissions">Permissions:</label>
                    <select id="rolePermissions" name="permissions" multiple>
                        <option value="viewRecords">View Records</option>
                        <option value="updateRecords">Update Records</option>
                        <option value="manageRoles">Manage Roles</option>
                        <option value="viewReports">View Reports</option>
                    </select>

                    <button type="submit">Save Role</button>
                </form>
            </div>
            
           
            <table class="role-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="roleTableBody">
                    <?php
                    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                        echo "<td>
                            <form method='POST' action=''>
                                <input type='hidden' name='user_id' value='" . $user['user_id'] . "'>
                                <select name='new_role'>
                                    <option value='admin' " . ($user['role'] == 'admin' ? 'selected' : '') . ">Admin</option>
                                    <option value='doctor' " . ($user['role'] == 'doctor' ? 'selected' : '') . ">Doctor</option>
                                </select>
                                <button type='submit'>Update Role</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        // Function to filter roles dynamically as the user types in the search bar
        function searchRoles() {
            const input = document.getElementById('searchRoles');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('roleTableBody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let matchFound = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        matchFound = true;
                        break;
                    }
                }
                if (matchFound) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    </script>
    <li><a href="../backend/dashboard.php">Back to Dashboard</a></li>
</body>
</html>
