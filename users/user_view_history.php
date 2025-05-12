<?php
include('../config/db.php');
include('../auth/check_login.php');
include('../includes/header.php');
include('../includes/sidebar.php');

$user_id = $_SESSION['user_id'];

// Fetch history entries for this user's bandwidth edits
$sql = "
    SELECT 
        h.*, 
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
    WHERE h.user_id = ?
    ORDER BY h.action_timestamp DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2 class="mb-4">My Bandwidth Edit History 📜</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>NOC Location</th>
                        <th>Loop Type</th>
                        <th>Loop Provider</th>
                        <th>Circuit ID</th>
                        <th>Bandwidth (Mbps)</th>
                        <th>Link Commission Date</th>
                        <th>IP Block Assigned</th>
                        <th>Discount (%)</th>
                        <th>Startup Discount (%)</th>
                        <th>Annual BW Tariff</th>
                        <th>Annual Loop Tariff</th>
                        <th>Customer Type</th>
                        <th>Remark</th>
                        <th>Action Type</th>
                        <th>Changed By</th>
                        <th>Action Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $counter++; ?></td>
                        <td><?= htmlspecialchars($row['client_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['noc_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['loop_type_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['loop_provider_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['circuit_id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['bandwidth_mbps'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['link_commissioning_date'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['ip_block_assigned'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['discount_bandwidth'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['additional_discount_startup'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['annual_bandwidth_tariff'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['annual_local_loop_tariff'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['customer_type_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['remark'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['action_type'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['changed_by_username'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($row['action_timestamp'])) ?? '-') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No history records found yet.
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
