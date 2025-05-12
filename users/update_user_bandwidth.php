<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    
    // Get all posted form data
    $client_name = trim($_POST['client_name']);
    $noc_location = trim($_POST['noc_location']);
    $loop_type = trim($_POST['loop_type']);
    $loop_provider = trim($_POST['loop_provider']);
    $circuit_id = trim($_POST['circuit_id']);
    $bandwidth_mbps = $_POST['bandwidth_mbps'];
    $link_commissioning_date = $_POST['link_commissioning_date'];
    $ip_block_assigned = trim($_POST['ip_block_assigned']);
    $discount_bandwidth = $_POST['discount_bandwidth'];
    $additional_discount_startup = $_POST['additional_discount_startup'];
    $annual_bandwidth_tariff = $_POST['annual_bandwidth_tariff'];
    $annual_local_loop_tariff = $_POST['annual_local_loop_tariff'];
    $customer_type = trim($_POST['customer_type']);
    $remark = trim($_POST['remark']);

    // --- Step 1: Find or Insert master table values and get IDs ---

    // 1. Client
    $stmt = $conn->prepare("SELECT client_id FROM clients WHERE client_name = ?");
    $stmt->bind_param("s", $client_name);
    $stmt->execute();
    $stmt->bind_result($client_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO clients (client_name) VALUES (?)");
        $stmt->bind_param("s", $client_name);
        $stmt->execute();
        $client_id = $stmt->insert_id;
    }
    $stmt->close();

    // 2. NOC
    $stmt = $conn->prepare("SELECT noc_location_id FROM noc_locations WHERE noc_name = ?");
    $stmt->bind_param("s", $noc_location);
    $stmt->execute();
    $stmt->bind_result($noc_location_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO noc_locations (noc_name) VALUES (?)");
        $stmt->bind_param("s", $noc_location);
        $stmt->execute();
        $noc_location_id = $stmt->insert_id;
    }
    $stmt->close();

    // 3. Loop Type
    $stmt = $conn->prepare("SELECT loop_type_id FROM loop_types WHERE loop_type_name = ?");
    $stmt->bind_param("s", $loop_type);
    $stmt->execute();
    $stmt->bind_result($loop_type_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO loop_types (loop_type_name) VALUES (?)");
        $stmt->bind_param("s", $loop_type);
        $stmt->execute();
        $loop_type_id = $stmt->insert_id;
    }
    $stmt->close();

    // 4. Loop Provider
    $stmt = $conn->prepare("SELECT loop_provider_id FROM loop_providers WHERE loop_provider_name = ?");
    $stmt->bind_param("s", $loop_provider);
    $stmt->execute();
    $stmt->bind_result($loop_provider_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO loop_providers (loop_provider_name) VALUES (?)");
        $stmt->bind_param("s", $loop_provider);
        $stmt->execute();
        $loop_provider_id = $stmt->insert_id;
    }
    $stmt->close();

    // 5. Customer Type
    $stmt = $conn->prepare("SELECT customer_type_id FROM customer_types WHERE customer_type_name = ?");
    $stmt->bind_param("s", $customer_type);
    $stmt->execute();
    $stmt->bind_result($customer_type_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO customer_types (customer_type_name) VALUES (?)");
        $stmt->bind_param("s", $customer_type);
        $stmt->execute();
        $customer_type_id = $stmt->insert_id;
    }
    $stmt->close();

 // --- Step 2: Update the main bandwidth_distribution record ---
$update_sql = "
UPDATE bandwidth_distribution
SET client_id = ?, 
    noc_location_id = ?, 
    loop_type_id = ?, 
    loop_provider_id = ?, 
    circuit_id = ?, 
    bandwidth_mbps = ?, 
    link_commissioning_date = ?, 
    ip_block_assigned = ?, 
    discount_bandwidth = ?, 
    additional_discount_startup = ?, 
    annual_bandwidth_tariff = ?, 
    annual_local_loop_tariff = ?, 
    customer_type_id = ?, 
    remark = ?
WHERE id = ? AND user_id = ?
";

$stmt = $conn->prepare($update_sql);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

// Corrected type string (NO SPACES):
$stmt->bind_param(
    "iiiisdsdddddissi", 
    $client_id, 
    $noc_location_id, 
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
    $id, 
    $user_id
);

if ($stmt->execute()) {
    echo "Record updated successfully.";
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();

        // --- Step 3: Insert into history table ---
        $insert_history_sql = "
            INSERT INTO bandwidth_distribution_history (
                bandwidth_id,
                client_id,
                noc_location_id,
                loop_type_id,
                loop_provider_id,
                circuit_id,
                bandwidth_mbps,
                link_commissioning_date,
                ip_block_assigned,
                discount_bandwidth,
                additional_discount_startup,
                annual_bandwidth_tariff,
                annual_local_loop_tariff,
                customer_type_id,
                remark,
                entry_date,
                user_id,
                action_type
            )
            SELECT 
                id, client_id, noc_location_id, loop_type_id, loop_provider_id, 
                circuit_id, bandwidth_mbps, link_commissioning_date, ip_block_assigned, 
                discount_bandwidth, additional_discount_startup, annual_bandwidth_tariff, 
                annual_local_loop_tariff, customer_type_id, remark, entry_date,
                ?, 'UPDATE'
            FROM bandwidth_distribution
            WHERE id = ?
        ";
        $stmt = $conn->prepare($insert_history_sql);
        $stmt->bind_param("ii", $user_id, $id);
        $stmt->execute();
        $stmt->close();

        // Redirect back to view page
        header("Location: view_my_data.php?success=Record updated successfully");
        exit;
    } else {
        $stmt->close();
        die("Failed to update record.");
    }

?>
