<?php
require 'config/database.php';
session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


$qIndikator = $conn->query("
    SELECT id, sub_kegiatan 
    FROM kegiatan 
    WHERE bidang = 'Perencanaan dan Keuangan'
    AND tahun >= YEAR(CURDATE()) - 4
    ORDER BY sub_kegiatan ASC
");

/* Ambil tahun (unik / tidak double) */
$qTahun = $conn->query("
    SELECT DISTINCT tahun   
    FROM kegiatan 
    WHERE bidang = 'Perencanaan dan Keuangan'
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


$realisasi = '';
$realisasi_anggaran = '';

if (!empty($_GET['indikator_id']) && !empty($_GET['tahun']) && !empty($_GET['bulan'])) {

    $indikator_id = intval($_GET['indikator_id']);
    $tahun        = intval($_GET['tahun']);
    $bulan_ke        = intval($_GET['bulan']);

    $qCek = $conn->prepare("
        SELECT realisasi_bulan$bulan_ke, realisasi_anggaran_bulan$bulan_ke
        FROM kegiatan
        WHERE id = ? AND tahun = ? 
        LIMIT 1
    ");
    $qCek->bind_param("ii", $indikator_id, $tahun);
    $qCek->execute();
    $res = $qCek->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $realisasi    = $row['realisasi_bulan'.$bulan_ke];
        $realisasi_anggaran = (int) $row['realisasi_anggaran_bulan'.$bulan_ke];
        
    }
}

// UPDATE HANDLER 
if (isset($_POST['submit_realisasi'])) {

    $id = intval($_POST['indikator_id']);
    $tahun        = intval($_POST['tahun']);
    $bulan_ke        = intval($_POST['bulan']);
    $fisik        = intval($_POST['realisasi_fisik']);
    $anggaran     = intval($_POST['realisasi_anggaran']);

        // UPDATE
        $update = $conn->prepare("
            UPDATE kegiatan
            SET realisasi_bulan$bulan_ke = ?, realisasi_anggaran_bulan$bulan_ke = ?
            WHERE id = ? AND tahun = ? 
        ");
        $update->bind_param(
            "iiii",
            $fisik,
            $anggaran,
            $id,
            $tahun
        );
        $update->execute();

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Data berhasil disimpan!'
    ];

    header("Location: perencanaan.php?indikator_id=$id&tahun=$tahun");
    exit;
}

//  NOTIF 


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
.main-content {
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
</head>
<body>

<div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
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
    <div class="content-wrapper container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-pencil-fill success me-3"></i>Edit Data per-Triwulan
            </h2>
        </div>
        
    <div class="text-start mb-1">
        <a class="btn btn-success" href="perencanaan.php">
            <i class="bi bi-arrow-bar-left"></i> Kembali
        </a>
    </div>

        <!-- Filter Form Card -->
<div class="card mb-4 shadow-sm">

    <!-- Header -->
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Pilih Data & Detail
        </h5>
        <small>Pilih indikator, tahun, dan triwulan</small>
    </div>

    <div class="card-body">

        <!-- FORM FILTER -->
        <form method="GET" action="" class="row g-3 align-items-end mb-4">

            <!-- Dropdown Indikator -->
            <div class="col-md-5">
                <label class="form-label fw-semibold">
                    Sub Kegiatan
                </label>
                <select name="indikator_id" class="form-select" required>
                    <option value="">-- Pilih Indikator --</option>
                    <?php while ($row = $qIndikator->fetch_assoc()) : ?>
                        <option value="<?= $row['id']; ?>"
                            <?= ($_GET['indikator_id'] ?? '') == $row['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row['sub_kegiatan']); ?>
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

            <!-- Dropdown Triwulan -->
            <div class="col-md-2">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select" required>
                    <option value="">-- Pilih Bulan --</option>
                    <option value="1" <?= ($_GET['bulan'] ?? '') == '1' ? 'selected' : ''; ?>>Januari</option>
                    <option value="2" <?= ($_GET['bulan'] ?? '') == '2' ? 'selected' : ''; ?>>Februari</option>
                    <option value="3" <?= ($_GET['bulan'] ?? '') == '3' ? 'selected' : ''; ?>>Maret</option>
                    <option value="4" <?= ($_GET['bulan'] ?? '') == '4' ? 'selected' : ''; ?>>April</option>
                    <option value="5" <?= ($_GET['bulan'] ?? '') == '5' ? 'selected' : ''; ?>>Mei</option>
                    <option value="6" <?= ($_GET['bulan'] ?? '') == '6' ? 'selected' : ''; ?>>Juni</option>
                    <option value="7" <?= ($_GET['bulan'] ?? '') == '7' ? 'selected' : ''; ?>>Juli</option>
                    <option value="8" <?= ($_GET['bulan'] ?? '') == '8' ? 'selected' : ''; ?>>Agustus</option>
                    <option value="9" <?= ($_GET['bulan'] ?? '') == '9' ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?= ($_GET['bulan'] ?? '') == '10' ? 'selected' : ''; ?>>OKtober</option>
                    <option value="11" <?= ($_GET['bulan'] ?? '') == '11' ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?= ($_GET['bulan'] ?? '') == '12' ? 'selected' : ''; ?>>Desember</option>
                </select>
            </div>

            <!-- Tombol -->
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-caret-down"></i> Tampilkan
                </button>
            </div>

        </form>

        <!-- GARIS PEMBATAS -->
        <hr>

        <!-- DETAIL DATA -->
        <?php if (!$data): ?>
            <div class="text-muted text-center py-4">
                Indikator tidak ada
            </div>
        <?php else: ?>

            <ul class="list-group">
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
                    <strong>Target:</strong><br>
                    <?= number_format($data['target'], 0, ',', '.') . " " . htmlspecialchars($data['satuan']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Sisa Target:</strong><br>
                    <?= number_format($tw[4]['sisa_target'], 0, ',', '.') . " " . htmlspecialchars($data['satuan']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Pagu Anggaran Tahunan:</strong><br>
                    Rp <?= number_format($data['pagu_anggaran'], 0, ',', '.'); ?>
                </li>
                <li class="list-group-item">
                    <strong>Sisa Pagu Anggaran:</strong><br>
                    Rp <?= number_format($tw[4]['sisa'], 0, ',', '.'); ?>
                </li>
            </ul>

            <hr>

            <h6 class="mb-3">
                <i class="bi bi-pencil-square me-1"></i>Input Realisasi
            </h6>

            <form method="POST" class="row g-3">

                <input type="hidden" name="indikator_id" value="<?= $_GET['indikator_id']; ?>">
                <input type="hidden" name="tahun" value="<?= $_GET['tahun']; ?>">
                <input type="hidden" name="bulan" value="<?= $_GET['bulan']; ?>"> 

                <?php if (isset($_GET['bulan'])):  ?>
                    <div class="col-md-5">
                        <label class="form-label">Realisasi Target</label>
                        <input type="number"
                            name="realisasi_fisik"
                            class="form-control"
                            value="<?= number_format($realisasi, 0, '.', ','); ?>"
                            required>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Realisasi Anggaran</label>
                        <input type="number"
                            name="realisasi_anggaran"
                            class="form-control"
                            value="<?= $realisasi_anggaran; ?>"
                            required>
                    </div>

                    <div class="col-md-2 d-grid align-items-end">
                        <button type="submit" name="submit_realisasi" class="btn btn-success">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                    </div>
                

                <?php else : ?>
                    <div class="text-muted text-center py-4">
                        Pilih bulan yang mau diisi terlebih dahulu
                    </div>

                <?php  endif;  ?>


                

            </form>
        <?php endif; ?>

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





</script>

</body>
</html>
