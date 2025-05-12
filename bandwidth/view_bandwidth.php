<?php
session_start();

$isAdminPage = true;
require_once '../config/db.php';

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
    bd.remark,
    bd.entry_date
FROM bandwidth_distribution bd
JOIN clients c ON bd.client_id = c.client_id
JOIN noc_locations n ON bd.noc_location_id = n.noc_location_id
JOIN loop_types lt ON bd.loop_type_id = lt.loop_type_id
JOIN loop_providers lp ON bd.loop_provider_id = lp.loop_provider_id
JOIN customer_types ct ON bd.customer_type_id = ct.customer_type_id
ORDER BY bd.entry_date DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bandwidth Distribution - Softlink</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f6f9; }
        .sidebar { height: 100vh; width: 240px; position: fixed; top: 0; left: 0; background-color: rgb(14, 15, 15); padding-top: 20px; z-index: 10; }
        .sidebar h2 { color: white; text-align: center; font-size: 24px; padding: 10px; margin: 0; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 18px; color: white; display: block; transition: 0.3s; }
        .sidebar a:hover { background-color: #1ABC9C; }
        .content { margin-left: 260px; padding: 40px; position: relative; z-index: 1; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f4f4f4; }
        .dt-button-collection { z-index: 9999 !important; }
        .action-btn {
        padding: 6px 12px;
        background-color: rgb(10, 11, 11);
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: 0.3s;
        margin-right: 10px; /* Add some space between the Edit and History buttons */
    }

    .action-btn:hover {
        background-color: rgb(1, 11, 17);
    }

    .history-btn {
        padding: 6px 12px;
        background-color: #3498db;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: 0.3s;
        margin-left: 10px; /* Add some space between View and History button */
    }

    .history-btn:hover {
        background-color: #2980b9;
    }
        @media print { .noPrint { display: none !important; } }
    </style>
</head>
<body>

<?php if ($isAdminPage) { ?>
<div class="sidebar">
    <h2>STPI Dashboard</h2>
    <a href="/softlink_project/admin/admin_dashboard.php">🏠 Home</a>
    <a href="/softlink_project/bandwidth/add_bandwidth.php">➕ Bandwidth Form</a>
    <a href="/softlink_project/bandwidth/view_bandwidth.php">📊 View Data</a>
    <a href="/softlink_project/admin/manage_users.php">👥 Users</a>
    <a href="/softlink_project/logout.php">🚪 Logout</a>
</div>
<?php } ?>

<div class="content">
    <h2>📊 Bandwidth Distribution Data</h2>

    <table id="bandwidthTable" class="display">
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Client Name</th>
                <th>NOC Location</th>
                <th>Loop Type</th>
                <th>Loop Provider</th>
                <th>Circuit ID</th>
                <th>Bandwidth (Mbps)</th>
                <th>Commissioning Date</th>
                <th>IP Block Assigned</th>
                <th>Discount Bandwidth</th>
                <th>Additional discount for Startup</th>
                <th>Annual Bandwidth Tariff</th>
                <th>Annual Local Loop Tariff</th>
                <th>Customer Type</th>
                <th>Remark</th>
                <th class="noPrint">Entry Date</th>
                <th class="noPrint">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $srNo = 1;
            while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $srNo++ ?></td>
                <td><?= $row['client_name'] ?></td>
                <td><?= $row['noc_name'] ?></td>
                <td><?= $row['loop_type_name'] ?></td>
                <td><?= $row['loop_provider_name'] ?></td>
                <td><?= $row['circuit_id'] ?></td>
                <td><?= $row['bandwidth_mbps'] ?></td>
                <td><?= $row['link_commissioning_date'] ?></td>
                <td><?= $row['ip_block_assigned'] ?></td>
                <td><?= $row['discount_bandwidth'] ?></td>
                <td><?= $row['additional_discount_startup'] ?></td>
                <td><?= $row['annual_bandwidth_tariff'] ?></td>
                <td><?= $row['annual_local_loop_tariff'] ?></td>
                <td><?= $row['customer_type_name'] ?></td>
                <td><?= $row['remark'] ?></td>
                <td class="noPrint"><?= $row['entry_date'] ?></td>
                <td class="noPrint">
                    <a href="edit_bandwidth.php?id=<?= $row['id'] ?>" class="action-btn">Edit</a>
                    <a href="history_button.php?id=<?= $row['id'] ?>" class="history-btn">History</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#bandwidthTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'collection',
            text: '⬇️ Export',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Bandwidth_Distribution_Report',
                    filename: 'Bandwidth_Distribution_Report_Copy',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                    }
                },
                {
                    extend: 'csv',
                    title: 'Bandwidth_Distribution_Report',
                    filename: 'Bandwidth_Distribution_Report_CSV',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                    }
                },
                {
                    extend: 'excel',
                    title: 'Bandwidth_Distribution_Report',
                    filename: 'Bandwidth_Distribution_Report_Excel',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Bandwidth Distribution Report',
                    filename: 'Bandwidth_Distribution_Report_PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                    }
                },
                {
                    extend: 'print',
                    title: 'Bandwidth Distribution Report',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                    }
                }
            ]
        }
    ]
});

});
</script>

</body>
</html>
