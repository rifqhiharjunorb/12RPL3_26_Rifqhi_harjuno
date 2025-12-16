<?php
include __DIR__ . '/database.php';

// Pastikan data dikirim lewat POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak diizinkan!");
}

// Ambil data dari form
$id_barang = isset($_POST['id_barang']) ? intval($_POST['id_barang']) : 0;
$jumlah    = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;

// Validasi data
if ($id_barang <= 0 || $jumlah <= 0) {
    die("Data permintaan tidak lengkap atau jumlah tidak valid!");
}

// Cek stok barang
$cek_stok = mysqli_query($conn, "SELECT qty FROM item WHERE id = $id_barang");
$data_stok = mysqli_fetch_assoc($cek_stok);

if (!$data_stok) {
    die("Barang tidak ditemukan!");
}

if ($jumlah > $data_stok['qty']) {
    die("Stok tidak mencukupi!");
}

// Simpan permintaan ke tabel permintaan
$status_awal = "pending";
$query = "INSERT INTO permintaan (id_barang, jumlah, status, created_at) 
          VALUES ($id_barang, $jumlah, '$status_awal', NOW())";
$insert = mysqli_query($conn, $query);

if (!$insert) {
    die("Gagal menyimpan permintaan: " . mysqli_error($conn));
}

// Kurangi stok di tabel item
$new_qty = $data_stok['qty'] - $jumlah;
mysqli_query($conn, "UPDATE item SET qty = $new_qty WHERE id = $id_barang");

// Redirect kembali
header("Location: permintaan.php");
exit();
