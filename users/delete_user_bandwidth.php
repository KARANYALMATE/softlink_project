<?php
session_start();
include('../auth/check_login.php'); // ✅ Corrected path
include('../config/db.php');

if (!isset($_GET['id'])) {
    die("No record ID provided.");
}

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id']);

// Step 1: Check if record belongs to the logged-in user
$stmt = $conn->prepare("SELECT id FROM bandwidth_distribution WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Unauthorized action or record not found.");
}

// Step 2: Proceed to delete
$delete = $conn->prepare("DELETE FROM bandwidth_distribution WHERE id = ? AND user_id = ?");
$delete->bind_param("ii", $id, $user_id);

if ($delete->execute()) {
    header("Location: view_my_data.php?status=deleted");
    exit;
} else {
    echo "Failed to delete: " . $conn->error;
}
?>
