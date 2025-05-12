<?php
include '../config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Clients</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 15px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }

        th, td {
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        a.button {
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 5px;
        }

        a.button:hover {
            background-color: #0056b3;
        }

        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>
<body>

<h1>Manage Clients</h1>

<table>
    <thead>
        <tr>
            <th>Client Name</th>
            <th>Organization Address</th>
            <th>Technical Contact</th>
            <th>Billing Contact</th>
            <th>Billing Address</th>
            <th>Installation Address</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $query = "SELECT * FROM clients ORDER BY client_id DESC";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
    ?>
        <tr>
            <td><?= htmlspecialchars($row['client_name']) ?></td>
            <td>
                <?= htmlspecialchars($row['organization_address1']) ?><br>
                <?= htmlspecialchars($row['organization_address2']) ?><br>
                <?= htmlspecialchars($row['organization_address3']) ?><br>
                <?= htmlspecialchars($row['organization_city']) ?>, <?= htmlspecialchars($row['organization_state']) ?><br>
                Pin: <?= htmlspecialchars($row['organization_pin_code']) ?>
            </td>
            <td>
                <?= htmlspecialchars($row['technical_full_name']) ?> (<?= htmlspecialchars($row['technical_designation']) ?>)<br>
                Mobile: <?= htmlspecialchars($row['technical_mobile_no']) ?><br>
                Telephone: <?= htmlspecialchars($row['technical_telephone']) ?><br>
                Fax: <?= htmlspecialchars($row['technical_fax_no']) ?><br>
                Email: <?= htmlspecialchars($row['technical_email']) ?>
            </td>
            <td>
                <?= htmlspecialchars($row['billing_full_name']) ?> (<?= htmlspecialchars($row['billing_designation']) ?>)<br>
                Mobile: <?= htmlspecialchars($row['billing_mobile_no']) ?><br>
                Telephone: <?= htmlspecialchars($row['billing_telephone']) ?><br>
                Fax: <?= htmlspecialchars($row['billing_fax_no']) ?><br>
                Email: <?= htmlspecialchars($row['billing_email']) ?>
            </td>
            <td>
                <?= htmlspecialchars($row['billing_address1']) ?><br>
                <?= htmlspecialchars($row['billing_address2']) ?><br>
                <?= htmlspecialchars($row['billing_address3']) ?><br>
                <?= htmlspecialchars($row['billing_city']) ?>, <?= htmlspecialchars($row['billing_state']) ?><br>
                Pin: <?= htmlspecialchars($row['billing_pin_code']) ?>
            </td>
            <td>
                <?= htmlspecialchars($row['installation_address1']) ?><br>
                <?= htmlspecialchars($row['installation_address2']) ?><br>
                <?= htmlspecialchars($row['installation_address3']) ?><br>
                <?= htmlspecialchars($row['installation_city']) ?>, <?= htmlspecialchars($row['installation_state']) ?><br>
                Pin: <?= htmlspecialchars($row['installation_pin_code']) ?>
            </td>
            <td class="action-buttons">
            <a class="button" href="edit_client.php?client_id=<?= $row['client_id'] ?>">Edit</a>
                <a class="button" href="delete_client.php?id=<?= $row['client_id'] ?>" onclick="return confirm('Are you sure to delete this client?')">Delete</a>
            </td>
        </tr>
    <?php
        endwhile;
    else:
    ?>
        <tr><td colspan="7">No clients found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
