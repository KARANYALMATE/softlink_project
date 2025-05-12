<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
include('../auth/check_login.php');
include('../config/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';


// ✅ NEW: Fetch summary stats
$summary = [
    'total_records' => 0,
    'total_bandwidth' => 0,
    'total_clients' => 0,
];

$summaryQuery = "
    SELECT 
        COUNT(*) AS total_records, 
        SUM(bandwidth_mbps) AS total_bandwidth, 
        COUNT(DISTINCT client_id) AS total_clients 
    FROM bandwidth_distribution 
    WHERE user_id = ?
";

$stmt = $conn->prepare($summaryQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $summary = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 15px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        .header {
            background-color: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            width: calc(100% - 240px);
            left: 240px;
            top: 0;
            z-index: 1000;
        }
        .stat-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="branch_bandwidth.php">➕ form</a>
    <a href="view_my_data.php">📊 View My Data</a>
    <a href="user_view_history.php">History</a>
    <a href="user_add_client.php">add client</a>
    <a href="../auth/logout.php">🚪 Logout</a>
</div>

<!-- Header -->
<div class="header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Welcome, <?php echo htmlspecialchars($username); ?></h4>
    <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content mt-5 pt-4">
    <div class="container-fluid">
        <h3>User Dashboard</h3>
        <p class="text-muted">This is your personalized dashboard. Use the sidebar to manage your data.</p>

        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="stat-box">
                    <h5>Total Bandwidth Records</h5>
                    <p class="fs-4"><?= $summary['total_records'] ?? 0 ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h5>Total Bandwidth (Mbps)</h5>
                    <p class="fs-4"><?= $summary['total_bandwidth'] ?? 0 ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h5>Total Clients</h5>
                    <p class="fs-4"><?= $summary['total_clients'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
