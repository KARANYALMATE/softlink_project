<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    $_SESSION['username'] = 'admin';  
    $_SESSION['role'] = 'admin';   
}

require_once '../config/db.php'; 

$username = $_SESSION['username'];

$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $adminData = $result->fetch_assoc();
} else {
    $adminData = [
        'username' => $username,
        'role' => $_SESSION['role'],
        'created_at' => 'Not available'
    ];
}

$userQuery = "SELECT * FROM users";
$userStmt = $conn->prepare($userQuery);
$userStmt->execute();
$userResult = $userStmt->get_result();

// Only this line below tells sidebar to load admin version
$isAdminPage = true;
include_once '../includes/sidebar.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Softlink</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }

        .content {
            margin-left: 240px; /* Important to avoid sidebar overlap */
            padding: 40px;
        }

        .dashboard {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 28px;
            text-align: center;
        }

        .user-details {
            font-size: 18px;
            color: #333;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 8px;
            margin-top: 20px;
        }

        .user-details p {
            margin: 10px 0;
        }

        .user-table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }

        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .action-btn {
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            margin: 10px 0;
            background-color: rgb(10, 11, 11);
            display: inline-block;
            transition: 0.3s ease;
        }

        .action-btn:hover {
            background-color: rgb(1, 11, 17);
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="dashboard">
            <h2>📍 Welcome, <?= htmlspecialchars($adminData['username']) ?> (<?= ucfirst($adminData['role']) ?>)</h2>

            <div class="user-details">
                <p><strong>Username:</strong> <?= htmlspecialchars($adminData['username']) ?></p>
                <p><strong>Role:</strong> <?= ucfirst($adminData['role']) ?></p>
                <p><strong>Joined On:</strong> <?= $adminData['created_at'] ?></p>
            </div>

            <h3>All Users:</h3>
            <?php if ($userResult->num_rows > 0): ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $userResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= ucfirst($user['role']) ?></td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>

            <a href="../logout.php" class="action-btn">🚪 Logout</a>
        </div>
    </div>

</body>
</html>
