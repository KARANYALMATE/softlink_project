<?php

require_once '../config/db.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM bandwidth_distribution WHERE id = ?";
    
   
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
           
            header("Location: view_bandwidth.php?delete=success");
            exit;
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
} else {
    echo "Invalid ID!";
}

$conn->close();
