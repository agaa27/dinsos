<?php
require 'config/database.php';
session_start();
// var_dump($_SESSION);die();
//TAMPIL DATA DROPDOWN INDIKATOR DAN TAHUN
/* Ambil indikator khusus bidang Umum dan Kepegawaian */
$qIndikator = $conn->query("
    SELECT id, indikator_kinerja 
    FROM kegiatan 
    WHERE bidang = 'Umum dan Kepegawaian'
    ORDER BY indikator_kinerja ASC
");

/* Ambil tahun (unik / tidak double) */
$qTahun = $conn->query("
    SELECT DISTINCT tahun 
    FROM kegiatan 
    WHERE bidang = 'Umum dan Kepegawaian'
    ORDER BY tahun DESC
");


// AMBIL DATA KEGIATAN
$data = null;

if (isset($_GET['indikator_id'], $_GET['tahun'])) {

    $stmt = $conn->prepare("
        SELECT *
        FROM kegiatan
        WHERE id = ? AND tahun = ?
    ");
    $stmt->bind_param("ii", $_GET['indikator_id'], $_GET['tahun']);
    $stmt->execute();

    $data = $stmt->get_result()->fetch_assoc();
}

//TAMPILKAN DATA PER TRIWULAN
if ($data) {

    $pagu_tahunan = (float) $data['pagu_anggaran'];
    $target = (float) $data['target'];
    $satuan = (float) $data['satuan'];

    $tw = [];
    $total_realisasi = 0;

    for ($i = 1; $i <= 4; $i++) {

        $realisasiT      = (float) ($data["realisasiTW{$i}"] ?? 0);
        $pagu      = (float) ($data["paguTW{$i}"] ?? 0);
        $realisasi = (float) ($data["realisasi_anggaranTW{$i}"] ?? 0);
        $total_realisasi += $realisasi;

        $tw[$i] = [
            'pagu'       => $pagu ?: null,
            'realisasi'  => $realisasi ?: null,
            'realisasiT'  => $realisasiT ?: null,

            // sisa pagu tahunan (akumulatif)
            'sisa'       => $pagu_tahunan - $total_realisasi,

            // persentase terhadap pagu tahunan
            'persentase' => ($pagu_tahunan > 0 && $realisasi > 0)
                            ? round(($realisasi / $pagu_tahunan) * 100, 2)
                            : null,
            
                            // persentase terhadap pagu tahunan
            'persentase_target' => ($realisasiT > 0 && $target > 0)
                            ? round(($realisasiT / $target) * 100, 2)
                            : null
        ];
    }
}

//UPDATE HANDLER

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id = intval($_POST['id']);
  $tahun = intval($_POST['tahun']);

  // Tangkap data TW 1–4
  for ($i = 1; $i <= 4; $i++) {
    $paguTW[$i]       = $_POST["paguTW$i"] ?? 0;
    $realisasiTW[$i]  = $_POST["realisasiTW$i"] ?? 0;
    $anggaranTW[$i]   = $_POST["realisasi_anggaranTW$i"] ?? 0;
  }

  // Query update
  $sql = "UPDATE kegiatan SET
            paguTW1 = ?, realisasiTW1 = ?, realisasi_anggaranTW1 = ?,
            paguTW2 = ?, realisasiTW2 = ?, realisasi_anggaranTW2 = ?,
            paguTW3 = ?, realisasiTW3 = ?, realisasi_anggaranTW3 = ?,
            paguTW4 = ?, realisasiTW4 = ?, realisasi_anggaranTW4 = ?
          WHERE id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "ddddddddddddi",
    $paguTW[1], $realisasiTW[1], $anggaranTW[1],
    $paguTW[2], $realisasiTW[2], $anggaranTW[2],
    $paguTW[3], $realisasiTW[3], $anggaranTW[3],
    $paguTW[4], $realisasiTW[4], $anggaranTW[4],
    $id
  );

  if ($stmt->execute()) {
    $_SESSION['success'] = 'Data berhasil disimpan';
    header("Location: perencanaan.php?indikator_id=$id&tahun=$tahun");
  } else {
    $_SESSION['error'] = 'Data gagal disimpan';
    header("Location: perencanaan.php?indikator_id=$id&tahun=$tahun");
  }

  $stmt->close();
  $conn->close();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Realisasi Triwulan - Dinsos Tarakan</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    background-color: #2c2f33;
    color: white;
    padding-top: 20px;
    overflow-y: auto;
}
.sidebar a {
    color: #ddd;
    text-decoration: none;
    display: block;
    padding: 10px 20px;
    border-radius: 8px;
    margin: 4px 8px;
    transition: all 0.3s;
}
.sidebar a:hover, .sidebar a.active {
    background-color: #343a40;
    color: #fff;
}
.submenu a {
    padding-left: 40px;
    font-size: 13px;
}
.main-content {
    margin-left: 250px;
    min-height: 100vh;
}
.navbar {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
    padding: 10px 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.account-dropdown {
    position: relative;
    display: inline-block;
}
.account-dropdown .dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    padding: 10px;
    border-radius: 10px;
    z-index: 1000;
}
.account-dropdown:hover .dropdown-content {
    display: block;
}
.account-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 25px;
    background-color: white;
}
.card-header {
    background-color: #27ae60;
    color: white;
    border-radius: 10px 10px 0 0 !important;
    padding: 15px 20px;
    font-weight: 600;
}
.btn-success {
    background-color: #27ae60;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-success:hover {
    background-color: #219653;
    transform: translateY(-2px);
}
.btn-primary {
    background-color: #3498db;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-primary:hover {
    background-color: #2980b9;
}
.btn-warning {
    background-color: #f39c12;
    border: none;
    border-radius: 5px;
    padding: 6px 12px;
}
.btn-danger {
    background-color: #e74c3c;
    border: none;
    border-radius: 5px;
    padding: 6px 12px;
}
.btn-outline-success {
    color: #27ae60;
    border-color: #27ae60;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-outline-success:hover {
    background-color: #27ae60;
    color: white;
}
.progress {
    height: 25px;
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
}
.table th {
    background-color: #2c3e50;
    color: white;
    border-color: #34495e;
}
.table td {
    vertical-align: middle;
}
.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.badge-tw {
    font-size: 0.9em;
    padding: 5px 10px;
}
.readonly-input {
    background-color: #f8f9fa;
    cursor: not-allowed;
}
.info-box {
    background-color: #e8f5e8;
    border-left: 4px solid #27ae60;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.empty-state i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}
.empty-state h4 {
    font-size: 1.25rem;
    margin-bottom: 10px;
}
.empty-state p {
    font-size: 0.95rem;
    color: #6c757d;
    margin-bottom: 20px;
}
#currentDateTime {
    color: #6c757d;
    font-size: 0.9rem;
}
.modal-header {
    background-color: #27ae60;
    color: white;
    border-radius: 10px 10px 0 0;
}
.modal-header .btn-close {
    filter: invert(1);
    opacity: 0.8;
}
.modal-header .btn-close:hover {
    opacity: 1;
}
.required::after {
    content: " *";
    color: #e74c3c;
}
.content-wrapper {
    padding: 20px;
    background-color: #f8f9fa;
    min-height: calc(100vh - 70px);
}
.page-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 20px;
}
.filter-required {
    color: #e74c3c;
    font-size: 0.9em;
}
.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 10px 15px;
    transition: all 0.3s;
}
.form-control:focus, .form-select:focus {
    border-color: #27ae60;
    box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
}
.dropdown-item {
    padding: 8px 15px;
    color: #333;
    text-decoration: none;
    display: block;
    border-radius: 5px;
    transition: all 0.3s;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
.table-action-buttons {
    display: flex;
    gap: 5px;
}
.table-action-buttons .btn {
    padding: 5px 10px;
    font-size: 0.875rem;
}
.action-header {
    text-align: center;
}
.btn-add-triwulan {
    background-color: #f39c12;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-add-triwulan:hover {
    background-color: #e67e22;
    transform: translateY(-2px);
}
.bidang-badge {
    font-size: 0.7em;
    margin-left: 5px;
    vertical-align: middle;
}
.option-group {
    font-weight: 600;
    color: #2c3e50;
    background-color: #f8f9fa;
    padding: 8px 15px;
}
.option-item {
    padding-left: 30px;
}
/* Warna untuk bidang */
.bidang-perencanaan { background-color: #3498db; }
.bidang-umum { background-color: #2ecc71; }
.bidang-rehabilitasi { background-color: #e74c3c; }
.bidang-perlindungan { background-color: #9b59b6; }
.bidang-pemberdayaan { background-color: #f39c12; }
</style>
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include "includes/sidebar.php"; ?>

<div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <h5 class="mb-0">Pemberdayaan Sosial</h5>
            <span id="currentDateTime">
                <i class="bi bi-clock"></i> 
                <!-- Date & Time will be inserted here -->
            </span>
            <div class="account-dropdown">
                <button class="btn account-btn d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <h6 class="mb-0">Hello, User Name</h6>
                </button>
                <div class="dropdown-content">
                    <div class="d-flex align-items-center p-2">
                        <i class="bi bi-person-circle fs-3 text-primary me-2"></i>
                        <div>
                            <strong>User Name</strong>
                            <p class="mb-0 text-muted small">Role</p>
                            <p class="mb-0 text-muted small">Bidang</p>
                        </div>
                    </div>
                    <hr class="my-2">
                    <a href="profile.php" class="dropdown-item">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                    <a href="logout.php" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title">
                <i class="bi bi-clipboard-data text-success me-2"></i>Input Realisasi Triwulan
            </h2>
        </div>

        <!-- Alert Messages Section -->
        <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i>
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success']); endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); endif; ?>

        <!-- Filter Form Card -->
        <div class="card mb-4">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">
                    <i class="bi bi-funnel me-2"></i>Filter Data
                </h5>
                <small class="text-white">
                    Pilih indikator untuk melihat / mengisi realisasi
                </small>
            </div>

            <div class="card-body">
                <form method="GET" action="" class="row g-3">

                    <!-- Dropdown Indikator -->
                    <div class="col-md-6">
                        <label class="form-label">Indikator Kinerja</label>
                        <select name="indikator_id" class="form-select" required>
                            <option value="">-- Pilih Indikator --</option>
                            <?php while ($row = $qIndikator->fetch_assoc()) : ?>
                                <option value="<?= $row['id']; ?>"
                                    <?= ($_GET['indikator_id'] ?? '') == $row['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($row['indikator_kinerja']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Dropdown Tahun -->
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            <?php while ($row = $qTahun->fetch_assoc()) : ?>
                                <option value="<?= $row['tahun']; ?>"
                                    <?= ($_GET['tahun'] ?? '') == $row['tahun'] ? 'selected' : ''; ?>>
                                    <?= $row['tahun']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Tombol -->
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>

                </form>
            </div>
        </div>


        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>Detail Realisasi Anggaran
                </h5>
            </div>

            <div class="card-body">

                <?php if (!$data): ?>
                    <div class="text-muted text-center py-4">
                        Pilih indikator dan tahun terlebih dahulu
                    </div>
                <?php else: ?>

                <!-- Informasi Utama -->
                <ul class="list-group mb-4">
                    <li class="list-group-item">
                        <strong>Sasaran:</strong><br>
                        <?= htmlspecialchars($data['sasaran_strategis']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Indikator:</strong><br>
                        <?= htmlspecialchars($data['indikator_kinerja']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Program:</strong><br>
                        <?= htmlspecialchars($data['program']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Target:</strong><br>
                        <?= htmlspecialchars($data['target']) . " " . htmlspecialchars($data['satuan']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Pagu Anggaran Tahunan:</strong> <br>Rp : 
                        <?= htmlspecialchars($data['pagu_anggaran']); ?>
                    </li>
                </ul>

                <!-- Realisasi per TW -->
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="border rounded p-3 mb-3">

                        <h6 class="mb-2">Triwulan <?= $i; ?></h6>

                        <?php if ($tw[$i]['pagu'] === null): ?>
                            <span class="text-muted">Belum ada pagu</span>

                        <?php elseif ($tw[$i]['realisasi'] === null): ?>
                            <ul class="mb-0">
                                <li>Pagu Anggaran:
                                    <strong>
                                        Rp <?= number_format($tw[$i]['pagu'], 0, ',', '.'); ?>
                                    </strong>
                                </li>
                                <li class="text-muted">
                                    Belum ada realisasi
                                </li>
                            </ul>

                        <?php else: ?>
                            <ul class="mb-0">
                                <li>
                                    Realisasi:
                                    <strong>
                                        <?= number_format($tw[$i]['realisasiT'], 0, ',', '.') . " " . $data['satuan']; ?> 
                                    </strong>
                                </li>
                                <li>
                                    Persentase:
                                    <strong><?= $tw[$i]['persentase_target']; ?>%</strong>
                                </li>
                                <li>
                                    Pagu Anggaran:
                                    <strong>
                                        Rp <?= number_format($tw[$i]['pagu'], 0, ',', '.'); ?>
                                    </strong>
                                </li>
                                <li>
                                    Realisasi Anggaran:
                                    <strong>
                                        Rp <?= number_format($tw[$i]['realisasi'], 0, ',', '.'); ?>
                                    </strong>
                                </li>
                                <li>
                                    Persentase Anggaran:
                                    <strong><?= $tw[$i]['persentase']; ?>%</strong>
                                </li>
                                <li>
                                    Sisa Anggaran:
                                    <strong>
                                        Rp <?= number_format($tw[$i]['sisa'], 0, ',', '.'); ?>
                                    </strong>
                                </li>
                            </ul>
                        <?php endif; ?>

                    </div>
                <?php endfor; ?>

                <!-- Tombol Input -->
                <?php if ($_SESSION['role'] === 'umum_kepegawaian'): ?>
                    <div class="text-end">
                        <button class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#modalRealisasi">
                            <i class="bi bi-pencil-square"></i> Input / Edit Realisasi
                        </button>
                    </div>
                <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>


    </div>
</div>

<!-- Modal Input / Edit Realisasi -->
<div class="modal fade" id="modalRealisasi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <form method="POST" action="">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-pencil-square me-2"></i>Input / Edit Realisasi Triwulan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">

          <!-- ID kegiatan -->
          <input type="hidden" name="id" value="<?= $data['id']; ?>">
          <input type="hidden" name="tahun" value="<?= $data['tahun']; ?>">

          <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="border rounded p-3 mb-4">
              <h6 class="mb-3">Triwulan <?= $i; ?></h6>

              <div class="row g-3">

                <!-- Pagu TW -->
                <div class="col-md-4">
                  <label class="form-label">Pagu TW <?= $i; ?></label>
                  <input type="number" step="0.01"
                         name="paguTW<?= $i; ?>"
                         class="form-control"
                         value="<?= $data["paguTW{$i}"]; ?>">
                </div>

                <!-- Realisasi Target -->
                <div class="col-md-4">
                  <label class="form-label">Realisasi Target</label>
                  <input type="number" step="0.01"
                         name="realisasiTW<?= $i; ?>"
                         class="form-control"
                         value="<?= $data["realisasiTW{$i}"]; ?>">
                </div>

                <!-- Realisasi Anggaran -->
                <div class="col-md-4">
                  <label class="form-label">Realisasi Anggaran</label>
                  <input type="number" step="0.01"
                         name="realisasi_anggaranTW<?= $i; ?>"
                         class="form-control"
                         value="<?= $data["realisasi_anggaranTW{$i}"]; ?>">
                </div>

              </div>
            </div>
          <?php endfor; ?>

        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Data
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Batal
          </button>
        </div>

      </div>
    </form>
  </div>
</div>


<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.js"></script>

<!-- Custom JavaScript -->
<script>
// Update waktu secara real-time
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const dateString = now.toLocaleDateString('id-ID', options);
    const timeString = now.toLocaleTimeString('id-ID');
    
    document.getElementById('currentDateTime').innerHTML = 
        `<i class="bi bi-clock"></i> ${dateString} | ${timeString}`;
}

// Update waktu setiap detik
setInterval(updateDateTime, 60*1000);
updateDateTime(); // Panggil sekali saat pertama kali load

// Fungsi untuk konfirmasi hapus
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data realisasi ini?')) {
        window.location.href = 'perencanaan.php?delete=' + id;
    }
}

// Validasi pagu tidak melebihi sisa pagu
function validatePagu(inputId, sisaPagu) {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('change', function() {
            const inputValue = parseFloat(this.value) || 0;
            if (inputValue > sisaPagu) {
                alert(`Pagu anggaran tidak boleh melebihi sisa pagu yang tersedia (Rp ${sisaPagu.toLocaleString('id-ID')})`);
                this.value = sisaPagu;
            }
        });
    }
}

// Validasi realisasi anggaran tidak melebihi pagu anggaran
function validateRealisasiAnggaran(realisasiId, paguId) {
    const realisasiInput = document.getElementById(realisasiId);
    const paguInput = document.getElementById(paguId);
    
    if (realisasiInput && paguInput) {
        realisasiInput.addEventListener('change', function() {
            const paguAnggaran = parseFloat(paguInput.value) || 0;
            const realisasiAnggaran = parseFloat(this.value) || 0;
            
            if (realisasiAnggaran > paguAnggaran) {
                alert(`Realisasi anggaran tidak boleh melebihi pagu anggaran (Rp ${paguAnggaran.toLocaleString('id-ID')})`);
                this.value = paguAnggaran;
            }
        });
    }
}

// Format angka untuk input uang
function formatCurrency(input) {
    input.addEventListener('blur', function() {
        if (this.value && !isNaN(this.value)) {
            this.value = parseFloat(this.value).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    });
    
    input.addEventListener('focus', function() {
        this.value = this.value.replace(/[^\d]/g, '');
    });
}

// Dropdown account hover effect
const accountDropdown = document.querySelector('.account-dropdown');
if (accountDropdown) {
    accountDropdown.addEventListener('mouseenter', function() {
        this.querySelector('.dropdown-content').style.display = 'block';
    });
    
    accountDropdown.addEventListener('mouseleave', function() {
        this.querySelector('.dropdown-content').style.display = 'none';
    });
}

// Auto-focus ke field pertama saat modal dibuka
document.getElementById('addModal')?.addEventListener('shown.bs.modal', function () {
    document.getElementById('modal_triwulan')?.focus();
});
</script>


</body>
</html>
