<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$category    = $_GET['category'] ?? '';

$inputOpen = in_array($currentPage, [
  'input_data.php',
  'input_undangan.php'
]);

// Dropdown Sekretariat terbuka jika di menus.php
$menuOpen = in_array($currentPage, [
  'perencanaan.php',
  'kepegawaian.php',
  'keuangan.php'
]);

// Dropdown Bidang /  terbuka jika di halaman seksi
$bidangOpen = in_array($currentPage, [
  'rehabilitasi.php',
  'perlindungan.php',
  'pemberdayaan.php'
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
    <i class="bi bi-folder-check"></i> Rekapitulasi
  </a>
  
  <!-- DROPDOWN INPUT DATA -->
  <a class="d-flex justify-content-between align-items-center mt-2"
    data-bs-toggle="collapse"
    href="#inputDropdown"
    role="button"
    aria-expanded="<?= $inputOpen ? 'true' : 'false' ?>"
    aria-controls="inputDropdown">
    <span><i class="bi bi-file-earmark-plus"></i> Input Data</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu <?= $inputOpen ? 'show' : '' ?>" id="inputDropdown">
    <a href="input_data.php"
      class="<?= $currentPage === 'input_data.php' ? 'active fw-bold text-primary' : '' ?>">
      Input Kegiatan
    </a>

    <a href="input_undangan.php"
      class="<?= $currentPage === 'input_undangan.php' ? 'active fw-bold text-primary' : '' ?>">
      Input Undangan
    </a>
  </div>


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

  <div class="collapse submenu <?= $menuOpen ? 'show' : '' ?>" id="menuDropdown">
    <a href="perencanaan.php"
       class="<?= ($currentPage === 'perencanaan.php' && $category === '') ? 'active fw-bold text-primary' : '' ?>">
      Perencanaan dan Keuangan
    </a>

    <a href="kepegawaian.php"
       class="<?= ($currentPage === 'kepegawaian.php') ? 'active fw-bold text-primary' : '' ?>">
      Umum dan kepegawaian
    </a>
  </div>

  <!-- DROPDOWN BIDANG /  -->
  <a class="d-flex justify-content-between align-items-center mt-2"
     data-bs-toggle="collapse"
     href="#bidangDropdown"
     role="button"
     aria-expanded="<?= $bidangOpen ? 'true' : 'false' ?>"
     aria-controls="bidangDropdown">
    <span><i class="bi bi-diagram-3-fill"></i> Bidang Sosial</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu <?= $bidangOpen ? 'show' : '' ?>" id="bidangDropdown">
    <a href="rehabilitasi.php"
       class="<?= $currentPage === 'rehabilitasi.php' ? 'active fw-bold text-primary' : '' ?>">
        Rehabilitasi Sosial
    </a>

    <a href="perlindungan.php"
       class="<?= $currentPage === 'perlindungan.php' ? 'active fw-bold text-primary' : '' ?>">
        Perlindungan & Jaminan Sosial
    </a>

    <a href="pemberdayaan.php"
       class="<?= $currentPage === 'pemberdayaan.php' ? 'active fw-bold text-primary' : '' ?>">
        Pemberdayaan Sosial
    </a>
  </div>

  <a href="pemberdayaanMasyarakat.php"
     class="<?= $currentPage === 'pemberdayaanMasyarakat.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-person-rolodex"></i> Bidang PM
  </a>

</div>