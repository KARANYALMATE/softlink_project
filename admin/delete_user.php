<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';


if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

   
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    
    if ($stmt->execute()) {
        echo "success";  
    } else {
        echo "error";  
    }
} else {
    echo "error";  
}
?>
