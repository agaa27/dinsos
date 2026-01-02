<div class="sidebar">
  <h4 class="text-center mb-4">Dinsos - PM</h4>

  <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="rekap.php" class="<?= basename($_SERVER['PHP_SELF']) == 'rekap.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-pie-chart-fill"></i> Rekapitulasi
  </a>

  <!-- Dropdown Sekretariat -->
  <a class="d-flex justify-content-between align-items-center"
     data-bs-toggle="collapse"
     href="#menuDropdown"
     role="button"
     aria-controls="menuDropdown">
    <span><i class="bi bi-cup-hot"></i> Sekretariat</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu" id="menuDropdown">
    <a href="menus.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == '') ? 'active fw-bold text-primary' : '' ?>">All Menu</a>
    <a href="menus.php?category=coffee" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'coffee') ? 'active fw-bold text-primary' : '' ?>">Coffee</a>
    <a href="menus.php?category=non-coffee" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'non-coffee') ? 'active fw-bold text-primary' : '' ?>">Non-Coffee</a>
    <a href="menus.php?category=main-course" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'main-course') ? 'active fw-bold text-primary' : '' ?>">Main Course</a>
    <a href="menus.php?category=snack" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'snack') ? 'active fw-bold text-primary' : '' ?>">Snacks</a>
  </div>

  <!-- Dropdown Bidang / Seksi -->
  <a class="d-flex justify-content-between align-items-center mt-2"
     data-bs-toggle="collapse"
     href="#bidangDropdown"
     role="button"
     aria-controls="bidangDropdown">
    <span><i class="bi bi-diagram-3-fill"></i> Bidang / Seksi</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu" id="bidangDropdown">
    <a href="seksi-rehabilitasi.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'seksi-rehabilitasi.php' ? 'active fw-bold text-primary' : '' ?>">
      Seksi Rehabilitasi Sosial
    </a>

    <a href="seksi-jaminan.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'seksi-jaminan.php' ? 'active fw-bold text-primary' : '' ?>">
      Seksi Perlindungan & Jaminan Sosial
    </a>

    <a href="seksi-pemberdayaan.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'seksi-pemberdayaan.php' ? 'active fw-bold text-primary' : '' ?>">
      Seksi Pemberdayaan Sosial
    </a>
  </div>

  <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-cart4"></i> Orders
  </a>

  <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-file-earmark-text"></i> Reports
  </a>
</div>
