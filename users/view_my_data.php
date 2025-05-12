<!-- User Sidebar -->
<style>
    .sidebar {
        background-color: #343a40;
        min-height: 100vh;
        padding: 20px;
    }
    .sidebar a {
        display: block;
        padding: 10px 0;
        color: #fff;
        text-decoration: none;
    }
    .sidebar a:hover {
        background-color: #495057;
        border-radius: 5px;
        padding-left: 10px;
    }
</style>

<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$providerFilter = $_GET['provider'] ?? '';
$search = $_GET['search'] ?? '';

$providers = $conn->query("SELECT DISTINCT lp.loop_provider_name FROM loop_providers lp 
    JOIN bandwidth_distribution bd ON lp.loop_provider_id = bd.loop_provider_id 
    WHERE bd.user_id = $user_id");

$query = "
    SELECT bd.*, c.client_name, n.noc_name, lt.loop_type_name, lp.loop_provider_name, ct.customer_type_name
    FROM bandwidth_distribution bd
    JOIN clients c ON bd.client_id = c.client_id
    JOIN noc_locations n ON bd.noc_location_id = n.noc_location_id
    JOIN loop_types lt ON bd.loop_type_id = lt.loop_type_id
    JOIN loop_providers lp ON bd.loop_provider_id = lp.loop_provider_id
    JOIN customer_types ct ON bd.customer_type_id = ct.customer_type_id
    WHERE bd.user_id = ?
";

$params = [$user_id];
$types = "i";

if ($providerFilter) {
    $query .= " AND lp.loop_provider_name = ?";
    $params[] = $providerFilter;
    $types .= "s";
}

if ($search) {
    $query .= " AND (
        bd.circuit_id LIKE ? OR 
        c.client_name LIKE ? OR 
        lp.loop_provider_name LIKE ?
    )";
    $likeSearch = "%" . $search . "%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $types .= "sss";
}

$query .= " ORDER BY bd.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$summaryQuery = "
    SELECT COUNT(*) AS total_records, SUM(bd.bandwidth_mbps) AS total_bandwidth, COUNT(DISTINCT c.client_id) AS total_clients
    FROM bandwidth_distribution bd
    JOIN clients c ON bd.client_id = c.client_id
    WHERE bd.user_id = ?
";
$summaryStmt = $conn->prepare($summaryQuery);
$summaryStmt->bind_param("i", $user_id);
$summaryStmt->execute();
$summaryResult = $summaryStmt->get_result();
$summary = $summaryResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bandwidth Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h2 class="text-white text-center">Branch Panel</h2>
            <a href="/softlink_project/users/dashboard.php">🏠 Dashboard</a>
            <a href="/softlink_project/users/branch_bandwidth.php">➕ Form</a>
            <a href="/softlink_project/users/view_my_data.php">📊 View My Data</a>
            <a href="/softlink_project/auth/logout.php">🚪 Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Bandwidth Records</h2>
                <!-- Export Buttons -->
                <div class="btn-group">
                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Export</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportTable('copy')">Copy</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportTable('csv')">CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportTable('excel')">Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportTable('pdf')">PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportTable('print')">Print</a></li>
                    </ul>
                </div>
            </div>

            <!-- Dashboard Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Records</h5>
                            <p class="card-text"><?= $summary['total_records'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Bandwidth (Mbps)</h5>
                            <p class="card-text"><?= $summary['total_bandwidth'] ?> Mbps</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Clients</h5>
                            <p class="card-text"><?= $summary['total_clients'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <select name="provider" class="form-select" onchange="this.form.submit()">
                        <option value="">🔍 Filter by Provider</option>
                        <?php while($row = $providers->fetch_assoc()): ?>
                            <option value="<?= $row['loop_provider_name'] ?>" <?= ($providerFilter == $row['loop_provider_name']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['loop_provider_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="🔎 Search..." onkeyup="delaySubmit()">
                </div>
            </form>

            <!-- Data Table -->
            <table id="bandwidthTable" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>NOC Location</th>
                        <th>Loop Type</th>
                        <th>Loop Provider</th>
                        <th>Circuit ID</th>
                        <th>Bandwidth (Mbps)</th>
                        <th>Commissioning Date</th>
                        <th>IP Block Assigned</th>
                        <th>Discount Bandwidth</th>
                        <th>Additional Discount for Startups</th>
                        <th>Annual BandWidth Tariff</th>
                        <th>Annual Local Loop Tariff</th>
                        <th>Customer Type</th>
                        <th>Remark</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): 
                        $sn = 1;
                        while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= htmlspecialchars($row['client_name']) ?></td>
                            <td><?= htmlspecialchars($row['noc_name']) ?></td>
                            <td><?= htmlspecialchars($row['loop_type_name']) ?></td>
                            <td><?= htmlspecialchars($row['loop_provider_name']) ?></td>
                            <td><?= htmlspecialchars($row['circuit_id']) ?></td>
                            <td><?= htmlspecialchars($row['bandwidth_mbps']) ?> Mbps</td>
                            <td><?= htmlspecialchars($row['link_commissioning_date']) ?></td>
                            <td><?= htmlspecialchars($row['ip_block_assigned']) ?></td>
                            <td><?= htmlspecialchars($row['discount_bandwidth']) ?></td>
                            <td><?= htmlspecialchars($row['additional_discount_startup']) ?></td>
                            <td><?= htmlspecialchars($row['annual_bandwidth_tariff']) ?></td>
                            <td><?= htmlspecialchars($row['annual_local_loop_tariff']) ?></td>
                            <td><?= htmlspecialchars($row['customer_type_name']) ?></td>
                            <td><?= htmlspecialchars($row['remark']) ?></td>
                            <td>
                                <a href="edit_user_bandwidth.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_user_bandwidth.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="16" class="text-center">No records found!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
let timer;
function delaySubmit() {
    clearTimeout(timer);
    timer = setTimeout(() => document.forms[0].submit(), 700);
}

function exportTable(type) {
    const table = $('#bandwidthTable').DataTable();
    table.button(`.buttons-${type}`).trigger();
}
$(document).ready(function () {
    $('#bandwidthTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'd-none',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude last column
                }
            },
            {
                extend: 'csv',
                className: 'd-none',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                className: 'd-none',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                className: 'd-none',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                className: 'd-none',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});
</script>
</body>
</html>
