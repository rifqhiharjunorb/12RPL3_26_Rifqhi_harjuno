<?php
$host     = "localhost"; // host db dari cpanel InfinityFree
$username = "root";           // username db (cek di cpanel → MySQL Databases)
$password = "";       // password db (yang dibuat waktu buat database)
$dbname   = "projek_urban"; // nama database

// Koneksi mysqli
$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Koneksi mysqli gagal: " . mysqli_connect_error());
}

// Koneksi PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi PDO gagal: " . $e->getMessage());
}
?>
