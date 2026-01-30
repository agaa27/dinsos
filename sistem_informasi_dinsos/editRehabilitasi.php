<?php
require 'config/database.php';
require 'fungsi.php';
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}


if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $jabatan = explode(" ", $username);  
}

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


$qIndikator = $conn->query("
    SELECT id, sub_kegiatan 
    FROM kegiatan 
    WHERE bidang = 'Rehabilitasi Sosial'
    AND tahun >= YEAR(CURDATE()) - 4
    ORDER BY sub_kegiatan ASC
");

/* Ambil tahun (unik / tidak double) */
$qTahun = $conn->query("
    SELECT DISTINCT tahun   
    FROM kegiatan 
    WHERE bidang = 'Rehabilitasi Sosial'
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

//TAMPILKAN DATA PER Bulan
if ($data) {

    $pagu_tahunan = (float) $data['pagu_anggaran'];
    $target       = (float) $data['target'];
    $satuan       = $data['satuan'];

    $tw = [];
    $total_realisasi_anggaran = 0;
    $total_realisasi_target   = 0;

    // Mapping bulan per Bulan
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
$bulan_ke = $_GET['bulan'];
$bukti = "bukti$bulan_ke";

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
    $fisik        = floatval($_POST['realisasi_fisik']);
    $anggaran     = floatval($_POST['realisasi_anggaran']);
    $bukti     = upload_file();

    if ($bukti === false) {
        exit; // upload error
    }

    if ($bukti === null) {
        // tidak upload → jangan update kolom bukti
        $sql = "
            UPDATE kegiatan
            SET realisasi_bulan$bulan_ke = ?,
                realisasi_anggaran_bulan$bulan_ke = ?
            WHERE id = ? AND tahun = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddii", $fisik, $anggaran, $id, $tahun);
    } else {
        // upload ada → update bukti
        $sql = "
            UPDATE kegiatan
            SET realisasi_bulan$bulan_ke = ?,
                realisasi_anggaran_bulan$bulan_ke = ?,
                bukti$bulan_ke = ?
            WHERE id = ? AND tahun = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddsis", $fisik, $anggaran, $bukti, $id, $tahun);
    }

    $stmt->execute();


    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Data berhasil disimpan!'
    ];

    header("Location: rehabilitasi.php?indikator_id=$id&tahun=$tahun");
    exit;
}

//  NOTIF 


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Realisasi Bulan - Dinsos Tarakan</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
body {
    background-color: #f8f9fa;
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
.btn-primary {
    background-color: #3498db;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    transition: all 0.3s;
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

</style>
</head>
<body>

<div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-primary text-white">
        <div class="container-fluid">
            <h5 class="mb-0">Rehabilitasi Sosial</h5>
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
    <div class="content-wrapper container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-pencil-fill success me-3"></i>Update Data per-Bulan
            </h2>
        </div>
        
    <div class="text-start mb-2">
        <a class="btn btn-primary" href="rehabilitasi.php">
            <i class="bi bi-arrow-bar-left"></i> Kembali
        </a>
    </div>

        <!-- Filter Form Card -->
<div class="card mb-4 shadow-sm">

    <!-- Header -->
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Pilih Data & Detail
        </h5>
        <small>Pilih indikator, tahun, dan Bulan</small>
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
                    <option value="">-- Pilih Sub Kegiatan --</option>
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

            <!-- Dropdown Bulan -->
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
                    <?= number_format($data['target'], 2, ',', '.') . " " . htmlspecialchars($data['satuan']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Sisa Target:</strong><br>
                    <?= number_format($tw[4]['sisa_target'], 2, ',', '.') . " " . htmlspecialchars($data['satuan']); ?>
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

            <form method="POST"
                class="row g-4 align-items-end"
                enctype="multipart/form-data">


                <input type="hidden" name="indikator_id" value="<?= $_GET['indikator_id']; ?>">
                <input type="hidden" name="tahun" value="<?= $_GET['tahun']; ?>">
                <input type="hidden" name="bulan" value="<?= $_GET['bulan']; ?>"> 

                <?php if (isset($_GET['bulan'])): ?>

                    <div class="col-md-3 mb-auto">
                        <label class="form-label fw-semibold">Realisasi Target</label>
                        <input type="number"
                            name="realisasi_fisik"
                            class="form-control"
                            step="0.01"
                            value="<?= number_format($realisasi, 2, '.', ''); ?>"
                            required>
                    </div>

                    <div class="col-md-3 mb-auto">
                        <label class="form-label fw-semibold">Realisasi Anggaran</label>
                        <input type="number"
                            name="realisasi_anggaran"
                            class="form-control"
                            value="<?= $realisasi_anggaran; ?>"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bukti Dukung/Evidence (PDF)</label>
                        <input type="file"
                            name="gambar"
                            class="form-control"
                            accept="application/pdf">

                        <?php
                            $bulan = (int) $_GET['bulan'];
                            $namaBukti = $data["bukti{$bulan}"] ?? null;
                        ?>

                        <?php if ($namaBukti): ?>
                            <small class="text-muted d-block mt-1">
                                Bukti saat ini:
                                <a href="file bukti/<?= htmlspecialchars($namaBukti); ?>" target="_blank">
                                    <?= htmlspecialchars($namaBukti); ?>
                                </a>
                            </small>
                        <?php else: ?>
                            <small class="text-muted d-block" style="font-size:12px;">
                                Maksimal ukuran file: 40 MB
                            </small>
                        <?php endif; ?>                        
                    </div>

                    <div class="col-md-2 d-grid my-auto">
                        <label class="form-label fw-semibold text-white">.</label>
                        <button type="submit" name="submit_realisasi" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                    </div>

                <?php else: ?>

                    <div class="col-12 text-center text-muted py-4">
                        Pilih bulan yang mau diisi terlebih dahulu
                    </div>

                <?php endif; ?>

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





</script>

</body>
</html>