<?php
include '../config/db.php';

$client_id = $_GET['client_id'] ?? null;
if (!$client_id) {
    echo "Client ID is missing.";
    exit;
}

$query = "SELECT * FROM clients WHERE client_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if (!$client) {
    echo "Client not found.";
    exit;
}

// Function for contact fields
function contactFields($client, $prefix) {
    echo '
    <div class="row mb-3">
        <div class="col"><input type="text" class="form-control" name="'.$prefix.'_full_name" placeholder="Full Name" value="'.htmlspecialchars($client[$prefix.'_full_name']).'"></div>
        <div class="col"><input type="text" class="form-control" name="'.$prefix.'_designation" placeholder="Designation" value="'.htmlspecialchars($client[$prefix.'_designation']).'"></div>
    </div>
    <div class="row mb-3">
        <div class="col"><input type="text" class="form-control" name="'.$prefix.'_telephone" placeholder="Telephone (STD)" value="'.htmlspecialchars($client[$prefix.'_telephone']).'"></div>
        <div class="col"><input type="text" class="form-control" name="'.$prefix.'_mobile_no" placeholder="Mobile No" value="'.htmlspecialchars($client[$prefix.'_mobile_no']).'"></div>
    </div>
    <div class="row mb-3">
        <div class="col"><input type="text" class="form-control" name="'.$prefix.'_fax_no" placeholder="Fax No (STD)" value="'.htmlspecialchars($client[$prefix.'_fax_no']).'"></div>
        <div class="col"><input type="email" class="form-control" name="'.$prefix.'_email" placeholder="Email" value="'.htmlspecialchars($client[$prefix.'_email']).'"></div>
    </div>';
}

// Function for address fields
function addressFields($client, $prefix) {
    echo '
    <div class="row mb-3">
        <div class="col">
            <label>Pin Code</label>
            <input type="text" class="form-control" name="'.$prefix.'_pin_code" id="'.$prefix.'_pincode" value="'.htmlspecialchars($client[$prefix.'_pin_code']).'" onkeyup="fetchCity(\''.$prefix.'_pincode\', \''.$prefix.'_city\')">
        </div>
        <div class="col">
            <label>City</label>
            <input type="text" class="form-control" name="'.$prefix.'_city" id="'.$prefix.'_city" value="'.htmlspecialchars($client[$prefix.'_city']).'">
        </div>
        <div class="col">
            <label>State</label>
            <input type="text" class="form-control" value="Maharashtra" readonly>
        </div>
    </div>
    <input type="text" class="form-control mb-2" name="'.$prefix.'_address1" placeholder="Address 1" value="'.htmlspecialchars($client[$prefix.'_address1']).'">
    <input type="text" class="form-control mb-2" name="'.$prefix.'_address2" placeholder="Address 2" value="'.htmlspecialchars($client[$prefix.'_address2']).'">
    <input type="text" class="form-control mb-3" name="'.$prefix.'_address3" placeholder="Address 3" value="'.htmlspecialchars($client[$prefix.'_address3']).'">';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Client</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <h2>Edit Client Application</h2>
    <form action="update_client.php" method="POST">
        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client['client_id']) ?>">

        <div class="mb-3">
            <label class="form-label">Client Name</label>
            <input type="text" name="client_name" class="form-control" value="<?= htmlspecialchars($client['client_name']) ?>" required>
        </div>

        <h4>Address of the Organization</h4>
        <div class="row mb-3">
            <div class="col">
                <label>Pin Code</label>
                <input type="text" class="form-control" name="organization_pin_code" id="org_pincode" value="<?= htmlspecialchars($client['organization_pin_code']) ?>" onkeyup="fetchCity('org_pincode', 'org_city')">
                
            </div>
            <div class="col">
                <label>City</label>
                <input type="text" class="form-control" name="organization_city" id="org_city" value="<?= htmlspecialchars($client['organization_city']) ?>">
            </div>
            <div class="col">
                <label>State</label>
                <input type="text" class="form-control" value="Maharashtra" readonly>
            </div>
        </div>
        <input type="text" class="form-control mb-2" name="organization_address1" placeholder="Address 1" value="<?= htmlspecialchars($client['organization_address1']) ?>">
        <input type="text" class="form-control mb-2" name="organization_address2" placeholder="Address 2" value="<?= htmlspecialchars($client['organization_address2']) ?>">
        <input type="text" class="form-control mb-3" name="organization_address3" placeholder="Address 3" value="<?= htmlspecialchars($client['organization_address3']) ?>">

        <h4>Technical Contact Details</h4>
        <?php contactFields($client, 'technical'); ?>

        <h4>Billing Contact Details</h4>
        <?php contactFields($client, 'billing'); ?>

        <h5>Billing Address</h5>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="copy_org_to_bill" onclick="copyOrgAddress('billing')">
            <label class="form-check-label">Same as Organization Address</label>
        </div>
        <?php addressFields($client, 'billing'); ?>

        <h5>Installation Address</h5>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="copy_org_to_inst" onclick="copyOrgAddress('installation')">
            <label class="form-check-label">Same as Organization Address</label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="copy_bill_to_inst" onclick="copyBillingAddress()">
            <label class="form-check-label">Same as Billing Address</label>
        </div>
        <?php addressFields($client, 'installation'); ?>

        <button type="submit" class="btn btn-primary mt-3">Update Client</button>
        <a href="manage_clients.php" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<script>
function fetchCity(pinId, cityId) {
    var pincode = document.getElementById(pinId).value;
    if (pincode.length >= 6) {
        $.ajax({
            url: 'get_city.php',
            method: 'POST',
            data: { pincode: pincode },
            success: function(response) {
                const cityField = document.getElementById(cityId);
                cityField.value = response;
                cityField.classList.add('bg-light');
                setTimeout(() => cityField.classList.remove('bg-light'), 1500);
            },
            error: function() {
                alert('City fetch failed. Please enter manually.');
            }
        });
    }
}
function copyOrgAddress(target) {
    ['pin_code', 'address1', 'address2', 'address3', 'city'].forEach(field => {
        $(`input[name="${target}_${field}"]`).val($(`input[name="organization_${field}"]`).val());
    });
}
function copyBillingAddress() {
    ['pin_code', 'address1', 'address2', 'address3', 'city'].forEach(field => {
        $(`input[name="installation_${field}"]`).val($(`input[name="billing_${field}"]`).val());
    });
}
</script>

</body>
</html>
