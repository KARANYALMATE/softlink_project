<?php
session_start();
require_once '../config/db.php';

// Function to get or insert and return master table IDs
function getOrCreateId($conn, $table, $nameColumn, $nameValue, $idColumn) {
    $stmt = $conn->prepare("SELECT $idColumn FROM $table WHERE $nameColumn = ? LIMIT 1");
    $stmt->bind_param("s", $nameValue);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $stmt->close();
        return $row[$idColumn];
    }
    $stmt->close();

    // Insert new value if not found
    $stmt = $conn->prepare("INSERT INTO $table ($nameColumn) VALUES (?)");
    $stmt->bind_param("s", $nameValue);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    return $newId;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bandwidth_id = $_POST['id'] ?? '';
    $client_name = $_POST['client_name'] ?? '';
    $noc_location = $_POST['noc_name'] ?? '';
    $loop_type = $_POST['loop_type_name'] ?? '';
    $loop_provider = $_POST['loop_provider_name'] ?? '';
    $circuit_id = $_POST['circuit_id'] ?? '';
    $bandwidth_mbps = $_POST['bandwidth_mbps'] ?? '';
    $link_commissioning_date = $_POST['link_commissioning_date'] ?? '';
    $ip_block_assigned = $_POST['ip_block_assigned'] ?? '';
    $discount_bandwidth = $_POST['discount_bandwidth'] ?? '';
    $additional_discount_startup = $_POST['additional_discount_startup'] ?? '';
    $annual_bandwidth_tariff = $_POST['annual_bandwidth_tariff'] ?? '';
    $annual_local_loop_tariff = $_POST['annual_local_loop_tariff'] ?? '';
    $customer_type = $_POST['customer_type_name'] ?? '';
    $remark = $_POST['remark'] ?? '';

    // Get or create foreign keys
    $client_id = getOrCreateId($conn, 'clients', 'client_name', $client_name, 'client_id');
    $noc_id = getOrCreateId($conn, 'noc_locations', 'noc_name', $noc_location, 'noc_location_id');
    $loop_type_id = getOrCreateId($conn, 'loop_types', 'loop_type_name', $loop_type, 'loop_type_id');
    $loop_provider_id = getOrCreateId($conn, 'loop_providers', 'loop_provider_name', $loop_provider, 'loop_provider_id');
    $customer_type_id = getOrCreateId($conn, 'customer_types', 'customer_type_name', $customer_type, 'customer_type_id');

    // Fetch existing data for history
    $stmt = $conn->prepare("SELECT * FROM bandwidth_distribution WHERE id = ?");
    $stmt->bind_param("i", $bandwidth_id);
    $stmt->execute();
    $current = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Get user ID from session
    $user_id = $_SESSION['user_id'] ?? null;

    if ($current && $user_id) {
        // Insert into history table
        $history_sql = "
        INSERT INTO bandwidth_distribution_history (
            bandwidth_id, client_id, noc_location_id, loop_type_id,
            loop_provider_id, circuit_id, bandwidth_mbps, link_commissioning_date,
            ip_block_assigned, discount_bandwidth, additional_discount_startup,
            annual_bandwidth_tariff, annual_local_loop_tariff, customer_type_id,
            remark, entry_date, user_id, action_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $history_stmt = $conn->prepare($history_sql);
        $today = date('Y-m-d');
        $action_type = 'UPDATE';

        $history_stmt->bind_param(
            "iiiissdssddddsssis",
            $bandwidth_id,
            $current['client_id'],
            $current['noc_location_id'],
            $current['loop_type_id'],
            $current['loop_provider_id'],
            $current['circuit_id'],
            $current['bandwidth_mbps'],
            $current['link_commissioning_date'],
            $current['ip_block_assigned'],
            $current['discount_bandwidth'],
            $current['additional_discount_startup'],
            $current['annual_bandwidth_tariff'],
            $current['annual_local_loop_tariff'],
            $current['customer_type_id'],
            $current['remark'],
            $today,
            $user_id,
            $action_type
        );

        $history_stmt->execute();
        $history_stmt->close();
    }

    // Now update the main bandwidth_distribution record
    $update_sql = "UPDATE bandwidth_distribution SET 
        client_id = ?, noc_location_id = ?, loop_type_id = ?, loop_provider_id = ?,
        circuit_id = ?, bandwidth_mbps = ?, link_commissioning_date = ?, ip_block_assigned = ?,
        discount_bandwidth = ?, additional_discount_startup = ?, annual_bandwidth_tariff = ?, 
        annual_local_loop_tariff = ?, customer_type_id = ?, remark = ?
        WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param(
        "iiissdssddddssi",
        $client_id,
        $noc_id,
        $loop_type_id,
        $loop_provider_id,
        $circuit_id,
        $bandwidth_mbps,
        $link_commissioning_date,
        $ip_block_assigned,
        $discount_bandwidth,
        $additional_discount_startup,
        $annual_bandwidth_tariff,
        $annual_local_loop_tariff,
        $customer_type_id,
        $remark,
        $bandwidth_id
    );

    if ($stmt->execute()) {
        header("Location: view_bandwidth.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
