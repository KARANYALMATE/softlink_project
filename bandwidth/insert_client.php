<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fields = [
        'client_name', 'organization_pin_code', 'organization_address1', 'organization_address2', 'organization_address3',
        'organization_city', 'technical_full_name', 'technical_designation', 'technical_telephone', 'technical_mobile_no',
        'technical_fax_no', 'technical_email', 'billing_full_name', 'billing_designation', 'billing_telephone', 'billing_mobile_no',
        'billing_fax_no', 'billing_email', 'billing_pin_code', 'billing_address1', 'billing_address2', 'billing_address3',
        'billing_city', 'installation_pin_code', 'installation_address1', 'installation_address2', 'installation_address3', 'installation_city'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? '';
    }

    $sql = "INSERT INTO clients 
        (client_name, organization_pin_code, organization_address1, organization_address2, organization_address3, organization_city, organization_state,
         technical_full_name, technical_designation, technical_telephone, technical_mobile_no, technical_fax_no, technical_email,
         billing_full_name, billing_designation, billing_telephone, billing_mobile_no, billing_fax_no, billing_email,
         billing_pin_code, billing_address1, billing_address2, billing_address3, billing_city, billing_state,
         installation_pin_code, installation_address1, installation_address2, installation_address3, installation_city, installation_state)
        VALUES 
        ('{$data['client_name']}', '{$data['organization_pin_code']}', '{$data['organization_address1']}', '{$data['organization_address2']}', '{$data['organization_address3']}',
         '{$data['organization_city']}', 'Maharashtra', '{$data['technical_full_name']}', '{$data['technical_designation']}',
         '{$data['technical_telephone']}', '{$data['technical_mobile_no']}', '{$data['technical_fax_no']}', '{$data['technical_email']}',
         '{$data['billing_full_name']}', '{$data['billing_designation']}', '{$data['billing_telephone']}', '{$data['billing_mobile_no']}',
         '{$data['billing_fax_no']}', '{$data['billing_email']}', '{$data['billing_pin_code']}', '{$data['billing_address1']}',
         '{$data['billing_address2']}', '{$data['billing_address3']}', '{$data['billing_city']}', 'Maharashtra',
         '{$data['installation_pin_code']}', '{$data['installation_address1']}', '{$data['installation_address2']}',
         '{$data['installation_address3']}', '{$data['installation_city']}', 'Maharashtra')";

    if (mysqli_query($conn, $sql)) {
        echo "Client added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
