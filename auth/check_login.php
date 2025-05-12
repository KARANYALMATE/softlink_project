<?php
session_start();

function checkAdminLogin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../auth/login.php');
        exit();
    }
}

function checkUserLogin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
        header('Location: ../auth/login.php');
        exit();
    }
}
