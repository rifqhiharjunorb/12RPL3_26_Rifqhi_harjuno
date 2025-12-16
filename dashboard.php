<?php

include __DIR__ . '/database.php';
require __DIR__ . '/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$hasil_sortir = [];
$msg = '';

if (isset($_POST['hapus_semua'])) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
    mysqli_query($conn, "TRUNCATE TABLE permintaan");
    mysqli_query($conn, "TRUNCATE TABLE item");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    $msg = "Semua data berhasil direset.";
}

if (isset($_GET['hapus_id'])) {
    $hapus_id = (int)$_GET['hapus_id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM item WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $hapus_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $msg = "Item berhasil dihapus.";
}

// ==============================
// UPLOAD & RAPIHKAN DATA
// ==============================
if (isset($_POST['rapihkan']) && isset($_FILES['file_excel']) && is_uploaded_file($_FILES['file_excel']['tmp_name'])) {
    $file_tmp = $_FILES['file_excel']['tmp_name'];
    try {
        $spreadsheet = IOFactory::load($file_tmp);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $data_items = [];

        foreach ($rows as $i => $row) {
            if ($i === 1) continue; // lewati header

            $kode_barang  = trim((string)($row['C'] ?? ''));
            $nama_barang  = trim((string)($row['D'] ?? ''));
            $nama_pemasok = trim((string)($row['F'] ?? ''));
            $qty_raw      = $row['H'] ?? 0;

            $qty = is_numeric($qty_raw) ? (int)$qty_raw : (int)preg_replace('/[^\d-]/', '', (string)$qty_raw);

            if ($nama_barang === '' && $kode_barang === '' && $qty === 0) continue;

            $data_items[] = [
                'kode_barang' => $kode_barang,
                'nama_barang' => $nama_barang,
                'nama_pemasok' => $nama_pemasok,
                'qty' => $qty
            ];
        }

        // Isi pemasok kosong berdasarkan nama barang
        $map_pemasok = [];
        foreach ($data_items as &$item) {
            if ($item['nama_pemasok'] !== '') {
                $map_pemasok[$item['nama_barang']] = $item['nama_pemasok'];
            }
        }
        unset($item);

        foreach ($data_items as &$item) {
            if ($item['nama_pemasok'] === '') {
                $item['nama_pemasok'] = $map_pemasok[$item['nama_barang']] ?? $item['nama_barang'];
            }
        }
        unset($item);

        // Gabungkan qty
        $final_items = [];
        foreach ($data_items as $item) {
            $key = $item['nama_barang'] . '||' . $item['nama_pemasok'];
            if (isset($final_items[$key])) {
                $final_items[$key]['qty'] += $item['qty'];
            } else {
                $final_items[$key] = $item;
            }
        }

        // Reset tabel & insert baru
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
        mysqli_query($conn, "TRUNCATE TABLE item");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

        $stmt = mysqli_prepare($conn, "INSERT INTO item (kode_barang, nama_barang, nama_pemasok, qty) VALUES (?, ?, ?, ?)");
        foreach ($final_items as $item) {
            mysqli_stmt_bind_param($stmt, "sssi", $item['kode_barang'], $item['nama_barang'], $item['nama_pemasok'], $item['qty']);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);

        $msg = "Data berhasil dirapihkan.";
    } catch (Throwable $e) {
        $msg = "Gagal membaca file: " . $e->getMessage();
    }
}

// ==============================
// EXPORT CSV
// ==============================
if (isset($_GET['export']) && $_GET['export'] !== '') {
    ob_clean();
    $pemasok = $_GET['export'];

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $pemasok . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nama Barang', 'Qty']);

    $stmt = mysqli_prepare($conn, "SELECT nama_barang, SUM(qty) as qty FROM item WHERE nama_pemasok=? GROUP BY nama_barang ORDER BY nama_barang ASC");
    mysqli_stmt_bind_param($stmt, "s", $pemasok);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, [$row['nama_barang'], $row['qty']]);
    }

    fclose($output);
    exit;
}

// ==============================
// AMBIL DATA
// ==============================
$q = mysqli_query($conn, "SELECT id, nama_pemasok, nama_barang, qty FROM item ORDER BY nama_pemasok ASC, nama_barang ASC");
while ($r = mysqli_fetch_assoc($q)) {
    $hasil_sortir[$r['nama_pemasok']][] = [
        'id' => $r['id'],
        'nama_barang' => $r['nama_barang'],
        'qty' => $r['qty']
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 250px; flex-shrink: 0; }
        .content { flex-grow: 1; padding: 20px; }
        .table-danger { background-color: #f8d7da !important; }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php include __DIR__ . '/sidebar.php'; ?>
    </div>

    <div class="content bg-light">
        <h2 class="mb-3">Dashboard</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php
        // Check for pending requests not completed by gudang
        $pendingQuery = mysqli_query($conn, "SELECT COUNT(*) as pending_count FROM permintaan WHERE status != 'selesai'");
        $pending = mysqli_fetch_assoc($pendingQuery);
        if ($pending['pending_count'] > 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Ada <?= $pending['pending_count'] ?> permintaan barang yang belum diselesaikan oleh gudang.
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="file" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
                </div>
                <div class="col-auto">
                    <button type="submit" name="rapihkan" class="btn btn-primary">Rapihkan</button>
                </div>
                <div class="col-auto">
                    <button type="submit" name="hapus_semua" class="btn btn-danger" onclick="return confirm('Hapus semua data?')">Hapus Semua</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php if (empty($hasil_sortir)): ?>
                <div class="col-12"><div class="alert alert-warning">Belum ada data.</div></div>
            <?php else: ?>
                <?php foreach ($hasil_sortir as $pemasok => $items): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong><?= htmlspecialchars($pemasok ?: 'Tanpa Pemasok') ?></strong>
                                <a href="?export=<?= urlencode($pemasok) ?>" class="btn btn-success btn-sm">Export</a>
                            </div>
                            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th width="80" class="text-end">Qty</th>
                                            <th width="80" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr class="<?= ((int)$item['qty'] === 0) ? 'table-danger' : '' ?>">
                                                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                <td class="text-end"><?= (int)$item['qty'] ?></td>
                                                <td class="text-center">
                                                    <a href="?hapus_id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus item ini?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>