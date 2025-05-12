<?php
session_start();
require_once '../config/db.php';

// ✅ Proper session check for logged-in user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Utility function to get or insert into master tables
function getId($conn, $table, $id_col, $name_col, $value) {
    $stmt = $conn->prepare("SELECT $id_col FROM $table WHERE $name_col = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row[$id_col];
    } else {
        $insert = $conn->prepare("INSERT INTO $table ($name_col) VALUES (?)");
        $insert->bind_param("s", $value);
        $insert->execute();
        return $insert->insert_id;
    }
}

// ✅ Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST['client_name'];
    $noc_name = $_POST['noc_name'];
    $loop_type_name = $_POST['loop_type_name'];
    $loop_provider_name = $_POST['loop_provider_name'];
    $circuit_id = $_POST['circuit_id'];
    $bandwidth_mbps = $_POST['bandwidth_mbps'];
    $link_commissioning_date = $_POST['link_commissioning_date'];
    $ip_block_assigned = $_POST['ip_block_assigned'];
    $discount_bandwidth = $_POST['discount_bandwidth'];
    $additional_discount_startup = $_POST['additional_discount_startup'];
    $annual_bandwidth_tariff = $_POST['annual_bandwidth_tariff'];
    $annual_local_loop_tariff = $_POST['annual_local_loop_tariff'];
    $customer_type_name = $_POST['customer_type_name'];
    $remark = $_POST['remark'];

    // Get or insert IDs from master tables
    $client_id = getId($conn, "clients", "client_id", "client_name", $client_name);
    $noc_id = getId($conn, "noc_locations", "noc_location_id", "noc_name", $noc_name);
    $loop_type_id = getId($conn, "loop_types", "loop_type_id", "loop_type_name", $loop_type_name);
    $loop_provider_id = getId($conn, "loop_providers", "loop_provider_id", "loop_provider_name", $loop_provider_name);
    $customer_type_id = getId($conn, "customer_types", "customer_type_id", "customer_type_name", $customer_type_name);

    // Insert into bandwidth_distribution
    $stmt = $conn->prepare("INSERT INTO bandwidth_distribution (
        user_id, client_id, noc_location_id, loop_type_id, loop_provider_id,
        circuit_id, bandwidth_mbps, link_commissioning_date, ip_block_assigned,
        discount_bandwidth, additional_discount_startup, annual_bandwidth_tariff,
        annual_local_loop_tariff, customer_type_id, remark
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "iiiiisdsdddddis",
        $user_id, $client_id, $noc_id, $loop_type_id, $loop_provider_id,
        $circuit_id, $bandwidth_mbps, $link_commissioning_date, $ip_block_assigned,
        $discount_bandwidth, $additional_discount_startup, $annual_bandwidth_tariff,
        $annual_local_loop_tariff, $customer_type_id, $remark
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => '✔️ Data added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Error: ' . $stmt->error]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Bandwidth</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f8;
            padding: 0;
            margin: 0;
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

        .container {
            max-width: 850px;
        }
    </style>
</head>
<body>

<!-- User Sidebar -->
<div class="sidebar">
    <h2 class="text-white text-center">Branch Panel</h2>
    <a href="/softlink_project/users/dashboard.php">🏠 Dashboard</a>
    <a href="/softlink_project/users/branch_bandwidth.php">➕ Form</a>
    <a href="/softlink_project/users/view_my_data.php">📊 View My Data</a>
    <a href="/softlink_project/auth/logout.php">🚪 Logout</a>
</div>

<!-- Main Form Content -->
<div class="main-content">
    <div class="container mt-4">
        <h3 class="mb-4">Add Bandwidth Record</h3>
        <form id="bandwidthForm">
            <div class="row g-3">
                <div class="col-md-6"><input type="text" name="client_name" class="form-control" placeholder="Client Name" required></div>
                <div class="col-md-6"><input type="text" name="noc_name" class="form-control" placeholder="NOC Location" required></div>

                <div class="col-md-6">
                    <select name="loop_type_name" class="form-control" required>
                        <option value="">Select Loop Type</option>
                        <option value="UTP/LAN">UTP/LAN</option>
                        <option value="Radio">Radio</option>
                        <option value="Fiber">Fiber</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <select name="loop_provider_name" class="form-control" required>
                        <option value="">Select Loop Provider</option>
                        <option value="STPI">STPI</option>
                        <option value="STPI(TATA)">STPI(TATA)</option>
                        <option value="Customer Owned">Customer Owned</option>
                        <option value="BSNL">BSNL</option>
                        <option value="Airtel">Airtel</option>
                        <option value="Jio">Jio</option>
                        <option value="Vodafone">Vodafone</option>
                    </select>
                </div>

                <div class="col-md-6"><input type="text" name="circuit_id" class="form-control" placeholder="Circuit ID" required></div>
                <div class="col-md-6"><input type="number" name="bandwidth_mbps" class="form-control" placeholder="Bandwidth (Mbps)" required></div>
                <div class="col-md-6"><input type="date" name="link_commissioning_date" class="form-control" required></div>
                <div class="col-md-6"><input type="text" name="ip_block_assigned" class="form-control" placeholder="IP Block Assigned"></div>
                <div class="col-md-6"><input type="number" name="discount_bandwidth" class="form-control" placeholder="Discount Bandwidth"></div>
                <div class="col-md-6"><input type="number" name="additional_discount_startup" class="form-control" placeholder="Startup Discount"></div>
                <div class="col-md-6"><input type="number" name="annual_bandwidth_tariff" class="form-control" placeholder="Annual BW Tariff"></div>
                <div class="col-md-6"><input type="number" name="annual_local_loop_tariff" class="form-control" placeholder="Local Loop Tariff"></div>

                <div class="col-md-6">
                    <select name="customer_type_name" class="form-control" required>
                        <option value="">Select Customer Type</option>
                        <option value="GOVT">GOVT</option>
                        <option value="Private">Private</option>
                    </select>
                </div>

                <div class="col-md-6"><input type="text" name="remark" class="form-control" placeholder="Remark"></div>
            </div>
            <button type="submit" class="btn btn-primary mt-4">Submit</button>
            <div id="message" class="mt-3"></div>
        </form>
    </div>
</div>

<!-- AJAX Script -->
<script>
document.getElementById('bandwidthForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("branch_bandwidth.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('message').innerHTML = `
            <div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">
                ${data.message}
            </div>`;
        if (data.status === 'success') this.reset();
    });
});
</script>
</body>
</html>
