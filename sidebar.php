<?php
if (!function_exists('is_active')) {
    function is_active($path) {
        $current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if ($current === '' || $current === 'index.php') {
            $current = 'dashboard.php';
        }
        return $current === basename($path) ? 'active' : '';
    }
}

$base = '/PROJEK_URBAN';
?>
<div class="sidebar-root">

    <div class="sidebar-brand">

        <!-- LOGO ONLY LINK -->
        <a href="<?php echo $base; ?>/dashboard.php" class="brand-logo-link">
            <img src="<?php echo $base; ?>/nagaterbang.jpg"
                 alt="Logo"
                 class="brand-logo"
                 onerror="this.style.display='none'">
        </a>

        <!-- NAMA PT TIDAK JADI LINK -->
        <div class="brand-text">
            <span class="company-name">PT Naga Terbang Abadi</span>
        </div>

    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li><a href="<?php echo $base; ?>/dashboard.php" class="nav-link <?php echo is_active('dashboard.php'); ?>">Dashboard</a></li>
            <li><a href="<?php echo $base; ?>/item.php" class="nav-link <?php echo is_active('item.php'); ?>">Data Item</a></li>
            <li><a href="<?php echo $base; ?>/permintaan.php" class="nav-link <?php echo is_active('permintaan.php'); ?>">Permintaan</a></li>
            <li><a href="<?php echo $base; ?>/input_data.php" class="nav-link <?php echo is_active('input_data.php'); ?>">Tambahan Data</a></li>
            <li><a href="<?php echo $base; ?>/logout.php" class="nav-link" onclick="return confirm('Yakin mau logout?')">Logout</a></li>
        </ul>
    </nav>

</div>

<style>
.sidebar-root {
    width: 240px;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    padding: 18px;
    background: #222528;
    color: #fff;
    box-sizing: border-box;
    overflow-y: auto;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    margin-bottom: 18px;
}

/* FIX: hanya logo yang jadi link */
.brand-logo-link {
    display: inline-block;
    margin-right: 10px;
}

/* logo */
.brand-logo {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 6px;
    background: #fff;
}

/* nama tidak kena link */
.brand-text {
    line-height: 1.2;
}
.company-name {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
}

/* menu */
.nav-list { list-style: none; padding: 0; margin: 0; }
.nav-list li { margin-bottom: 10px; }

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-radius: 8px;
    color: #e6e6e6;
    text-decoration: none;
    transition: background 0.15s, color .15s;
}
.nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
.nav-link.active { background: #fff8d9; color: #1a1a1a; }
</style>

<!-- Modal Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin mau logout?
            </div>
            <div class="modal-footer">
                <a href="<?php echo $base; ?>/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
function showLogoutModal() {
    var myModal = new bootstrap.Modal(document.getElementById('logoutModal'));
    myModal.show();
}
</script>

