<?php
<<<<<<< Updated upstream
$currentPage = basename($_SERVER['PHP_SELF']);
$category    = $_GET['category'] ?? '';
=======
$isMenuPage = basename($_SERVER['PHP_SELF']) === 'perencanaan.php';
$currentPage = basename($_SERVER['PHP_SELF']);
$category = $_GET['category'] ?? '';
$menuOpen = $isMenuPage;
>>>>>>> Stashed changes

// Dropdown Sekretariat terbuka jika di menus.php
$menuOpen = in_array($currentPage, [
  'perencanaan.php',
<<<<<<< Updated upstream
  'kepegawaian.php',
=======
  'perencanaan2.php',
  'seksi-pemberdayaan.php'
>>>>>>> Stashed changes
]);

// Dropdown Bidang / Seksi terbuka jika di halaman seksi
$bidangOpen = in_array($currentPage, [
<<<<<<< Updated upstream
  'rehabilitasi.php',
  'perlindungan.php',
  'pemberdayaan.php'
=======
  'seksi-rehabilitasi.php',
  'seksi-jaminan.php',
  'seksi-pemberdayaan.php'
>>>>>>> Stashed changes
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
    <span><i class="bi bi-briefcase"></i> Sekretariat</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

<<<<<<< Updated upstream
  <div class="collapse submenu <?= $menuOpen ? 'show' : '' ?>" id="menuDropdown">
    <a href="perencanaan.php"
       class="<?= ($currentPage === 'perencanaan.php' && $category === '') ? 'active fw-bold text-primary' : '' ?>">
      Keuangan dan Perencanaan
=======
<a href="perencanaan.php"
       class="<?= ($currentPage === 'perencanaan.php' && $category === '') ? 'active fw-bold text-primary' : '' ?>">
      Perencanaan dan Keuangan
>>>>>>> Stashed changes
    </a>

    <a href="kepegawaian.php"
       class="<?= ($currentPage === 'kepegawaian.php') ? 'active fw-bold text-primary' : '' ?>">
<<<<<<< Updated upstream
      Umum dan kepegawaian
    </a>
  </div>
=======
      Umum dan Kepegawaian
    </a>
>>>>>>> Stashed changes

  <!-- DROPDOWN BIDANG / SEKSI -->
  <a class="d-flex justify-content-between align-items-center mt-2"
     data-bs-toggle="collapse"
     href="#bidangDropdown"
     role="button"
     aria-expanded="<?= $bidangOpen ? 'true' : 'false' ?>"
     aria-controls="bidangDropdown">
    <span><i class="bi bi-diagram-3-fill"></i> Bidang Seksi</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu <?= $bidangOpen ? 'show' : '' ?>" id="bidangDropdown">
    <a href="rehabilitasi.php"
       class="<?= $currentPage === 'rehabilitasi.php' ? 'active fw-bold text-primary' : '' ?>">
       Seksi Rehabilitasi Sosial
    </a>

    <a href="perlindungan.php"
       class="<?= $currentPage === 'perlindungan.php' ? 'active fw-bold text-primary' : '' ?>">
       Seksi Perlindungan & Jaminan Sosial
    </a>

    <a href="pemberdayaan.php"
       class="<?= $currentPage === 'pemberdayaan.php' ? 'active fw-bold text-primary' : '' ?>">
       Seksi Pemberdayaan Sosial
    </a>
  </div>

<<<<<<< Updated upstream
  <!-- <a href="orders.php"
     class="<?= $currentPage === 'orders.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-cart4"></i> Orders
  </a>

  <a href="reports.php"
     class="<?= $currentPage === 'reports.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-file-earmark-text"></i> Reports
  </a> -->
</div>
=======
  <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-cart4"></i> Laporan Kegiatan
  </a>

</div>
>>>>>>> Stashed changes
