<?php
require_once '../config/db.php';

// ✅ Fetch all history records
$sql = "
    SELECT h.*, 
           c.client_name,
           n.noc_name,
           lt.loop_type_name,
           lp.loop_provider_name,
           ct.customer_type_name,
           u.username AS changed_by_username
    FROM bandwidth_distribution_history h
    LEFT JOIN clients c ON h.client_id = c.client_id
    LEFT JOIN noc_locations n ON h.noc_location_id = n.noc_location_id
    LEFT JOIN loop_types lt ON h.loop_type_id = lt.loop_type_id
    LEFT JOIN loop_providers lp ON h.loop_provider_id = lp.loop_provider_id
    LEFT JOIN customer_types ct ON h.customer_type_id = ct.customer_type_id
    LEFT JOIN users u ON h.user_id = u.user_id
    ORDER BY h.action_timestamp DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bandwidth Change History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #aaa;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
        }
        h2 {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<h2>📜 All Bandwidth Change History</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>NOC Location</th>
                <th>Loop Type</th>
                <th>Loop Provider</th>
                <th>Circuit ID</th>
                <th>Bandwidth (Mbps)</th>
                <th>Link Date</th>
                <th>IP Block</th>
                <th>Discount (%)</th>
                <th>Startup Discount (%)</th>
                <th>BW Tariff</th>
                <th>Loop Tariff</th>
                <th>Customer Type</th>
                <th>Remark</th>
                <th>Action Type</th>
                <th>Changed By</th>
                <th>Action Timestamp</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['client_name']) ?></td>
                <td><?= htmlspecialchars($row['noc_name']) ?></td>
                <td><?= htmlspecialchars($row['loop_type_name']) ?></td>
                <td><?= htmlspecialchars($row['loop_provider_name']) ?></td>
                <td><?= htmlspecialchars($row['circuit_id']) ?></td>
                <td><?= htmlspecialchars($row['bandwidth_mbps']) ?></td>
                <td><?= htmlspecialchars($row['link_commissioning_date']) ?></td>
                <td><?= htmlspecialchars($row['ip_block_assigned']) ?></td>
                <td><?= htmlspecialchars($row['discount_bandwidth']) ?></td>
                <td><?= htmlspecialchars($row['additional_discount_startup']) ?></td>
                <td><?= htmlspecialchars($row['annual_bandwidth_tariff']) ?></td>
                <td><?= htmlspecialchars($row['annual_local_loop_tariff']) ?></td>
                <td><?= htmlspecialchars($row['customer_type_name']) ?></td>
                <td><?= htmlspecialchars($row['remark']) ?></td>
                <td><?= htmlspecialchars($row['action_type']) ?></td> <!-- New -->
                <td><?= htmlspecialchars($row['changed_by_username']) ?></td>
                <td><?= htmlspecialchars($row['action_timestamp']) ?></td> <!-- New -->
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No change history records found.</p>
<?php endif; ?>

<?php $conn->close(); ?>

</body>
</html>
