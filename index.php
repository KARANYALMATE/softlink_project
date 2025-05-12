<?php
session_start();

// Redirect based on login status and role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/admin_dashboard.php');
    } else {
        header('Location: users/dashboard.php');
    }
    exit();
} else {
    header('Location: auth/login.php');
    exit();
}
