<?php
require 'config/database.php';
session_start();

/* ==========================
   INSERT / UPDATE REALISASI
   ========================== */
if (isset($_POST['update'])) {

    $rt_id              = $_POST['rt_id'] ?: null;
    $indikator_id       = (int)$_POST['indikator_id'];
    $triwulan           = (int)$_POST['triwulan'];
    $realisasi          = $_POST['realisasi'] ?: null;
    $pagu_anggaran      = $_POST['pagu_anggaran'] ?: null;
    $realisasi_anggaran = $_POST['realisasi_anggaran'] ?: null;

    if ($rt_id) {
        // UPDATE (PASTI BARIS YANG BENAR)
        $stmt = $conn->prepare("
            UPDATE realisasi_triwulan
            SET triwulan = ?,
                realisasi = ?,
                pagu_anggaran = ?,
                realisasi_anggaran = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "idddi",
            $triwulan,
            $realisasi,
            $pagu_anggaran,
            $realisasi_anggaran,
            $rt_id
        );
    } else {
        // INSERT BARU
        $stmt = $conn->prepare("
            INSERT INTO realisasi_triwulan
            (indikator_id, triwulan, realisasi, pagu_anggaran, realisasi_anggaran)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiddd",
            $indikator_id,
            $triwulan,
            $realisasi,
            $pagu_anggaran,
            $realisasi_anggaran
        );
    }

    $stmt->execute();
    header("Location: perencanaan.php");
    exit;
}

/* ==========================
   PROSES HAPUS (TANPA FILE BARU)
   ========================== */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM realisasi_triwulan WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: perencanaan.php");
    exit;
}

/* ==========================
   AMBIL DATA
   ========================== */
$sql = "
    SELECT
        i.id AS indikator_id,
        i.sasaran_strategis,
        i.indikator_kinerja AS indikator,
        i.satuan,
        i.target_tahunan AS target,
        i.tahun,

        rt.id AS rt_id,
        rt.triwulan,
        rt.realisasi,
        rt.pagu_anggaran,
        rt.realisasi_anggaran,

        ROUND(
            CASE 
                WHEN i.target_tahunan > 0 AND rt.realisasi IS NOT NULL
                THEN (rt.realisasi / i.target_tahunan) * 100
                ELSE 0
            END, 2
        ) AS persentase,

        ROUND(
            CASE 
                WHEN rt.pagu_anggaran > 0
                THEN (rt.realisasi_anggaran / rt.pagu_anggaran) * 100
                ELSE 0
            END, 2
        ) AS persentase_anggaran

    FROM indikator i
    LEFT JOIN realisasi_triwulan rt 
        ON rt.indikator_id = i.id
    WHERE i.bidang = 'Perencanaan dan Keuangan'
    ORDER BY i.id DESC, rt.triwulan ASC
";
$query = mysqli_query($conn, $sql);
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Perencanaan & Keuangan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css">

<style>
 body {
      background-color: #f8f9fa;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      background-color: #2c2f33;
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
      background-color: #343a40;
      color: #fff;
    }
    .submenu a {
      padding-left: 40px;
      font-size: 13px;
    }
    .main-content {
      margin-left: 250px;
    }
    .navbar {
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
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
      z-index: 10;
    }
    .account-dropdown:hover .dropdown-content {
      display: block;
    }
    .account-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
    }
    /* Pastikan cell aksi selalu center */
.table td {
    vertical-align: middle !important;
}

/* Wrapper tombol aksi */
.aksi-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    min-height: 48px;
}
</style>
</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">
<nav class="navbar navbar-expand-lg navbar-light">
<div class="container-fluid">
<h5 class="mb-0">Perencanaan dan Keuangan</h5>
<span id="currentDateTime"><i class="bi bi-clock"></i> --</span>
<div class="account-dropdown">
<button class="btn account-btn d-flex align-items-center">
<i class="bi bi-person-circle fs-4 me-2"></i>
<h6 class="mb-0">Hello, Administrator</h6>
</button>
</div>
</div>
</nav>

<div class="container mt-4">


<div id="toolbar" class="mb-3 d-flex gap-2">
<select id="filterIndikator" class="form-select form-select-sm w-auto">
<option value="">Pilih Indikator</option>
<?php foreach (array_unique(array_column($data,'indikator')) as $i): ?>
<option value="<?= htmlspecialchars($i) ?>"><?= htmlspecialchars($i) ?></option>
<?php endforeach ?>
</select>

