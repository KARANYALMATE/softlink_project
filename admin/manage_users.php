<?php
session_start();

// Only show sidebar if admin
$isAdminPage = true;

// DB connection
require_once '../config/db.php';

// Fetch users
$query = "SELECT * FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->get_result();

// Delete user
if (isset($_POST['delete_user_id'])) {
    $user_id = $_POST['delete_user_id'];

    $delete_query = "DELETE FROM users WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $user_id);
    if ($delete_stmt->execute()) {
        $delete_success = "User deleted successfully.";
    } else {
        $delete_error = "Error deleting user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - STPI Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }
        .content {
            margin-left: 240px; /* Make room for sidebar */
            padding: 40px;
        }
        .dashboard {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #2c3e50;
            color: white;
        }
        .btn-delete, .btn-edit, .btn-create {
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 4px 0;
        }
        .btn-delete {
            background-color: #e74c3c;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .btn-edit {
            background-color: #f39c12;
        }
        .btn-edit:hover {
            background-color: #e67e22;
        }
        .btn-create {
            background-color: #3498db;
        }
        .btn-create:hover {
            background-color: #2980b9;
        }
    </style>

    <!-- AJAX Script to handle delete dynamically -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $(".btn-delete").click(function(e) {
                e.preventDefault();  // Prevent form submission

                var userId = $(this).data("id");  // Get the user ID from data-id attribute

                // Confirm before deleting
                if (confirm("Are you sure you want to delete this user?")) {
                    $.ajax({
                        url: 'delete_user.php',
                        type: 'GET',
                        data: { id: userId },  // Send user ID to the PHP file
                        success: function(response) {
                            if (response === "success") {
                                $("#user-" + userId).fadeOut();  // Remove the row on success
                            } else {
                                alert("Error deleting user!");
                            }
                        },
                        error: function() {
                            alert("An error occurred while processing the request.");
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>

<?php include '../includes/sidebar.php'; ?> <!-- ✅ Sidebar logic here -->

<div class="content">
    <div class="dashboard">
        <h2>👥 Manage Users</h2>

        <!-- Success or Error Messages -->
        <?php if (isset($delete_success)): ?>
            <p style="color: green;"><?= $delete_success ?></p>
        <?php elseif (isset($delete_error)): ?>
            <p style="color: red;"><?= $delete_error ?></p>
        <?php endif; ?>

        <!-- Add User Button -->
        <a href="create_user.php" class="btn-create">➕ Create New User</a>

        <!-- Users Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr id="user-<?= $user['user_id'] ?>">
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td><?= $user['branch_name'] ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn-edit">Edit</a>
                            <button type="button" class="btn-delete" data-id="<?= $user['user_id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
