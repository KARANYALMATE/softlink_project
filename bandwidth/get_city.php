<?php
// (Later you can connect to API. For now hardcode a few samples)
$pincode = $_POST['pincode'];

$pin_city_mapping = [
    "400001" => "Mumbai",
    "411001" => "Pune",
    "421301" => "Thane",
    "400703" => "Navi Mumbai"
];

echo isset($pin_city_mapping[$pincode]) ? $pin_city_mapping[$pincode] : '';
?>
