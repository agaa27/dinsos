<?php
require 'config/database.php';
session_start();

$sql = "
    SELECT
    i.sasaran_strategis,
    i.indikator_kinerja AS indikator,
    i.satuan,
    i.target_tahunan AS target,
    i.tahun,

    rt.triwulan,
    rt.realisasi,

    ROUND(
        CASE 
            WHEN i.target_tahunan > 0 
            THEN (rt.realisasi / i.target_tahunan) * 100
            ELSE 0
        END
    , 2) AS persentase,

    rt.pagu_anggaran,
    rt.realisasi_anggaran,

    ROUND(
        CASE 
            WHEN rt.pagu_anggaran > 0
            THEN (rt.realisasi_anggaran / rt.pagu_anggaran) * 100
            ELSE 0
        END
    , 2) AS persentase_anggaran

    FROM indikator i
    LEFT JOIN realisasi_triwulan rt 
        ON rt.indikator_id = i.id
    WHERE i.bidang = 'Perencanaan dan Keuangan'
    ORDER BY i.created_at DESC;
";

$query = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DINSOS-PM | Keuangan dan Perencanaan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css" rel="stylesheet">

<!-- ⚠️ CSS ASLI TIDAK DIUBAH -->
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
  </style>
</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<!-- NAVBAR TIDAK DIUBAH -->
<nav class="navbar navbar-expand-lg navbar-light">
<div class="container-fluid">
<h5 class="mb-0">Keuangan dan Perencanaan</h5>
<span id="currentDateTime"><i class="bi bi-clock"></i> --</span>
<div class="account-dropdown">
<button class="btn account-btn d-flex align-items-center">
<i class="bi bi-person-circle fs-4 me-2"></i>
<h6 class="mb-0">Hello, Administrator</h6>
</button>
</div>
</div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-4">

<table class="table table-dark table-hover table-responsive small"
data-toggle="table"
data-search="true"
data-pagination="true"
style="background-color:#343a40;">

<thead>
<tr>
<th>#</th>
<th>Sasaran Strategis</th>
<th>Indikator</th>
<th>Satuan</th>
<th>Target</th>
<th>Realisasi</th>
<th>%</th>
<th>Pagu Anggaran</th>
<th>Realisasi Anggaran</th>
<th>% Anggaran</th>
<th>TW</th>
<th>Tahun</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; foreach($data as $n): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $n['sasaran_strategis'] ?></td>
<td><?= $n['indikator'] ?></td>
<td><?= $n['satuan'] ?></td>
<td><?= $n['target'] ?></td>
<td><?= $n['realisasi'] ?></td>
<td><?= $n['persentase'] ?>%</td>
<td><?= $n['pagu_anggaran'] ?></td>
<td><?= $n['realisasi_anggaran'] ?></td>
<td><?= $n['persentase_anggaran'] ?>%</td>
<td><?= $n['triwulan'] ?></td>
<td><?= $n['tahun'] ?></td>

<td class="text-center">
<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#edit<?= md5($n['indikator']) ?>">
<i class="bi bi-pencil"></i>
</button>

<a href="perencanaan.php?indikator=<?= urlencode($n['indikator']) ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin hapus data ini?')">
<i class="bi bi-trash"></i>
</a>
</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= md5($n['indikator']) ?>" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<form action="perencanaan.php" method="POST">

<div class="modal-header">
<h5>Edit</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="indikator_lama" value="<?= $n['indikator'] ?>">

<div class="mb-2">
<label>Realisasi</label>
<textarea name="sasaran_strategis" class="form-control"><?= $n['realisasi'] ?></textarea>
</div>

<div class="mb-2">
<label>Pagu</label>
<textarea name="indikator_kinerja" class="form-control"><?= $n['pagu_anggaran'] ?></textarea>
</div>

<div class="row">
<div class="col-md-4">
<label>Triwulan</label>
<input type="text" name="satuan" class="form-control" value="<?= $n['triwulan'] ?>">
</div>
<div class="col-md-4">
<label>Realisasi anggaran</label>
<input type="number" name="target" class="form-control" value="<?= $n['realisasi_anggaran'] ?>">
</div>
<div class="col-md-4">
<label>Pagu anggaran</label>
<input type="number" name="pagu" class="form-control" value="<?= $n['pagu_anggaran'] ?>">
</div>
<div class="col-md-4">
<label>Tahun</label>
<input type="number" name="tahun" class="form-control" value="<?= $n['tahun'] ?>">
</div>
</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button class="btn btn-primary">Update</button>
</div>

</form>
</div>
</div>
</div>

<?php endforeach; ?>
</tbody>
</table>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
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
