<?php
require_once '../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid ID!");
}

$bandwidth_id = $_GET['id'];

$query = "
SELECT 
    bd.id,
    c.client_name,
    n.noc_name,
    lt.loop_type_name,
    lp.loop_provider_name,
    bd.circuit_id,
    bd.bandwidth_mbps,
    bd.link_commissioning_date,
    bd.ip_block_assigned,
    bd.discount_bandwidth,
    bd.additional_discount_startup,
    bd.annual_bandwidth_tariff,
    bd.annual_local_loop_tariff,
    ct.customer_type_name,
    bd.remark
FROM bandwidth_distribution bd
JOIN clients c ON bd.client_id = c.client_id
JOIN noc_locations n ON bd.noc_location_id = n.noc_location_id
JOIN loop_types lt ON bd.loop_type_id = lt.loop_type_id
JOIN loop_providers lp ON bd.loop_provider_id = lp.loop_provider_id
JOIN customer_types ct ON bd.customer_type_id = ct.customer_type_id
WHERE bd.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bandwidth_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found!");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Bandwidth Record</title>
    <style>
              body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef1f5;
            margin: 0;
        }
        .sidebar {
            height: 100%;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #1f2d3d;
            padding-top: 20px;
        }
        .sidebar h2 {
            color: #ffffff;
            text-align: center;
            font-size: 24px;
            padding: 10px;
            margin: 0;
            border-bottom: 1px solid #444;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #ecf0f1;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #16a085;
        }
        .content {
            margin-left: 240px;
            padding: 40px;
        }
        .dashboard {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #34495e;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        .form-group input[type="date"],
        .form-group input[type="number"] {
            max-width: 350px;
        }
        .btn-update {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-update:hover {
            background-color: #2980b9;
        }
        .form-group textarea {
            height: 130px;
            resize: vertical;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>STPI Dashboard</h2>
    <a href="/softlink_project/admin/admin_dashboard.php">🏠 Home</a>
    <a href="/softlink_project/bandwidth/add_bandwidth.php">➕ Add Bandwidth</a>
    <a href="/softlink_project/bandwidth/view_bandwidth.php">📊 View Bandwidth</a>
    <a href="/softlink_project/admin/manage_users.php">👥 Manage Users</a>
    <a href="/softlink_project/logout.php">🚪 Logout</a>
</div>

<div class="content">
    <div class="dashboard">
        <h2>Edit Bandwidth Record</h2>

        <form method="post" action="update_bandwidth.php">
            <input type="hidden" name="id" value="<?= $row['id']; ?>">

            <div class="form-group">
                <label for="client_name">Client Name</label>
                <input type="text" name="client_name" value="<?= htmlspecialchars($row['client_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="noc_name">NOC Location</label>
                <input type="text" name="noc_name" value="<?= htmlspecialchars($row['noc_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="loop_type_name">Loop Type</label>
                <select name="loop_type_name" required>
                    <option value="UTP/ LAN" <?= $row['loop_type_name'] === 'UTP/ LAN' ? 'selected' : '' ?>>UTP/ LAN</option>
                    <option value="Radio" <?= $row['loop_type_name'] === 'Radio' ? 'selected' : '' ?>>Radio</option>
                    <option value="Fiber" <?= $row['loop_type_name'] === 'Fiber' ? 'selected' : '' ?>>Fiber</option>
                </select>
            </div>

            <div class="form-group">
                <label for="loop_provider_name">Loop Provider</label>
                <select name="loop_provider_name" required>
                    <?php
                    $providers = ["STPI", "STPI(TATA)", "CUSTOMER OWNED ", "BSNL", "Airtel", "Jio", "Vodafone"];
                    foreach ($providers as $provider) {
                        $selected = ($row['loop_provider_name'] === $provider) ? 'selected' : '';
                        echo "<option value=\"$provider\" $selected>$provider</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="circuit_id">Circuit ID</label>
                <input type="text" name="circuit_id" value="<?= htmlspecialchars($row['circuit_id']) ?>" required>
            </div>

            <div class="form-group">
                <label for="bandwidth_mbps">Bandwidth (Mbps)</label>
                <input type="number" name="bandwidth_mbps" value="<?= htmlspecialchars($row['bandwidth_mbps']) ?>" required>
            </div>

            <div class="form-group">
                <label for="link_commissioning_date">Link Commissioning Date</label>
                <input type="date" name="link_commissioning_date" value="<?= htmlspecialchars($row['link_commissioning_date']) ?>" required>
            </div>

            <div class="form-group">
                <label for="ip_block_assigned">IP Block Assigned</label>
                <input type="text" name="ip_block_assigned" value="<?= htmlspecialchars($row['ip_block_assigned']) ?>">
            </div>

            <div class="form-group">
                <label for="discount_bandwidth">Discount Bandwidth</label>
                <input type="number" name="discount_bandwidth" value="<?= htmlspecialchars($row['discount_bandwidth']) ?>">
            </div>

            <div class="form-group">
                <label for="additional_discount_startup">Additional Discount for Startups</label>
                <input type="number" name="additional_discount_startup" value="<?= htmlspecialchars($row['additional_discount_startup']) ?>">
            </div>

            <div class="form-group">
                <label for="annual_bandwidth_tariff">Annual Bandwidth Tariff</label>
                <input type="number" name="annual_bandwidth_tariff" value="<?= htmlspecialchars($row['annual_bandwidth_tariff']) ?>">
            </div>

            <div class="form-group">
                <label for="annual_local_loop_tariff">Annual Local Loop Tariff</label>
                <input type="number" name="annual_local_loop_tariff" value="<?= htmlspecialchars($row['annual_local_loop_tariff']) ?>">
            </div>

            <div class="form-group">
                <label for="customer_type_name">Customer Type</label>
                <select name="customer_type_name" required>
                    <option value="Govt" <?= $row['customer_type_name'] === 'Govt' ? 'selected' : '' ?>>Govt</option>
                    <option value="Private" <?= $row['customer_type_name'] === 'Private' ? 'selected' : '' ?>>Private</option>
                </select>
            </div>

            <div class="form-group">
                <label for="remark">Remark</label>
                <textarea name="remark"><?= htmlspecialchars($row['remark']) ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-update">Update Record</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