<select id="filterTahun" class="form-select form-select-sm w-auto">
<option value="">Tahun</option>
<?php foreach (array_unique(array_column($data,'tahun')) as $t): ?>
<option value="<?= $t ?>"><?= $t ?></option>
<?php endforeach ?>
</select>

<select id="filterTW" class="form-select form-select-sm w-auto">
<option value="">Semua TW</option>
<option value="1">TW I</option>
<option value="2">TW II</option>
<option value="3">TW III</option>
<option value="4">TW IV</option>
</select>
</div>

<table id="table"
class="table table-dark table-hover small"
data-toggle="table"
data-search="true"
data-pagination="true"
data-toolbar="#toolbar">

<thead>
<tr>
<th data-formatter="noFormatter">#</th>
<th data-field="sasaran_strategis">Sasaran</th>
<th data-field="indikator">Indikator</th>
<th data-field="satuan">Satuan</th>
<th data-field="target">Target</th>
<th data-field="realisasi">Realisasi</th>
<th data-field="persentase">%</th>
<th data-field="pagu_anggaran">Pagu</th>
<th data-field="realisasi_anggaran">Realisasi Anggaran</th>
<th data-field="persentase_anggaran">% Anggaran</th>
<th data-field="triwulan">TW</th>
<th data-field="tahun">Tahun</th>
<th data-formatter="aksiFormatter">Aksi</th>
</tr>
</thead>
</table>
</div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post">
<div class="modal-header">
<h5>Edit Realisasi Triwulan</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="rt_id" id="rt_id">
<input type="hidden" name="indikator_id" id="indikator_id">

<div class="mb-2">
<label>Realisasi</label>
<textarea name="realisasi" id="realisasi" class="form-control"></textarea>
</div>

<div class="row">
<div class="col-md-4">
<label>Triwulan</label>
<select name="triwulan" id="triwulan" class="form-select">
<option value="1">TW I</option>
<option value="2">TW II</option>
<option value="3">TW III</option>
<option value="4">TW IV</option>
</select>
</div>

<div class="col-md-4">
<label>Pagu Anggaran</label>
<input type="number" name="pagu_anggaran" id="pagu_anggaran" class="form-control">
</div>

<div class="col-md-4">
<label>Realisasi Anggaran</label>
<input type="number" name="realisasi_anggaran" id="realisasi_anggaran" class="form-control">
</div>
</div>

<div class="mt-2">
<label>Tahun</label>
<input type="number" id="tahun" class="form-control" readonly>
</div>
</div>

<div class="modal-footer">
<button type="submit" name="update" class="btn btn-primary">Simpan</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.js"></script>

<script>
const data = <?= json_encode($data); ?>;
const $table = $('#table');
$table.bootstrapTable({ data: [] });

$('#filterIndikator').on('change', function(){
    let v = $(this).val();
    if(!v) return $table.bootstrapTable('load', []);
    $table.bootstrapTable('load', data.filter(r => r.indikator === v));
});

$('#filterTahun, #filterTW').on('change', function(){
    let f = {};
    if($('#filterTahun').val()) f.tahun = $('#filterTahun').val();
    if($('#filterTW').val()) f.triwulan = $('#filterTW').val();
    $table.bootstrapTable('filterBy', f);
});

function noFormatter(v,r,i){ return i+1; }

function aksiFormatter(v, row) {
    return `
        <div class="aksi-wrapper">
            <button class="btn btn-warning btn-sm edit"
                data-rt='${JSON.stringify(row)}'
                data-bs-toggle="modal"
                data-bs-target="#modalEdit">
                <i class="bi bi-pencil"></i>
            </button>

            <a href="?hapus=${row.rt_id}"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Yakin hapus?')">
                <i class="bi bi-trash"></i>
            </a>
        </div>
    `;
}

$(document).on('click','.edit',function(){
    const r = $(this).data('rt');
    $('#rt_id').val(r.rt_id ?? '');
    $('#indikator_id').val(r.indikator_id);
    $('#realisasi').val(r.realisasi ?? '');
    $('#triwulan').val(r.triwulan ?? 1);
    $('#pagu_anggaran').val(r.pagu_anggaran ?? '');
    $('#realisasi_anggaran').val(r.realisasi_anggaran ?? '');
    $('#tahun').val(r.tahun);
});

function updateDateTime(){
const now=new Date();
document.getElementById('currentDateTime').innerHTML=
`<i class="bi bi-clock"></i> ${now.toLocaleString('id-ID')}`;
}
updateDateTime();
setInterval(updateDateTime,1000);
</script>

</body>
</html>
