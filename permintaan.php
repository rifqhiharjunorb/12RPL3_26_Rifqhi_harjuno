<?php
include __DIR__ . '/database.php';

// Ambil semua barang
$result = mysqli_query($conn, "SELECT * FROM item WHERE qty > 0 ORDER BY nama_barang ASC");
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

$itemsPerSlide = 10;
$totalItems = count($items);
$totalSlides = ceil($totalItems / $itemsPerSlide);

// Ambil permintaan
$resultPermintaan = mysqli_query(
    $conn,
    "SELECT p.id, i.nama_barang, p.jumlah, p.status, p.created_at 
     FROM permintaan p
     JOIN item i ON p.id_barang = i.id
     ORDER BY p.created_at DESC"
);
$permintaan = mysqli_fetch_all($resultPermintaan, MYSQLI_ASSOC);

$permintaanPerSlide = 10;
$totalPermintaan = count($permintaan);
$totalPermintaanSlides = ceil($totalPermintaan / $permintaanPerSlide);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Permintaan Barang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        margin: 0;
        background: #f4f4f4;
        font-family: Arial, sans-serif;
    }

    /* CONTENT FIX */
    .content {
        margin-left: 240px; /* sesuai sidebar-root */
        padding: 20px;
    }

    /* Slide Pagination Button */
    .pageBtn.active,
    .permintaanPageBtn.active {
        background-color: #0d6efd;
        color: white;
    }

    .ellipsis {
        border: none;
        background: transparent;
        cursor: default;
        color: #888;
        margin: 0 4px;
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

    <div class="d-flex align-items-center mb-3">
        <div class="hamburger" onclick="toggleSidebar()">☰</div>
        <h2 class="mb-0">Form Permintaan Barang</h2>
    </div>

    <!-- Search -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Cari barang...">

    <!-- Slide Barang -->
    <div id="slidesContainer">
        <?php for ($s = 0; $s < $totalSlides; $s++): ?>
            <div class="row slide" style="<?= $s === 0 ? '' : 'display:none;' ?>">
                <?php
                $start = $s * $itemsPerSlide;
                $end = min($start + $itemsPerSlide, $totalItems);

                for ($i = $start; $i < $end; $i++):
                    $row = $items[$i];
                ?>
                    <div class="col-md-4 mb-3 item-card" data-kode="<?= htmlspecialchars($row['kode_barang']) ?>">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['nama_barang']) ?></h5>
                                <p class="card-text">Stok: <?= $row['qty'] ?></p>

                                <form method="POST" action="proses_permintaan.php">
                                    <input type="hidden" name="id_barang" value="<?= $row['id'] ?>">

                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control mb-2" min="1" max="<?= $row['qty'] ?>" required>

                                    <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                                </form>

                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Pagination -->
    <div class="text-center mt-2">
        <button id="prevBtn" class="btn btn-secondary btn-sm" disabled>Sebelumnya</button>
        <button id="nextBtn" class="btn btn-secondary btn-sm" <?= $totalSlides <= 1 ? 'disabled' : '' ?>>Berikutnya</button>

        <div id="slidePagination" class="mt-2"></div>
    </div>

    <hr>

    <!-- Permintaan -->
    <h3>Status Permintaan</h3>

    <div id="permintaanSlidesContainer">
        <?php for ($ps = 0; $ps < $totalPermintaanSlides; $ps++): ?>
            <div class="slide-permintaan" style="<?= $ps === 0 ? '' : 'display:none;' ?>">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $startP = $ps * $permintaanPerSlide;
                        $endP = min($startP + $permintaanPerSlide, $totalPermintaan);

                        for ($p = $startP; $p < $endP; $p++):
                            $pm = $permintaan[$p];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($pm['nama_barang']) ?></td>
                                <td><?= $pm['jumlah'] ?></td>
                                <td><?= ucfirst($pm['status']) ?></td>
                                <td><?= $pm['created_at'] ?></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Pagination -->
    <div class="text-center mt-3">
        <button id="prevPermintaanBtn" class="btn btn-secondary btn-sm" disabled>Sebelumnya</button>
        <button id="nextPermintaanBtn" class="btn btn-secondary btn-sm" <?= $totalPermintaanSlides <= 1 ? 'disabled' : '' ?>>Berikutnya</button>
        <div id="permintaanPagination" class="mt-2"></div>
    </div>

</div>

<script>
    function toggleSidebar(){
        document.getElementById("sidebar").classList.toggle("active");
    }

    // ========================= SLIDE BARANG =========================
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlide(i){
        slides.forEach((s, idx)=> s.style.display = idx === i ? '' : 'none');
        currentSlide = i;
        document.getElementById("prevBtn").disabled = i === 0;
        document.getElementById("nextBtn").disabled = i === slides.length - 1;
    }

    document.getElementById("prevBtn").onclick = ()=> showSlide(currentSlide - 1);
    document.getElementById("nextBtn").onclick = ()=> showSlide(currentSlide + 1);

    showSlide(0);

    // ========================= SLIDE PERMINTAAN =========================
    let currentPermintaanSlide = 0;
    const permintaanSlides = document.querySelectorAll('.slide-permintaan');

    function showPermintaanSlide(i){
        permintaanSlides.forEach((s, idx)=> s.style.display = idx === i ? '' : 'none');
        currentPermintaanSlide = i;

        document.getElementById("prevPermintaanBtn").disabled = i === 0;
        document.getElementById("nextPermintaanBtn").disabled = i === permintaanSlides.length - 1;
    }

    document.getElementById("prevPermintaanBtn").onclick = ()=> showPermintaanSlide(currentPermintaanSlide - 1);
    document.getElementById("nextPermintaanBtn").onclick = ()=> showPermintaanSlide(currentPermintaanSlide + 1);

    showPermintaanSlide(0);

</script>

</body>
</html>
