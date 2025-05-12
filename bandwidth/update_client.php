<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];

    // General Info
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name']);

    // Organization Address
    $organization_pin_code = $_POST['organization_pin_code'];
    $organization_city = mysqli_real_escape_string($conn, $_POST['organization_city']);
    $organization_address1 = mysqli_real_escape_string($conn, $_POST['organization_address1']);
    $organization_address2 = mysqli_real_escape_string($conn, $_POST['organization_address2']);
    $organization_address3 = mysqli_real_escape_string($conn, $_POST['organization_address3']);

    // Technical Contact
    $technical_full_name = mysqli_real_escape_string($conn, $_POST['technical_full_name']);
    $technical_designation = mysqli_real_escape_string($conn, $_POST['technical_designation']);
    $technical_telephone = $_POST['technical_telephone'];
    $technical_mobile_no = $_POST['technical_mobile_no'];
    $technical_fax_no = $_POST['technical_fax_no'];
    $technical_email = mysqli_real_escape_string($conn, $_POST['technical_email']);

    // Billing Contact
    $billing_full_name = mysqli_real_escape_string($conn, $_POST['billing_full_name']);
    $billing_designation = mysqli_real_escape_string($conn, $_POST['billing_designation']);
    $billing_telephone = $_POST['billing_telephone'];
    $billing_mobile_no = $_POST['billing_mobile_no'];
    $billing_fax_no = $_POST['billing_fax_no'];
    $billing_email = mysqli_real_escape_string($conn, $_POST['billing_email']);

    // Billing Address
    $billing_pin_code = $_POST['billing_pin_code'];
    $billing_city = mysqli_real_escape_string($conn, $_POST['billing_city']);
    $billing_address1 = mysqli_real_escape_string($conn, $_POST['billing_address1']);
    $billing_address2 = mysqli_real_escape_string($conn, $_POST['billing_address2']);
    $billing_address3 = mysqli_real_escape_string($conn, $_POST['billing_address3']);

    // Installation Address
    $installation_pin_code = $_POST['installation_pin_code'];
    $installation_city = mysqli_real_escape_string($conn, $_POST['installation_city']);
    $installation_address1 = mysqli_real_escape_string($conn, $_POST['installation_address1']);
    $installation_address2 = mysqli_real_escape_string($conn, $_POST['installation_address2']);
    $installation_address3 = mysqli_real_escape_string($conn, $_POST['installation_address3']);

    // Update query
    $query = "UPDATE clients SET 
        client_name = '$client_name',
        organization_pin_code = '$organization_pin_code',
        organization_city = '$organization_city',
        organization_address1 = '$organization_address1',
        organization_address2 = '$organization_address2',
        organization_address3 = '$organization_address3',

        technical_full_name = '$technical_full_name',
        technical_designation = '$technical_designation',
        technical_telephone = '$technical_telephone',
        technical_mobile_no = '$technical_mobile_no',
        technical_fax_no = '$technical_fax_no',
        technical_email = '$technical_email',

        billing_full_name = '$billing_full_name',
        billing_designation = '$billing_designation',
        billing_telephone = '$billing_telephone',
        billing_mobile_no = '$billing_mobile_no',
        billing_fax_no = '$billing_fax_no',
        billing_email = '$billing_email',

        billing_pin_code = '$billing_pin_code',
        billing_city = '$billing_city',
        billing_address1 = '$billing_address1',
        billing_address2 = '$billing_address2',
        billing_address3 = '$billing_address3',

        installation_pin_code = '$installation_pin_code',
        installation_city = '$installation_city',
        installation_address1 = '$installation_address1',
        installation_address2 = '$installation_address2',
        installation_address3 = '$installation_address3'

        WHERE client_id = '$client_id'";

    if (mysqli_query($conn, $query)) {
        header("Location: manage_clients.php?success=1");
        exit;
    } else {
        echo "Update failed: " . mysqli_error($conn);
    }
}
?>
