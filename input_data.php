<?php
include __DIR__ . '/database.php';

// Ambil daftar nama pemasok yang unik dari database
$resultPemasok = mysqli_query($conn, "SELECT DISTINCT nama_pemasok FROM item ORDER BY nama_pemasok ASC");
$pemasokList = mysqli_fetch_all($resultPemasok, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = $_POST['kode_barang'] ?? '';
    $nama_barang = $_POST['nama_barang'] ?? '';
    $nama_pemasok = $_POST['nama_pemasok'] ?? '';
    $qty = $_POST['qty'] ?? 0;

    if ($kode_barang && $nama_barang && $nama_pemasok && is_numeric($qty)) {
        $kode_barang = mysqli_real_escape_string($conn, $kode_barang);
        $nama_barang = mysqli_real_escape_string($conn, $nama_barang);
        $nama_pemasok = mysqli_real_escape_string($conn, $nama_pemasok);
        $qty = intval($qty);

        $query = "INSERT INTO item (kode_barang, nama_barang, nama_pemasok, qty)
                  VALUES ('$kode_barang', '$nama_barang', '$nama_pemasok', $qty)";

        if (mysqli_query($conn, $query)) {
            $message = "Data berhasil ditambahkan.";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    } else {
        $message = "Semua field harus diisi dengan benar.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 220px;
            background-color: #222;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        /* Konten */
        .content {
            flex: 1;
            padding: 20px;
            margin-left: 240px;  /* Jarak sidebar */
            background-color: #f8f9fa;
            max-width: calc(100% - 220px);
        }

        /* Tombol Hamburger */
        .hamburger {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: white;
            padding: 10px 20px;
        }

        /* Responsif Mobile */
        @media (max-width: 768px) {

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .hamburger {
                display: block;
                background-color: #222;
            }

            .content {
                margin-left: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>


    <!-- Konten -->
    <div class="content">
        <div class="d-flex align-items-center mb-4">
            <span class="hamburger d-md-none" onclick="toggleSidebar()">☰</span>
            <h2 class="mb-0">Tambah Data Item</h2>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="input_data.php">
            <div class="mb-3">
                <label for="kode_barang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
            </div>

            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            </div>

            <div class="mb-3">
                <label for="nama_pemasok" class="form-label">Nama Pemasok</label>
                <select class="form-control" id="nama_pemasok" name="nama_pemasok" required>
                    <option value="">Pilih Pemasok</option>
                    <?php foreach ($pemasokList as $pemasok): ?>
                        <option value="<?= htmlspecialchars($pemasok['nama_pemasok']) ?>">
                            <?= htmlspecialchars($pemasok['nama_pemasok']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="qty" class="form-label">Qty</label>
                <input type="number" class="form-control" id="qty" name="qty" min="0" required>
            </div>

            <button type="submit" class="btn btn-primary">Tambah Data</button>
        </form>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>

</body>
</html>
