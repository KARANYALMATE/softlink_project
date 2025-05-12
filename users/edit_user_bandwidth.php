<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: view_my_data.php");
    exit;
}

$id = intval($_GET['id']);

// Get the record with name values by joining with master tables
$sql = "
    SELECT bd.*, 
           c.client_name, 
           n.noc_name AS noc_location, 
           lt.loop_type_name AS loop_type, 
           lp.loop_provider_name AS loop_provider, 
           ct.customer_type_name AS customer_type
    FROM bandwidth_distribution bd
    LEFT JOIN clients c ON bd.client_id = c.client_id
    LEFT JOIN noc_locations n ON bd.noc_location_id = n.noc_location_id
    LEFT JOIN loop_types lt ON bd.loop_type_id = lt.loop_type_id
    LEFT JOIN loop_providers lp ON bd.loop_provider_id = lp.loop_provider_id
    LEFT JOIN customer_types ct ON bd.customer_type_id = ct.customer_type_id
    WHERE bd.id = ? AND bd.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_my_data.php");
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Bandwidth Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <a href="../auth/logout.php">🚪 Logout</a>
</div>

<!-- Header -->
<div class="header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Edit Bandwidth Record</h4>
    <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content mt-5 pt-4">
    <div class="container mt-4">
        <h2 class="mb-4">Edit Bandwidth Record</h2>
        <form method="POST" action="update_user_bandwidth.php" autocomplete="off">

            <input type="hidden" name="id" value="<?= $id ?>">

            <!-- Client Name -->
            <div class="mb-3">
                <label class="form-label">Client Name</label>
                <input type="text" name="client_name" class="form-control" value="<?= htmlspecialchars($row['client_name'] ?? '') ?>" required>
            </div>

            <!-- NOC Location -->
            <div class="mb-3">
                <label class="form-label">NOC Location</label>
                <input type="text" name="noc_location" class="form-control" value="<?= htmlspecialchars($row['noc_location'] ?? '') ?>" required>
            </div>

            <!-- Loop Type -->
            <div class="mb-3">
                <label class="form-label">Loop Type</label>
                <input type="text" name="loop_type" class="form-control" value="<?= htmlspecialchars($row['loop_type'] ?? '') ?>" required>
            </div>

            <!-- Loop Provider -->
            <div class="mb-3">
                <label class="form-label">Loop Provider</label>
                <input type="text" name="loop_provider" class="form-control" value="<?= htmlspecialchars($row['loop_provider'] ?? '') ?>" required>
            </div>

            <!-- Circuit ID -->
            <div class="mb-3">
                <label class="form-label">Circuit ID</label>
                <input type="text" name="circuit_id" class="form-control" value="<?= htmlspecialchars($row['circuit_id']) ?>" required>
            </div>

            <!-- Bandwidth (Mbps) -->
            <div class="mb-3">
                <label class="form-label">Bandwidth (Mbps)</label>
                <input type="number" step="any" name="bandwidth_mbps" class="form-control" value="<?= $row['bandwidth_mbps'] ?>" required>
            </div>

            <!-- Commissioning Date -->
            <div class="mb-3">
                <label class="form-label">Commissioning Date</label>
                <input type="date" name="link_commissioning_date" class="form-control" value="<?= $row['link_commissioning_date'] ?>" required>
            </div>

            <!-- IP Block assigned -->
            <div class="mb-3">
                <label class="form-label">IP Block assigned</label>
                <input type="text" name="ip_block_assigned" class="form-control" value="<?= htmlspecialchars($row['ip_block_assigned']) ?>" required>
            </div>

            <!-- Discount Bandwidth -->
            <div class="mb-3">
                <label class="form-label">Discount (%)</label>
                <input type="number" step="any" name="discount_bandwidth" class="form-control" value="<?= $row['discount_bandwidth'] ?>" required>
            </div>

            <!-- Additional Discount for Startups -->
            <div class="mb-3">
                <label class="form-label">Additional Discount for Startups (%)</label>
                <input type="number" step="any" name="additional_discount_startup" class="form-control" value="<?= $row['additional_discount_startup'] ?>" required>
            </div>

            <!-- Annual BandWidth Tariff -->
            <div class="mb-3">
                <label class="form-label">Annual BandWidth Tariff</label>
                <input type="number" step="any" name="annual_bandwidth_tariff" class="form-control" value="<?= $row['annual_bandwidth_tariff'] ?>" required>
            </div>

            <!-- Annual Local Loop Tariff -->
            <div class="mb-3">
                <label class="form-label">Annual Local Loop Tariff</label>
                <input type="number" step="any" name="annual_local_loop_tariff" class="form-control" value="<?= $row['annual_local_loop_tariff'] ?>" required>
            </div>

            <!-- Customer Type -->
            <div class="mb-3">
                <label class="form-label">Customer Type</label>
                <input type="text" name="customer_type" class="form-control" value="<?= htmlspecialchars($row['customer_type'] ?? '') ?>" required>
            </div>

            <!-- Remark -->
            <div class="mb-3">
                <label class="form-label">Remark</label>
                <textarea name="remark" class="form-control"><?= htmlspecialchars($row['remark']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Record</button>
        </form>
    </div>
</div>

</body>
</html>
