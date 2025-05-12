<?php
session_start();
require_once '../config/db.php';

// ✅ Proper session check for logged-in branch user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Client Application (Branch)</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h1>Application Form (Branch)</h1>
<form action="user_insert_client.php" method="POST">

    <h2>Client Name</h2>
    <input type="text" name="client_name" required><br><br>

    <h2>Address of the Organization</h2>
    Pin Code: <input type="text" name="organization_pin_code" id="org_pincode" onkeyup="fetchCity('org_pincode', 'org_city')"><br><br>
    Address 1: <input type="text" name="organization_address1"><br>
    Address 2: <input type="text" name="organization_address2"><br>
    Address 3: <input type="text" name="organization_address3"><br>
    City: <input type="text" name="organization_city" id="org_city"><br>
    State: <input type="text" value="Maharashtra" readonly><br><br>

    <h2>Technical Contact Details</h2>
    Full Name: <input type="text" name="technical_full_name"><br>
    Designation: <input type="text" name="technical_designation"><br>
    Telephone (STD Code): <input type="text" name="technical_telephone"><br>
    Mobile No: <input type="text" name="technical_mobile_no"><br>
    Fax No (STD Code): <input type="text" name="technical_fax_no"><br>
    Email: <input type="email" name="technical_email"><br><br>

    <h2>Billing Contact Details</h2>
    Full Name: <input type="text" name="billing_full_name"><br>
    Designation: <input type="text" name="billing_designation"><br>
    Telephone (STD Code): <input type="text" name="billing_telephone"><br>
    Mobile No: <input type="text" name="billing_mobile_no"><br>
    Fax No (STD Code): <input type="text" name="billing_fax_no"><br>
    Email: <input type="email" name="billing_email"><br><br>

    <h3>Billing Address of Leased Internet (Soft Link) Service</h3>
    <input type="checkbox" id="copy_org_to_bill" onclick="copyOrgAddress('billing')"> Same as Organization Address<br><br>
    Pin Code: <input type="text" name="billing_pin_code" id="bill_pincode" onkeyup="fetchCity('bill_pincode', 'bill_city')"><br>
    Address 1: <input type="text" name="billing_address1"><br>
    Address 2: <input type="text" name="billing_address2"><br>
    Address 3: <input type="text" name="billing_address3"><br>
    City: <input type="text" name="billing_city" id="bill_city"><br>
    State: <input type="text" value="Maharashtra" readonly><br><br>

    <h3>Installation Address of Leased Internet (Soft Link) Service</h3>
    <input type="checkbox" id="copy_org_to_inst" onclick="copyOrgAddress('installation')"> Same as Organization Address
    <input type="checkbox" id="copy_bill_to_inst" onclick="copyBillingAddress()"> Same as Billing Address<br><br>
    Pin Code: <input type="text" name="installation_pin_code" id="inst_pincode" onkeyup="fetchCity('inst_pincode', 'inst_city')"><br>
    Address 1: <input type="text" name="installation_address1"><br>
    Address 2: <input type="text" name="installation_address2"><br>
    Address 3: <input type="text" name="installation_address3"><br>
    City: <input type="text" name="installation_city" id="inst_city"><br>
    State: <input type="text" value="Maharashtra" readonly><br><br>

    <button type="submit">Submit</button>
</form>

<script>
function fetchCity(pinId, cityId) {
    var pincode = document.getElementById(pinId).value;
    if (pincode.length >= 6) {
        $.ajax({
            url: '../admin/get_city.php',
            method: 'POST',
            data: { pincode: pincode },
            success: function(response) {
                document.getElementById(cityId).value = response;
            }
        });
    }
}

function copyOrgAddress(target) {
    if (target === 'billing' && document.getElementById('copy_org_to_bill').checked) {
        $('input[name="billing_pin_code"]').val($('input[name="organization_pin_code"]').val());
        $('input[name="billing_address1"]').val($('input[name="organization_address1"]').val());
        $('input[name="billing_address2"]').val($('input[name="organization_address2"]').val());
        $('input[name="billing_address3"]').val($('input[name="organization_address3"]').val());
        $('input[name="billing_city"]').val($('input[name="organization_city"]').val());
    } else if (target === 'installation' && document.getElementById('copy_org_to_inst').checked) {
        $('input[name="installation_pin_code"]').val($('input[name="organization_pin_code"]').val());
        $('input[name="installation_address1"]').val($('input[name="organization_address1"]').val());
        $('input[name="installation_address2"]').val($('input[name="organization_address2"]').val());
        $('input[name="installation_address3"]').val($('input[name="organization_address3"]').val());
        $('input[name="installation_city"]').val($('input[name="organization_city"]').val());
    }
}

function copyBillingAddress() {
    if (document.getElementById('copy_bill_to_inst').checked) {
        $('input[name="installation_pin_code"]').val($('input[name="billing_pin_code"]').val());
        $('input[name="installation_address1"]').val($('input[name="billing_address1"]').val());
        $('input[name="installation_address2"]').val($('input[name="billing_address2"]').val());
        $('input[name="installation_address3"]').val($('input[name="billing_address3"]').val());
        $('input[name="installation_city"]').val($('input[name="billing_city"]').val());
    }
}
</script>

</body>
</html>
