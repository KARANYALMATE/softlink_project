<?php
include '../config/db.php';
session_start();

// Get the branch user's ID from the session
$user_id = $_SESSION['user_id']; // Ensure the session has been started and user_id is stored after login

// Sanitize and retrieve form inputs
$client_name = $_POST['client_name'];

$organization_pin_code = $_POST['organization_pin_code'];
$organization_address1 = $_POST['organization_address1'];
$organization_address2 = $_POST['organization_address2'];
$organization_address3 = $_POST['organization_address3'];
$organization_city = $_POST['organization_city'];

$technical_full_name = $_POST['technical_full_name'];
$technical_designation = $_POST['technical_designation'];
$technical_telephone = $_POST['technical_telephone'];
$technical_mobile_no = $_POST['technical_mobile_no'];
$technical_fax_no = $_POST['technical_fax_no'];
$technical_email = $_POST['technical_email'];

$billing_full_name = $_POST['billing_full_name'];
$billing_designation = $_POST['billing_designation'];
$billing_telephone = $_POST['billing_telephone'];
$billing_mobile_no = $_POST['billing_mobile_no'];
$billing_fax_no = $_POST['billing_fax_no'];
$billing_email = $_POST['billing_email'];

$billing_pin_code = $_POST['billing_pin_code'];
$billing_address1 = $_POST['billing_address1'];
$billing_address2 = $_POST['billing_address2'];
$billing_address3 = $_POST['billing_address3'];
$billing_city = $_POST['billing_city'];

$installation_pin_code = $_POST['installation_pin_code'];
$installation_address1 = $_POST['installation_address1'];
$installation_address2 = $_POST['installation_address2'];
$installation_address3 = $_POST['installation_address3'];
$installation_city = $_POST['installation_city'];

// Insert into the clients table
$sql = "INSERT INTO clients (
    user_id,
    client_name,

    organization_pin_code,
    organization_address1,
    organization_address2,
    organization_address3,
    organization_city,

    technical_full_name,
    technical_designation,
    technical_telephone,
    technical_mobile_no,
    technical_fax_no,
    technical_email,

    billing_full_name,
    billing_designation,
    billing_telephone,
    billing_mobile_no,
    billing_fax_no,
    billing_email,

    billing_pin_code,
    billing_address1,
    billing_address2,
    billing_address3,
    billing_city,

    installation_pin_code,
    installation_address1,
    installation_address2,
    installation_address3,
    installation_city
) VALUES (
    '$user_id',
    '$client_name',

    '$organization_pin_code',
    '$organization_address1',
    '$organization_address2',
    '$organization_address3',
    '$organization_city',

    '$technical_full_name',
    '$technical_designation',
    '$technical_telephone',
    '$technical_mobile_no',
    '$technical_fax_no',
    '$technical_email',

    '$billing_full_name',
    '$billing_designation',
    '$billing_telephone',
    '$billing_mobile_no',
    '$billing_fax_no',
    '$billing_email',

    '$billing_pin_code',
    '$billing_address1',
    '$billing_address2',
    '$billing_address3',
    '$billing_city',

    '$installation_pin_code',
    '$installation_address1',
    '$installation_address2',
    '$installation_address3',
    '$installation_city'
)";

if (mysqli_query($conn, $sql)) {
    echo "Client added successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
