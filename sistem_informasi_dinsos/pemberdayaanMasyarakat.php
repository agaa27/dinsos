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
    WHERE bidang = 'Pemberdayaan Masyarakat'
    AND tahun >= YEAR(CURDATE()) - 4
    ORDER BY sub_kegiatan ASC
");

/* Ambil tahun (unik / tidak double) */
$qTahun = $conn->query("
    SELECT DISTINCT tahun   
    FROM kegiatan 
    WHERE bidang = 'Pemberdayaan Masyarakat'
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
    <link rel="icon" type="image/png" sizes="32x32" href="assets/image/dinsos_logo.png">
  <title>DINSOS PM - Pemberdayaan Masyarakat</title>
    
  
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/bootstrap-table/dist/bootstrap-table.min.css">
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
      background-color: #202f5b;
      color: white;
      padding-top: 20px;
    }
    .sidebar a {
      color: #ddd;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
      border-radius: 8px;
      margin: 4px 8px;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #1151d3;
      color: #fff;
    }
    .submenu a { padding-left: 40px; font-size: 14px; }
.main-content {
    margin-left: 250px;
    min-height: 100vh;
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
    transition: all 0.3s;
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
.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.content-wrapper {
    padding: 20px;
    background-color: #f8f9fa;
    min-height: calc(100vh - 70px);
}
.page-title {
    margin-bottom: 20px;
}
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 10px 15px;
    transition: all 0.3s;
}

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
</head>
<body>

<!-- Sidebar -->
<?php include "includes/sidebar.php"; ?>

<div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-primary text-white">
        <div class="container-fluid">
            <h5 class="mb-0">Pemberdayaan Masyarakat</h5>
            <span id="currentDateTime">
                <i class="bi bi-clock"></i> 
                <!-- Date & Time will be inserted here -->
            </span>
            <div class="account-dropdown">
                <button class="btn account-btn d-flex align-items-center text-white">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <h6 class="mb-0">Hallo, <?= $_SESSION['username']; ?> </h6>
                </button>
                <div class="dropdown-content">
                    <div class="d-flex align-items-center p-2">
                        <i class="bi bi-person-circle fs-3 text-primary me-2"></i>
                        <div>
                            <strong class="text-black"><?= $jabatan[0]; ?></strong>
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
                <i class="bi bi-clipboard-data text-primary me-2"></i>Input Realisasi Kinerja dan Anggaran
            </h2>
        </div>
        <!-- Tombol Input -->
                <?php if ($_SESSION['role'] === 'Pemberdayaan Masyarakat'): ?>
                    <div class="text-start mb-1">
                        <a class="btn btn-primary" href="editPM.php">
                            <i class="bi bi-pencil-square"></i> Input / Edit Realisasi
                        </a>
                    </div>
                <?php  else: ?>
                    <div class="text-start mb-1">
                        <p class=" text-muted"><i class="bi bi-info-circle-fill"></i> Hanya bisa di akses staff Pemberdayaan Masyarakat</p>
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
    <div class="card-header bg-primary text-white">
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

                <a href="pemberdayaanMasyarakat.php" class="btn btn-outline-success btn-filter">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Reset</span>
                </a>
            </div>


        </form>
    </div>

</div>



        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header bg-primary">
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
                        <?= number_format($data['pagu_anggaran'], 2, ',', '.'); ?>
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

                        <?php if ($tw[$i]['realisasi'] === null && $tw[$i]['realisasiT'] === null): ?>
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
                                        Rp <?= number_format($tw[$i]['realisasi'], 2, ',', '.'); ?>
                                    </strong>
                                </li>
                                <li>
                                    Persentase Anggaran:
                                    <strong><?= $tw[$i]['persentase']; ?>%</strong>
                                </li>
                                <li>
                                    Sisa Anggaran:
                                    <strong>
                                        Rp <?= number_format($tw[$i]['sisa'], 2, ',', '.'); ?>
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

            <div class="modal-header bg-primary text-white">
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

            <?php if ($_SESSION['role'] === 'Pemberdayaan Masyarakat'): ?>
                    <div class="modal-footer">
                        <a href="editPM.php?indikator_id=<?= $id; ?>&tahun=<?= $tahun; ?>" class="btn btn-primary px-4">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </div>
                <?php endif; ?>
            
        </div>
    </div>
</div>



<script src="assets/jquery-4.0.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/bootstrap-table/dist/bootstrap-table.min.js"></script>
<!-- Custom JavaScript -->
<script>
function updateDateTime() {
    const now = new Date();

    const dateOptions = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };

    const timeOptions = {
        hour: '2-digit',
        minute: '2-digit'
    };

    const dateString = now.toLocaleDateString('id-ID', dateOptions);
    const timeString = now.toLocaleTimeString('id-ID', timeOptions);

    document.getElementById('currentDateTime').innerHTML = 
        `<i class="bi bi-clock"></i> ${dateString} | ${timeString}`;
}

// Update tiap menit (sudah benar)
setInterval(updateDateTime, 60 * 1000);
updateDateTime();
 // Panggil sekali saat pertama kali load


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
                        <li>
  <strong>Sisa Target:</strong>
  ${Number(b.sisa_target).toFixed(2)} ${satuan}
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
