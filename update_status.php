<?php
session_start();
include __DIR__ . '/database.php';
include __DIR__ . '/auth.php';

checkRole('gudang');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_permintaan = intval($_POST['id_permintaan']);
    $status = $_POST['status'] ?? 'pending';

    // Ambil jumlah & id_barang dari permintaan
    $q = mysqli_query($conn, "SELECT id_barang, jumlah FROM permintaan WHERE id = $id_permintaan");
    $permintaan = mysqli_fetch_assoc($q);

    if ($permintaan && $status == 'selesai') {
        $id_barang = $permintaan['id_barang'];
        $jumlah = $permintaan['jumlah'];

        // Kurangi stok di tabel item
        mysqli_query($conn, "UPDATE item SET qty = qty - $jumlah WHERE id = $id_barang");

        // Update status permintaan
        mysqli_query($conn, "UPDATE permintaan SET status = 'selesai' WHERE id = $id_permintaan");
    }

    header("Location: dashboard_gudang.php");
    exit();
}
?>
