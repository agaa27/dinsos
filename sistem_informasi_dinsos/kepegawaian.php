<?php
require 'config/database.php';
session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}


if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $jabatan = explode(" ", $username);  
}



$qIndikator = $conn->query("
    SELECT id, sub_kegiatan 
    FROM kegiatan 
    WHERE bidang = 'Umum dan Kepegawaian'
    AND tahun >= YEAR(CURDATE()) - 4
    ORDER BY sub_kegiatan ASC
");

/* Ambil tahun (unik / tidak double) */
$qTahun = $conn->query("
    SELECT DISTINCT tahun   
    FROM kegiatan 
    WHERE bidang = 'Umum dan Kepegawaian'
    AND tahun >= YEAR(CURDATE()) - 4
    ORDER BY tahun DESC
");


// AMBIL DATA KEGIATAN
$data = null;

if (isset($_GET['indikator_id'])) {

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

    $id = $data['id'];
    $tahun = $data['tahun'];
    $pagu_tahunan = (float) $data['pagu_anggaran'];
    $target       = (float) $data['target'];
    $satuan       = $data['satuan'];

    $tw = [];
    $total_realisasi_anggaran = 0;
    $total_realisasi_target   = 0;

    // Mapping bulan per triwulan
    $mapping_tw = [
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12],
    ];

    foreach ($mapping_tw as $i => $bulan_list) {

        $realisasi_anggaran_tw = 0;
        $realisasi_target_tw   = 0;

        foreach ($bulan_list as $bulan) {
            $realisasi_target_tw   += (float) ($data["realisasi_bulan{$bulan}"] ?? 0);
            $realisasi_anggaran_tw += (float) ($data["realisasi_anggaran_bulan{$bulan}"] ?? 0);
        }

        // Akumulasi tahunan
        $total_realisasi_anggaran += $realisasi_anggaran_tw;
        $total_realisasi_target   += $realisasi_target_tw;

        $tw[$i] = [
            'realisasi'   => $realisasi_anggaran_tw ?: null,
            'realisasiT'  => $realisasi_target_tw ?: null,

            // sisa tahunan (akumulatif)
            'sisa'        => $pagu_tahunan - $total_realisasi_anggaran,
            'sisa_target' => $target - $total_realisasi_target,

            // persentase terhadap pagu tahunan
            'persentase' => ($pagu_tahunan > 0 && $realisasi_anggaran_tw > 0)
                ? round(($total_realisasi_anggaran / $pagu_tahunan) * 100, 2)
                : null,

            // persentase terhadap target
            'persentase_target' => ($target > 0 && $realisasi_target_tw > 0)
                ? round(($total_realisasi_target / $target) * 100, 2)
                : null
        ];
    }
}

// TAMPIL DATA PER BULAN
$bulan = [];

$total_realisasi_target   = 0;
$total_realisasi_anggaran = 0;


for ($i = 1; $i <= 12; $i++) {

    $realisasi_target   = (float) ($data["realisasi_bulan{$i}"] ?? 0);
    $realisasi_anggaran = (float) ($data["realisasi_anggaran_bulan{$i}"] ?? 0);

    // Akumulasi tahunan
    $total_realisasi_target   += $realisasi_target;
    $total_realisasi_anggaran += $realisasi_anggaran;

    $bulan[$i] = [
        'realisasi_target'   => $realisasi_target ?: null,
        'realisasi_anggaran' => $realisasi_anggaran ?: null,
        'bukti' => $data["bukti{$i}"] ?? null,

        'sisa_target'   => $target - $total_realisasi_target,
        'sisa_anggaran' => $pagu_tahunan - $total_realisasi_anggaran,

        'persentase_target' => ($target > 0 && $total_realisasi_target > 0)
            ? round(($total_realisasi_target / $target) * 100, 2)
            : null,

        'persentase_anggaran' => ($pagu_tahunan > 0 && $total_realisasi_anggaran > 0)
            ? round(($total_realisasi_anggaran / $pagu_tahunan) * 100, 2)
            : null
    ];
}

$mapping_tw = [
    1 => [1, 2, 3],
    2 => [4, 5, 6],
    3 => [7, 8, 9],
    4 => [10, 11, 12],
];




?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DINSOS PM - Umum dan Kepegawaian</title>
    
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
.bidang-kepegawaian { background-color: #3498db; }
.bidang-umum { background-color: #2ecc71; }
.bidang-rehabilitasi { background-color: #e74c3c; }
.bidang-perlindungan { background-color: #9b59b6; }
.bidang-pemberdayaan { background-color: #f39c12; }

/* notif */
.notif-wrapper {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1055;
    width: auto;
    max-width: 90%;
}

.notif-wrapper .alert {
    min-width: 300px;
    text-align: center;
}

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
            <h5 class="mb-0">Umum dan Kepegawaian</h5>
            <span id="currentDateTime">
                <i class="bi bi-clock"></i> 
                <!-- Date & Time will be inserted here -->
            </span>
            <div class="account-dropdown">
                <button class="btn account-btn d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <h6 class="mb-0">Hallo, <?= $_SESSION['username']; ?> </h6>
                </button>
                <div class="dropdown-content">
                    <div class="d-flex align-items-center p-2">
                        <i class="bi bi-person-circle fs-3 text-primary me-2"></i>
                        <div>
                            <strong><?= $jabatan[0]; ?></strong>
                            <p class="mb-0 text-muted small"><?= $_SESSION['role']; ?></p>
                        </div>
                    </div>
                    <hr class="my-2">
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
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-clipboard-data text-success me-2"></i>Input Realisasi Pagu dan Anggaran
            </h2>
        </div>
        <!-- Tombol Input -->
                <?php if ($_SESSION['role'] === 'Umum dan Kepegawaian'): ?>
                    <div class="text-start mb-1">
                        <a class="btn btn-success" href="editKepegawaian.php">
                            <i class="bi bi-pencil-square"></i> Input / Edit Realisasi
                        </a>
                    </div>

                <?php  else: ?>
                    <div class="text-start mb-1">
                        <p class=" text-muted"><i class="bi bi-info-circle-fill"></i> Hanya bisa di akses staff Umum dan Kepegawaian</p>
                    </div>
                <?php endif; ?>

        <!-- notif  -->

    <?php if (isset($_SESSION['notif'])): ?>
        <div class="notif-wrapper">
            <div class="alert alert-<?= $_SESSION['notif']['type']; ?> alert-dismissible fade show auto-close shadow"
                role="alert">
                <?= $_SESSION['notif']['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php
        unset($_SESSION['notif']);
        endif;
        ?>

        <!-- Filter Form Card -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-secondary text-white">
        <div class="d-flex align-items-center">
            <i class="bi bi-funnel me-2 fs-5"></i>
            <div>
                <h6 class="mb-0 fw-semibold">Filter Data</h6>
                <small class="opacity-75">
                    Pilih Sub Kegiatan untuk melihat / mengisi realisasi
                </small>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="" class="row g-3 align-items-end">

            <!-- Indikator -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Sub Kegiatan
                </label>
                <select name="indikator_id" class="form-select" required>
                    <option value="">-- Pilih Sub Kegiatan --</option>
                    <?php while ($row = $qIndikator->fetch_assoc()) : ?>
                        <option value="<?= $row['id']; ?>"
                            <?= ($_GET['indikator_id'] ?? '') == $row['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row['sub_kegiatan']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Tahun -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    Tahun
                </label>
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

            <div class="col-md-3 d-flex gap-3 align-items-end">
                <button type="submit" class="btn btn-primary btn-filter">
                    <i class="bi bi-search"></i>
                    <span>Tampilkan</span>
                </button>

                <a href="kepegawaian.php" class="btn btn-outline-success btn-filter">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Reset</span>
                </a>
            </div>


        </form>
    </div>

</div>



        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>Detail Realisasi Anggaran Tahunan
                </h5>
            </div>

            <div class="card-body">

                <?php if (!$data): ?>
                    <div class="text-muted text-center py-4">
                        Sesuaikan filter untuk menampilkan data
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
                        <strong>Kegiatan:</strong><br>
                        <?= htmlspecialchars($data['kegiatan']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Sub Kegiatan:</strong><br>
                        <?= htmlspecialchars($data['sub_kegiatan']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Target:</strong><br>
                        <?= number_format($data['target'], 2, ',', '.') . " " . htmlspecialchars($data['satuan']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Pagu Anggaran Tahunan:</strong> <br>Rp : 
                        <?= number_format($data['pagu_anggaran'], 0, ',', '.'); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Tahun:</strong> <br> 
                        <?= htmlspecialchars($data['tahun']); ?>
                    </li>
                </ul>

                <!-- Realisasi per TW -->
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="border rounded p-3 mb-3 position-relative">
                        <h6 class="mb-2">Triwulan <?= $i; ?></h6>

                        <?php if ($tw[$i]['realisasi'] === null): ?>
                            <ul class="mb-0">                                
                                <li class="text-muted">
                                    Belum ada realisasi
                                </li>
                            </ul>
                        <?php else: ?>
                            <ul class="mb-0">
                                <li>
                                    Realisasi Kinerja:
                                    <strong>
                                        <?= number_format($tw[$i]['realisasiT'], 2, ',', '.') . " " . $data['satuan']; ?> 
                                    </strong>
                                </li>
                                <li>
                                    Persentase:
                                    <strong><?= $tw[$i]['persentase_target']; ?>%</strong>
                                </li>
                                <li>
                                    Sisa Target:
                                    <strong><?= $tw[$i]['sisa_target'] . " " . $data['satuan']; ?></strong>
                                </li>
                                <hr>
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

                        <!-- Tombol Lihat Detail -->
                         
                        <div class="text-end mt-3">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                onclick="openDetailTW(<?= $i; ?>)">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </button>
                        </div>
                    </div>

                <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>


    </div>
</div>

<div class="modal fade" id="detailtw" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <div>
                    <h5 class="modal-title mb-1">
                        Detail Triwulan ke <span id="modalTw">-</span>
                    </h5>
                    <small>
                        Sub Kegiatan : <?=$data["sub_kegiatan"];?>
                    </small>
                </div>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3" id="modalBulanContent"></div>
            </div>

            <?php if ($_SESSION['role'] === 'Umum dan Kepegawaian'): ?>
                    <div class="modal-footer">
                        <a href="editKepegawaian.php?indikator_id=<?= $id; ?>&tahun=<?= $tahun; ?>" class="btn btn-success px-4">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </div>
                <?php endif; ?>
            
        </div>
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
        window.location.href = 'kepegawaian.php?delete=' + id;
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

//detail triwulan
const dataBulan = <?= json_encode($bulan); ?>;
const mappingTW = <?= json_encode($mapping_tw); ?>;
const satuan = "<?= $satuan; ?>";

const namaBulan = {
    1: 'Januari',
    2: 'Februari',
    3: 'Maret',
    4: 'April',
    5: 'Mei',
    6: 'Juni',
    7: 'Juli',
    8: 'Agustus',
    9: 'September',
    10: 'Oktober',
    11: 'November',
    12: 'Desember'
};


function openDetailTW(tw) {

    // Judul
    document.getElementById('modalTw').innerText = tw;

    const container = document.getElementById('modalBulanContent');
    container.innerHTML = '';

    mappingTW[tw].forEach(bln => {
        const b = dataBulan[bln];

        container.innerHTML += `
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-center fw-bold mb-3 fs-4 text-success">
                        ${namaBulan[bln]}
                    </h6>


                    <ul class="list-unstyled small mb-0">
                        <li><span class='fs-6 text-primary'><strong>Realisasi Kinerja:</strong>
                            ${b.realisasi_target ?? '-'} ${b.realisasi_target ? satuan : ''}</span>
                        </li>
                        <li><strong>Persentase Target:</strong>
                            ${b.persentase_target ?? '-'}%
                        </li>
                        <li><strong>Sisa Target:</strong>
                            ${b.sisa_target} ${satuan}
                        </li>
                        <hr>
                        <li><span class='fs-6 text-primary'><strong>Realisasi Anggaran:</strong>
                            ${b.realisasi_anggaran
                                ? 'Rp ' + Number(b.realisasi_anggaran).toLocaleString('id-ID')
                                : '-'}</span>
                        </li>
                        <li><strong>Persentase Anggaran:</strong>
                            ${b.persentase_anggaran ?? '-'}%
                        </li>
                        <li><strong>Sisa Anggaran:</strong>
                            Rp ${Number(b.sisa_anggaran).toLocaleString('id-ID')}
                        </li>
                        <hr>
                        <li><strong>Bukti Pendukung:</strong>
                            ${
                                b.bukti
                                ? `<a href="file bukti/${b.bukti}" target="_blank">${b.bukti}</a>`
                                : `<span class="text-muted">-</span>`
                            }
                        </li>
                    </ul>
                </div>
            </div>
        `;
    });

    // TAMPILKAN MODAL (MANUAL)
    const modal = new bootstrap.Modal(document.getElementById('detailtw'));
    modal.show();
}



// notif 
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        let alert = document.querySelector('.auto-close');
        if (alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 2000); // durasi 3 detik
});



</script>


</body>
</html>
