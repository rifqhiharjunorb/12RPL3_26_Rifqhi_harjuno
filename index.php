<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

// Cek login
if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$role = strtolower(trim($_SESSION['user_role']));

// Arahkan sesuai role
switch ($role) {
    case 'admin':
        header("Location: dashboard.php");
        break;
    case 'gudang':
        header("Location: dashboard_gudang.php");
        break;
    default:
        // Role tidak dikenal → logout
        session_destroy();
        header("Location: login.php");
        break;
}
exit();
