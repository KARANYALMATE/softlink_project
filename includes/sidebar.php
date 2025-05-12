<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional config include
if (!isset($BASE_URL)) {
    @include_once $_SERVER['DOCUMENT_ROOT'] . '/softlink_project/config/config.php';
}

// Get role from session
$role = $_SESSION['role'] ?? null;
?>

<style>
    .sidebar {
        height: 100%;
        width: 240px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: rgb(14, 15, 15);
        padding-top: 20px;
        z-index: 1000;
    }

    .sidebar h2 {
        color: white;
        text-align: center;
        font-size: 24px;
        padding: 10px;
        margin: 0;
    }

    .sidebar a {
        padding: 15px 25px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background-color: #1ABC9C;
    }
</style>

<?php if ($role === 'admin'): ?>
    <div class="sidebar">
        <h2>STPI Admin</h2>
        <a href="../admin/admin_dashboard.php">🏠 Home</a>
        <a href="../bandwidth/add_client.php"> Add Clients </a>
        <a href="../bandwidth/manage_clients.php"> Manage Clients </a>
        <a href="../bandwidth/add_bandwidth.php">➕ Bandwidth Form</a>
        <a href="../bandwidth/view_bandwidth.php">📊 View Data</a>
        <a href="../admin/manage_users.php">👥 Users</a>
        <a href="../bandwidth/view_history.php">📜 View History</a>
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>

<?php elseif ($role === 'user'): ?>
    <div class="sidebar">
        <h2>Branch Panel</h2>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="branch_bandwidth.php">➕ form</a>
        <a href="view_my_data.php">📊 View My Data</a>
        <a href="user_add_client.php">📊 View My Data</a>
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>
<?php endif; ?>
