<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proses logout jika ada parameter logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: /PROJEK_URBAN/login.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /PROJEK_URBAN/login.php");
    exit();
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Ambil role user dari SESSION AUTH
$role = $_SESSION['user_role'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Projek Urban</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-size: .875rem;
        }
        .navbar {
            z-index: 1000;
        }
    </style>
</head>
<body>

<?php if ($role !== 'gudang'): ?>
<header class="navbar navbar-dark bg-dark sticky-top shadow px-3">
    <span class="navbar-brand mb-0 h1">Projek Urban</span>
    <div class="ms-auto">
        <a href="/PROJEK_URBAN/logout.php"
            class="btn btn-outline-light btn-sm"
            onclick="return confirm('Yakin mau logout?')">
            Logout
        </a>
    </div>
</header>
<?php endif; ?>

<?php
// Tentukan sidebar berdasarkan role (HARUS SESUAI SESSION)
if ($role === 'gudang') {
    include __DIR__ . '/sidebar_gudang.php';
} else {
    include __DIR__ . '/sidebar.php';
}
?>
