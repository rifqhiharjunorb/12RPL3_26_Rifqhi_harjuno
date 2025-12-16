<?php
include __DIR__ . '/database.php';

// Hapus data
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM item WHERE id='$id'");
    header("Location: item.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM item ORDER BY nama_barang ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        margin: 0;
        background: #f4f4f4;
        font-family: Arial, sans-serif;
    }

    /* CONTENT */
    .content {
        margin-left: 240px; /* mengikuti sidebar-root */
        padding: 20px;
    }

    /* HAMBURGER MOBILE */
    .hamburger {
        display: none;
        font-size: 26px;
        cursor: pointer;
        margin-right: 10px;
    }

    @media(max-width:768px){
        .hamburger { display: block; }
        .content { margin-left: 0; }
        .sidebar-root { transform: translateX(-100%); }
        .sidebar-root.active { transform: translateX(0); }
    }
</style>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar-root" id="sidebar">
    <?php include __DIR__ . '/sidebar.php'; ?>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="d-flex align-items-center mb-4">
        <div class="hamburger" onclick="toggleSidebar()">☰</div>
        <h2 class="mb-0">Daftar Item (Hasil Sortir)</h2>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Nama Pemasok</th>
                        <th>Qty</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="<?= $row['qty'] == 0 ? 'table-danger' : '' ?>">
                            <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($row['nama_pemasok']) ?></td>
                            <td><?= $row['qty'] ?></td>
                            <td>
                                <a href="item.php?hapus=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Hapus data ini?')">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    function toggleSidebar(){
        document.getElementById("sidebar").classList.toggle("active");
    }
</script>

</body>
</html>
