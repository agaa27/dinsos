<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$category    = $_GET['category'] ?? '';

// Dropdown Sekretariat terbuka jika di menus.php
$menuOpen = in_array($currentPage, [
  'perencanaan.php',
  'kepegawaian.php',
  'seksi-pemberdayaan.php'
]);

// Dropdown Bidang / Seksi terbuka jika di halaman seksi
$bidangOpen = in_array($currentPage, [
  'seksi-rehabilitasi.php',
  'seksi-jaminan.php',
  'seksi-pemberdayaan.php'
]);
?>

<div class="sidebar">
  <h4 class="text-center mb-4">Dinsos - PM</h4>

  <a href="dashboard.php"
     class="<?= $currentPage === 'dashboard.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="rekap.php"
     class="<?= $currentPage === 'rekap.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-pie-chart-fill"></i> Rekapitulasi
  </a>

  <!-- DROPDOWN SEKRETARIAT -->
  <a class="d-flex justify-content-between align-items-center"
     data-bs-toggle="collapse"
     href="#menuDropdown"
     role="button"
     aria-expanded="<?= $menuOpen ? 'true' : 'false' ?>"
     aria-controls="menuDropdown">
    <span><i class="bi bi-cup-hot"></i> Sekretariat</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu <?= $menuOpen ? 'show' : '' ?>" id="menuDropdown">
    <a href="perencanaan.php"
       class="<?= ($currentPage === 'perencanaan.php' && $category === '') ? 'active fw-bold text-primary' : '' ?>">
      Keuangan dan Perencanaan
    </a>

    <a href="kepegawaian.php"
       class="<?= ($currentPage === 'kepegawaian.php') ? 'active fw-bold text-primary' : '' ?>">
      Umum dan kepegawaian
    </a>

    <a href="menus.php?category=non-coffee"
       class="<?= ($currentPage === 'menus.php' && $category === 'non-coffee') ? 'active fw-bold text-primary' : '' ?>">
      Non-Coffee
    </a>

    <a href="menus.php?category=main-course"
       class="<?= ($currentPage === 'menus.php' && $category === 'main-course') ? 'active fw-bold text-primary' : '' ?>">
      Main Course
    </a>

    <a href="menus.php?category=snack"
       class="<?= ($currentPage === 'menus.php' && $category === 'snack') ? 'active fw-bold text-primary' : '' ?>">
      Snacks
    </a>
  </div>

  <!-- DROPDOWN BIDANG / SEKSI -->
  <a class="d-flex justify-content-between align-items-center mt-2"
     data-bs-toggle="collapse"
     href="#bidangDropdown"
     role="button"
     aria-expanded="<?= $bidangOpen ? 'true' : 'false' ?>"
     aria-controls="bidangDropdown">
    <span><i class="bi bi-diagram-3-fill"></i> Bidang / Seksi</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu <?= $bidangOpen ? 'show' : '' ?>" id="bidangDropdown">
    <a href="seksi-rehabilitasi.php"
       class="<?= $currentPage === 'seksi-rehabilitasi.php' ? 'active fw-bold text-primary' : '' ?>">
      Seksi Rehabilitasi Sosial
    </a>

    <a href="seksi-jaminan.php"
       class="<?= $currentPage === 'seksi-jaminan.php' ? 'active fw-bold text-primary' : '' ?>">
      Seksi Perlindungan & Jaminan Sosial
    </a>

    <a href="seksi-pemberdayaan.php"
       class="<?= $currentPage === 'seksi-pemberdayaan.php' ? 'active fw-bold text-primary' : '' ?>">
      Seksi Pemberdayaan Sosial
    </a>
  </div>

  <a href="orders.php"
     class="<?= $currentPage === 'orders.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-cart4"></i> Orders
  </a>

  <a href="reports.php"
     class="<?= $currentPage === 'reports.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-file-earmark-text"></i> Reports
  </a>
</div>