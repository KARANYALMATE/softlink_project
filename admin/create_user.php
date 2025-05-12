<?php
session_start();


$isAdminPage = true;

require_once '../config/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = $_POST['role'];
    $branch_name = $_POST['branch_name'];

    
    $query = "SELECT user_id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        // Insert new user
        $query = "INSERT INTO users (username, password, role, branch_name, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $username, $password, $role, $branch_name);
        
        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit();
        } else {
            $error = "Error creating user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User - STPI Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }
        .content {
            margin-left: 240px; /* space for sidebar */
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-create {
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn-create:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?> <!-- ✅ Sidebar Include -->

<div class="content">
    <div class="dashboard">
        <h2>Create New User</h2>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <form action="create_user.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="form-group">
                <label for="branch_name">Branch Name</label>
                <input type="text" id="branch_name" name="branch_name" required>
            </div>
            <button type="submit" class="btn-create">Create User</button>
        </form>
    </div>
</div>

</body>
</html>
