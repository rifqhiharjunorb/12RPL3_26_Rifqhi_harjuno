<?php
include __DIR__ . '/database.php';
include __DIR__ . '/auth.php';
include __DIR__ . '/sidebar_gudang.php';

// Cek role gudang atau admin
checkRole(['gudang', 'admin']);

// Ambil data permintaan yang belum selesai
$result = mysqli_query($conn, "SELECT p.id, i.nama_barang, p.jumlah, p.status, p.created_at 
    FROM permintaan p 
    JOIN item i ON p.id_barang = i.id 
    WHERE p.status != 'selesai' 
    ORDER BY p.created_at DESC");
$permintaan = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ambil data permintaan yang sudah selesai untuk history
$resultHistory = mysqli_query($conn, "SELECT p.id, i.nama_barang, p.jumlah, p.status, p.created_at 
    FROM permintaan p 
    JOIN item i ON p.id_barang = i.id 
    WHERE p.status = 'selesai' 
    ORDER BY p.created_at DESC");
$history = mysqli_fetch_all($resultHistory, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Gudang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; display: flex; }
        .sidebar { height: 100vh; width: 220px; background-color: #222;
            padding-top: 20px; position: fixed; left: 0; top: 0;
            overflow-y: auto; transition: transform 0.3s ease; }
        .sidebar a { display: block; color: white; padding: 10px 20px; text-decoration: none; }
        .sidebar a:hover { background-color: #575757; }
        .content { flex: 1; padding: 20px; margin-left: 220px; background-color: #f8f9fa; width: 100%; }
        .hamburger { display: none; font-size: 24px; cursor: pointer; color: white; padding: 10px 20px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .hamburger { display: block; background-color: #222; }
            .content { margin-left: 0; }
        }

        /* ============================
           PERBAIKAN WARNA TAB
        ============================ */
        .nav-tabs .nav-link {
            color: #555 !important;
            font-weight: 600;
            opacity: 0.6;
            background-color: #e9e9e9 !important; /* Tab tidak aktif lebih jelas */
        }

        .nav-tabs .nav-link:hover {
            opacity: 1;
            color: #000 !important;
        }

        .nav-tabs .nav-link.active {
            background-color: #ffffff !important;
            color: #000 !important;
            border-color: #dee2e6 #dee2e6 #fff;
            font-weight: 700;
            opacity: 1;
        }
        /* Membuat tab yang tidak aktif lebih jelas */
.nav-tabs .nav-link {
    border: 1px solid #b3b3b3 !important; /* border tab */
    color: #555 !important;
}

/* Tab aktif tetap lebih tegas */
.nav-tabs .nav-link.active {
    background-color: #ffffff !important;
    border-color: #000 !important; 
    color: #000 !important;
    font-weight: bold;
}

/* Hover biar jelas */
.nav-tabs .nav-link:hover {
    background-color: #e6e6e6 !important;
    border-color: #888 !important;
}

    </style>
</head>
<body>
   <div class="sidebar" id="sidebar">
        <?php include __DIR__ . '/sidebar_gudang.php'; ?>
    </div>

    <div class="content">
        <div class="d-flex align-items-center mb-4">
            <span class="hamburger d-md-none" onclick="toggleSidebar()">☰</span>
            <h2 class="mb-0">Dashboard Gudang</h2>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="gudangTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                    type="button" role="tab" aria-controls="pending" aria-selected="true">
                    Permintaan Pending
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history"
                    type="button" role="tab" aria-controls="history" aria-selected="false">
                    History Report
                </button>
            </li>
        </ul>

        <div class="tab-content" id="gudangTabsContent">

            <!-- Pending -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permintaan as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nama_barang']) ?></td>
                            <td><?= $p['jumlah'] ?></td>
                            <td><?= ucfirst($p['status']) ?></td>
                            <td><?= $p['created_at'] ?></td>
                            <td>
                                <form method="POST" action="update_status.php" style="display:inline;">
                                    <input type="hidden" name="id_permintaan" value="<?= $p['id'] ?>">
                                    <button type="submit" name="status" value="selesai" class="btn btn-success btn-sm">
                                        Selesai
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- History -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['nama_barang']) ?></td>
                            <td><?= $h['jumlah'] ?></td>
                            <td><?= ucfirst($h['status']) ?></td>
                            <td><?= $h['created_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }
    </script>
</body>
</html>
