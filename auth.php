<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkRole($requiredRole) {
    if (!isset($_SESSION['user_role'])) {
        header("Location: login.php");
        exit();
    }

    $currentRole = $_SESSION['user_role'];

    if (is_array($requiredRole)) {
        if (!in_array($currentRole, $requiredRole)) {
            header("Location: login.php");
            exit();
        }
    } else {

        if ($currentRole !== $requiredRole) {
            header("Location: login.php");
            exit();
        }
    }
}

/**
 * CEK apakah user admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * CEK apakah user gudang
 */
function isGudang() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'gudang';
}

/**
 * Redirect otomatis berdasarkan role
 */
function redirectBasedOnRole() {
    if (!isset($_SESSION['user_role'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SESSION['user_role'] === 'admin') {
        header("Location: dashboard.php");
    } else if ($_SESSION['user_role'] === 'gudang') {
        header("Location: dashboard_gudang.php");
    } else {
        // Jika role tidak dikenal, logout saja untuk safety
        header("Location: logout.php");
    }
    exit();
}
?>
