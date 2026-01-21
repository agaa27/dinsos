<?php
include 'config/database.php';

$judul_bidang     = '';
$judul_realisasi  = '';

$bidang        = $_GET['bidang'] ?? 'Semua';
$tahun         = $_GET['tahun'] ?? date('Y');
$realisasi_min = $_GET['realisasi_min'] ?? 'semua';

/* Normalisasi nilai */
$bidang_lower       = strtolower($bidang);
$realisasi_lower    = strtolower($realisasi_min);

/* Judul tambahan */
if ($bidang_lower !== 'semua') {
    $bidang_safe  = preg_replace('/[^a-zA-Z0-9_-]/', '', $bidang);
    $judul_bidang = "_bidang_" . $bidang_safe;
}

if ($realisasi_lower !== 'semua') {
    $realisasi_safe   = preg_replace('/[^a-zA-Z0-9_-]/', '', $realisasi_min);
    $judul_realisasi  = "_realisasi_" . $realisasi_safe;
}

/* Header Excel */
header("Content-Type: application/vnd.ms-excel");
header(
    "Content-Disposition: attachment; filename=laporan_kegiatan_{$tahun}{$judul_bidang}{$judul_realisasi}.xls"
);

$where = "WHERE tahun = '$tahun'";
if ($bidang !== 'Semua') {
  $where .= " AND bidang = '$bidang'";
}

$sql = "
SELECT *,
(
  IFNULL(realisasi_bulan1,0)+IFNULL(realisasi_bulan2,0)+IFNULL(realisasi_bulan3,0)+
  IFNULL(realisasi_bulan4,0)+IFNULL(realisasi_bulan5,0)+IFNULL(realisasi_bulan6,0)+
  IFNULL(realisasi_bulan7,0)+IFNULL(realisasi_bulan8,0)+IFNULL(realisasi_bulan9,0)+
  IFNULL(realisasi_bulan10,0)+IFNULL(realisasi_bulan11,0)+IFNULL(realisasi_bulan12,0)
) AS total_realisasi,

(
  IFNULL(realisasi_anggaran_bulan1,0)+IFNULL(realisasi_anggaran_bulan2,0)+
  IFNULL(realisasi_anggaran_bulan3,0)+IFNULL(realisasi_anggaran_bulan4,0)+
  IFNULL(realisasi_anggaran_bulan5,0)+IFNULL(realisasi_anggaran_bulan6,0)+
  IFNULL(realisasi_anggaran_bulan7,0)+IFNULL(realisasi_anggaran_bulan8,0)+
  IFNULL(realisasi_anggaran_bulan9,0)+IFNULL(realisasi_anggaran_bulan10,0)+
  IFNULL(realisasi_anggaran_bulan11,0)+IFNULL(realisasi_anggaran_bulan12,0)
) AS total_realisasi_anggaran
FROM kegiatan
$where
";

$result = mysqli_query($conn, $sql);



echo "<table border='1'>";

/* ================= HEADER ================= */

echo "<tr>";
echo "<th rowspan='3'>NO</th>";
if ($bidang === 'Semua') echo "<th rowspan='3'>Bidang</th>";
echo "
<th rowspan='3'>Sasaran</th>
<th rowspan='3'>Indikator</th>
<th rowspan='3'>Program</th>
<th rowspan='3'>Kegiatan</th>
<th rowspan='3'>Sub Kegiatan</th>

<th colspan='6'>Triwulan 1</th>
<th colspan='6'>Triwulan 2</th>
<th colspan='6'>Triwulan 3</th>
<th colspan='6'>Triwulan 4</th>

<th rowspan='3'>Target Tahunan</th>
<th rowspan='3'>Realisasi</th>
<th rowspan='3'>Sisa</th>
<th rowspan='3'>%</th>
<th rowspan='3'>Pagu Tahunan</th>
<th rowspan='3'>Realisasi Pagu</th>
<th rowspan='3'>Sisa Pagu</th>
<th rowspan='3'>% Pagu</th>
</tr>";

echo "<tr>";
foreach (['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'] as $b) {
  echo "<th colspan='2'>$b</th>";
}
echo "</tr>";

echo "<tr>";
for ($i=1; $i<=12; $i++) {
  echo "<th>K</th><th>A</th>";
}
echo "</tr>";
$n = 1;
while ($r = mysqli_fetch_assoc($result)) {

  $persen = $r['target'] > 0 ? ($r['total_realisasi'] / $r['target']) * 100 : 0;
  if ($realisasi_min !== '' && $persen < $realisasi_min) continue;

  $sisa = $r['target'] - $r['total_realisasi'];
  $sisa_pagu = $r['pagu_anggaran'] - $r['total_realisasi_anggaran'];
  $persen_pagu = $r['pagu_anggaran'] > 0
    ? ($r['total_realisasi_anggaran'] / $r['pagu_anggaran']) * 100 : 0;

  echo "<tr>";
  echo "<td>{$n}</td>";

  if ($bidang === 'Semua') echo "<td>{$r['bidang']}</td>";

  echo "
  <td>{$r['sasaran_strategis']}</td>
  <td>{$r['indikator_kinerja']}</td>
  <td>{$r['program']}</td>
  <td>{$r['kegiatan']}</td>
  <td>{$r['sub_kegiatan']}</td>
  ";

  for ($i=1; $i<=12; $i++) {
    echo "<td>{$r["realisasi_bulan$i"]}</td>";
    echo "<td>{$r["realisasi_anggaran_bulan$i"]}</td>";
  }

  echo "
  <td>{$r['target']}</td>
  <td>{$r['total_realisasi']}</td>
  <td>$sisa</td>
  <td>".round($persen,2)."</td>
  <td>{$r['pagu_anggaran']}</td>
  <td>{$r['total_realisasi_anggaran']}</td>
  <td>$sisa_pagu</td>
  <td>".round($persen_pagu,2)."</td>
  </tr>";
  $n++;
}

echo "</table>";
exit;
