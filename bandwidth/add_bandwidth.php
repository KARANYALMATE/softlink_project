<?php
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "admin";
    $_SESSION['role'] = "admin";
}

$isAdminPage = true;
include_once '../includes/sidebar.php';

require_once '../config/db.php'; 

function getId($conn, $table, $id_col, $name_col, $value) {
    $stmt = $conn->prepare("SELECT $id_col FROM $table WHERE $name_col = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row[$id_col];
    } else {
        $insert = $conn->prepare("INSERT INTO $table ($name_col) VALUES (?)");
        $insert->bind_param("s", $value);
        $insert->execute();
        return $insert->insert_id;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST['client_name'];
    $noc_name = $_POST['noc_name'];
    $loop_type_name = $_POST['loop_type_name'];
    $loop_provider_name = $_POST['loop_provider_name'];
    $circuit_id = $_POST['circuit_id'];
    $bandwidth_mbps = $_POST['bandwidth_mbps'];
    $link_commissioning_date = $_POST['link_commissioning_date'];
    $ip_block_assigned = $_POST['ip_block_assigned'];
    $discount_bandwidth = $_POST['discount_bandwidth'];
    $additional_discount_startup = $_POST['additional_discount_startup'];
    $annual_bandwidth_tariff = $_POST['annual_bandwidth_tariff'];
    $annual_local_loop_tariff = $_POST['annual_local_loop_tariff'];
    $customer_type_name = $_POST['customer_type_name'];
    $remark = $_POST['remark'];

    $client_id = getId($conn, "clients", "client_id", "client_name", $client_name);
    $noc_id = getId($conn, "noc_locations", "noc_location_id", "noc_name", $noc_name);
    $loop_type_id = getId($conn, "loop_types", "loop_type_id", "loop_type_name", $loop_type_name);
    $loop_provider_id = getId($conn, "loop_providers", "loop_provider_id", "loop_provider_name", $loop_provider_name);
    $customer_type_id = getId($conn, "customer_types", "customer_type_id", "customer_type_name", $customer_type_name);

    $stmt = $conn->prepare("INSERT INTO bandwidth_distribution (
        client_id, noc_location_id, loop_type_id, loop_provider_id,
        circuit_id, bandwidth_mbps, link_commissioning_date,
        ip_block_assigned, discount_bandwidth, additional_discount_startup,
        annual_bandwidth_tariff, annual_local_loop_tariff, customer_type_id, remark
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iiiisdsdddddis", $client_id, $noc_id, $loop_type_id, $loop_provider_id,
        $circuit_id, $bandwidth_mbps, $link_commissioning_date,
        $ip_block_assigned, $discount_bandwidth, $additional_discount_startup,
        $annual_bandwidth_tariff, $annual_local_loop_tariff, $customer_type_id, $remark);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => '✔️ Data added successfully!']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Error: ' . $stmt->error]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bandwidth - Softlink</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f4f6f9;
        }

        .content {
            margin-left: 240px; 
            padding: 40px;
        }

        .dashboard {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background-color: rgb(14, 15, 15);
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #1abc9c;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .form-group label {
            width: 200px; /* Adjust label width */
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .form-group input,
        .form-group textarea {
            width: 300px; /* Adjust input width */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .form-group select {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 100px;
        }

        #successMessage {
            display: none;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        #errorMessage {
            display: none;
            background-color: #dc3545;
            color: white;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="dashboard">
            <h2>📍 Bandwidth Form</h2>

            <!-- Success and Error Messages -->
            <div id="successMessage">✔️ Data added successfully!</div>
            <div id="errorMessage">❌ Error occurred while submitting data!</div>

            <form id="bandwidthForm" method="POST" action="">
                <div class="form-group">
                    <label for="client_name">Client Name</label>
                    <input type="text" name="client_name" placeholder="Client Name" required>
                </div>
                
                <div class="form-group">
                    <label for="noc_name">NOC Location</label>
                    <input type="text" name="noc_name" placeholder="NOC Location" required>
                </div>
                
                <div class="form-group">
                    <label for="loop_type_name">Loop Type</label>
                    <select name="loop_type_name" id="loop_type_name" required>
                        <option value="" disabled selected>Select Loop Type</option>
                        <option value="UTP/ LAN">UTP/ LAN</option>
                        <option value="Radio">Radio</option>
                        <option value="Fiber">Fiber</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="loop_provider_name">Loop Provider</label>
                    <select name="loop_provider_name" id="loop_provider_name" required>
                        <option value="" disabled selected>Select Loop Provider</option>
                        <option value="STPI">STPI</option>
                        <option value="STPI(TATA)">STPI(TATA)</option>
                        <option value="CUSTOMER OWNED ">CUSTOMER OWNED </option>
                        <option value="BSNL">BSNL</option>
                        <option value="Airtel">Airtel</option>
                        <option value="Jio">Jio</option>
                        <option value="Vodafone">Vodafone</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="circuit_id">Circuit ID</label>
                    <input type="text" name="circuit_id" placeholder="Circuit ID" required>
                </div>
                
                <div class="form-group">
                    <label for="bandwidth_mbps">Bandwidth (Mbps)</label>
                    <input type="number" name="bandwidth_mbps" placeholder="Bandwidth (Mbps)" required>
                </div>
                
                <div class="form-group">
                    <label for="link_commissioning_date">Link Commissioning Date</label>
                    <input type="date" name="link_commissioning_date" required>
                </div>
                
                <div class="form-group">
                    <label for="ip_block_assigned">IP Block Assigned</label>
                    <input type="text" name="ip_block_assigned" placeholder="IP Block Assigned">
                </div>
                
                <div class="form-group">
                    <label for="discount_bandwidth">Discount Bandwidth</label>
                    <input type="number" name="discount_bandwidth" placeholder="Discount Bandwidth" step="any" min="0">
                </div>
                
                <div class="form-group">
                    <label for="additional_discount_startup">Additional Discount for Startups</label>
                    <input type="number" name="additional_discount_startup" placeholder="Additional Discount for Startups" step="any" min="0">
                </div>
                
                <div class="form-group">
                    <label for="annual_bandwidth_tariff">Annual Bandwidth Tariff</label>
                    <input type="number" name="annual_bandwidth_tariff" placeholder="Annual Bandwidth Tariff"step="any" min="0">
                </div>
                
                <div class="form-group">
                    <label for="annual_local_loop_tariff">Annual Local Loop Tariff</label>
                    <input type="number" name="annual_local_loop_tariff" placeholder="Annual Local Loop Tariff" step="any" min="0">
                </div>
                
                <div class="form-group">
                    <label for="customer_type_name">Customer Type</label>
                    <select name="customer_type_name" id="customer_type_name" required>
                        <option value="" disabled selected>Select Customer Type</option>
                        <option value="Govt">Govt</option>
                        <option value="Private">Private</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="remark">Remark</label>
                    <textarea name="remark" placeholder="Remark"></textarea>
                </div>

                <button type="submit" id="submitButton">Submit</button>
            </form>
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
            $('#bandwidthForm').on('submit', function (e) {
                e.preventDefault();

                // Disable submit button to prevent multiple submissions
                $('#submitButton').prop('disabled', true);

                $.ajax({
    url: '',  
    type: 'POST',
    data: $(this).serialize(),
    success: function(response) {
        console.log(response);  // Log the response to see what's coming from the server
        const res = JSON.parse(response);
        if (res.status === 'success') {
            $('#successMessage').fadeIn().delay(3000).fadeOut();
            $('#bandwidthForm')[0].reset(); // Clears the form
        } else {
            $('#errorMessage').text(res.message).fadeIn().delay(3000).fadeOut();
        }
    },
    error: function() {
        $('#errorMessage').text('❌ Error occurred while submitting data!').fadeIn().delay(3000).fadeOut();
    },
    complete: function() {
        // Re-enable submit button
        $('#submitButton').prop('disabled', false);
    }
});

            });
        });
    </script>

</body>
</html>
