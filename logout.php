<?php
session_start();

// Hapus semua variabel sesi
$_SESSION = [];

// Hapus cookie sesi (jika ada)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hapus sesi
session_destroy();

// Mencegah cache halaman
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect ke halaman login
header("Location: /PROJEK_URBAN/login.php");
exit;
