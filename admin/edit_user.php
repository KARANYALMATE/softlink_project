<?php
// session_start();
// if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

require_once '../config/db.php';

$errors = [];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT username, role, branch_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    header("Location: manage_users.php");
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $branch_name = $_POST['branch_name'];

    // Basic validation
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($role))     $errors[] = "Role is required.";
    if (empty($branch_name)) $errors[] = "Branch name is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, branch_name = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $role, $branch_name, $user_id);
        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit();
        } else {
            $errors[] = "Error updating user.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
        }
        /* Sidebar */
        .sidebar {
            height: 100%;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            background-color:rgb(17, 17, 17);
            padding-top: 20px;
            padding-left: 20px;
        }
        .sidebar h2 {
            color: white;
            text-align: center;
            font-size: 24px;
            margin: 0;
            padding: 10px;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #1abc9c;
        }
        .content {
            margin-left: 240px;
            padding: 40px;
        }
        .dashboard {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 30px;
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
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-update {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-update:hover {
            background-color: #2980b9;
        }
        /* Error Message */
        .error-message {
            color: red;
            margin-bottom: 20px;
            border: 1px solid #e74c3c;
            padding: 10px;
            background-color: #f9e4e4;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>STPI Dashboard</h2>
    <a href="/softlink_project/admin/admin_dashboard.php">🏠 Home</a>
    <a href="/softlink_project/bandwidth/add_bandwidth.php">➕ Bandwidth Form</a>
    <a href="/softlink_project/bandwidth/view_bandwidth.php">📊 View Data</a>
    <a href="/softlink_project/admin/manage_users.php">👥 Users</a>
    <a href="/softlink_project/logout.php">🚪 Logout</a>
</div>

<!-- Content -->
<div class="content">
    <div class="dashboard">
        <h2>Edit User</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <div class="form-group">
                <label for="branch_name">Branch Name</label>
                <input type="text" id="branch_name" name="branch_name" value="<?= htmlspecialchars($user['branch_name']) ?>" required>
            </div>
            <button type="submit" class="btn-update">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>
