<?php
require 'config/database.php';
session_start();


if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $jabatan = explode("_", $username);  
}


// TOTAL KEGIATAN 
$query_data_total = "SELECT COUNT(*) AS total_kegiatan FROM kegiatan WHERE tahun = YEAR(CURDATE());";
$result_data_total = mysqli_query($conn, $query_data_total);
$row_data_total = mysqli_fetch_assoc($result_data_total);
$total_data = $row_data_total['total_kegiatan'];

// TOTAL KEGIATAN YANG LEBIH 30%
$query_data_up_30 = "SELECT 
    *,
    COUNT(*) OVER () AS jumlah
FROM (
    SELECT 
        sub_kegiatan,
        bidang,
        pagu_anggaran,
        (
            IFNULL(realisasi_anggaran_bulan1,0) +
            IFNULL(realisasi_anggaran_bulan2,0) +
            IFNULL(realisasi_anggaran_bulan3,0) +
            IFNULL(realisasi_anggaran_bulan4,0) +
            IFNULL(realisasi_anggaran_bulan5,0) +
            IFNULL(realisasi_anggaran_bulan6,0) +
            IFNULL(realisasi_anggaran_bulan7,0) +
            IFNULL(realisasi_anggaran_bulan8,0) +
            IFNULL(realisasi_anggaran_bulan9,0) +
            IFNULL(realisasi_anggaran_bulan10,0) +
            IFNULL(realisasi_anggaran_bulan11,0) +
            IFNULL(realisasi_anggaran_bulan12,0)
        ) AS total_realisasi,
        ROUND(
            (
                (
                    IFNULL(realisasi_anggaran_bulan1,0) +
                    IFNULL(realisasi_anggaran_bulan2,0) +
                    IFNULL(realisasi_anggaran_bulan3,0) +
                    IFNULL(realisasi_anggaran_bulan4,0) +
                    IFNULL(realisasi_anggaran_bulan5,0) +
                    IFNULL(realisasi_anggaran_bulan6,0) +
                    IFNULL(realisasi_anggaran_bulan7,0) +
                    IFNULL(realisasi_anggaran_bulan8,0) +
                    IFNULL(realisasi_anggaran_bulan9,0) +
                    IFNULL(realisasi_anggaran_bulan10,0) +
                    IFNULL(realisasi_anggaran_bulan11,0) +
                    IFNULL(realisasi_anggaran_bulan12,0)
                ) / pagu_anggaran
            ) * 100, 2
        ) AS persentase_realisasi
    FROM kegiatan
    WHERE tahun = YEAR(CURDATE())
) x
WHERE persentase_realisasi >= 30;

";
$result_data_up_30 = mysqli_query($conn, $query_data_up_30);
$row_data_up_30 = mysqli_fetch_assoc($result_data_up_30);
// PERSENTASE KEGIATAN YANG TEREALISASI DIATAS 30%
$persentase_data_up_30 = round(($row_data_up_30['jumlah'] / $total_data) * 100, 2);

// TOTAL KEGIATAN YANG KURANG 30%
$sql = "
SELECT 
    sub_kegiatan,
    bidang,
    pagu_anggaran,
    (
        IFNULL(realisasi_anggaran_bulan1,0) +
        IFNULL(realisasi_anggaran_bulan2,0) +
        IFNULL(realisasi_anggaran_bulan3,0) +
        IFNULL(realisasi_anggaran_bulan4,0) +
        IFNULL(realisasi_anggaran_bulan5,0) +
        IFNULL(realisasi_anggaran_bulan6,0) +
        IFNULL(realisasi_anggaran_bulan7,0) +
        IFNULL(realisasi_anggaran_bulan8,0) +
        IFNULL(realisasi_anggaran_bulan9,0) +
        IFNULL(realisasi_anggaran_bulan10,0) +
        IFNULL(realisasi_anggaran_bulan11,0) +
        IFNULL(realisasi_anggaran_bulan12,0)
    ) AS total_realisasi,
    ROUND(
        (
            (
                IFNULL(realisasi_anggaran_bulan1,0) +
                IFNULL(realisasi_anggaran_bulan2,0) +
                IFNULL(realisasi_anggaran_bulan3,0) +
                IFNULL(realisasi_anggaran_bulan4,0) +
                IFNULL(realisasi_anggaran_bulan5,0) +
                IFNULL(realisasi_anggaran_bulan6,0) +
                IFNULL(realisasi_anggaran_bulan7,0) +
                IFNULL(realisasi_anggaran_bulan8,0) +
                IFNULL(realisasi_anggaran_bulan9,0) +
                IFNULL(realisasi_anggaran_bulan10,0) +
                IFNULL(realisasi_anggaran_bulan11,0) +
                IFNULL(realisasi_anggaran_bulan12,0)
            ) / pagu_anggaran
        ) * 100, 2
    ) AS persentase_realisasi
FROM kegiatan
WHERE tahun = YEAR(CURDATE())
HAVING persentase_realisasi < 30
";
$result = mysqli_query($conn, $sql);


// TOTAL PAGU ANGGARAN 
$query_pagu_anggaran = "SELECT SUM(pagu_anggaran) AS total_pagu_anggaran FROM kegiatan WHERE tahun = YEAR(CURDATE());";
$result_pagu_anggaran = mysqli_query($conn, $query_pagu_anggaran);
$row_pagu_anggaran = mysqli_fetch_assoc($result_pagu_anggaran);
$total_pagu_anggaran = $row_pagu_anggaran['total_pagu_anggaran'];

// TOTAL ANGGARAN YANG SUDAH DI PAKE
$query_anggaran_used = "SELECT 
    SUM(
        IFNULL(realisasi_anggaran_bulan1,0) +
        IFNULL(realisasi_anggaran_bulan2,0) +
        IFNULL(realisasi_anggaran_bulan3,0) +
        IFNULL(realisasi_anggaran_bulan4,0) +
        IFNULL(realisasi_anggaran_bulan5,0) +
        IFNULL(realisasi_anggaran_bulan6,0) +
        IFNULL(realisasi_anggaran_bulan7,0) +
        IFNULL(realisasi_anggaran_bulan8,0) +
        IFNULL(realisasi_anggaran_bulan9,0) +
        IFNULL(realisasi_anggaran_bulan10,0) +
        IFNULL(realisasi_anggaran_bulan11,0) +
        IFNULL(realisasi_anggaran_bulan12,0)
    ) AS total_realisasi,
    ROUND(
        (
            SUM(
                IFNULL(realisasi_anggaran_bulan1,0) +
                IFNULL(realisasi_anggaran_bulan2,0) +
                IFNULL(realisasi_anggaran_bulan3,0) +
                IFNULL(realisasi_anggaran_bulan4,0) +
                IFNULL(realisasi_anggaran_bulan5,0) +
                IFNULL(realisasi_anggaran_bulan6,0) +
                IFNULL(realisasi_anggaran_bulan7,0) +
                IFNULL(realisasi_anggaran_bulan8,0) +
                IFNULL(realisasi_anggaran_bulan9,0) +
                IFNULL(realisasi_anggaran_bulan10,0) +
                IFNULL(realisasi_anggaran_bulan11,0) +
                IFNULL(realisasi_anggaran_bulan12,0)
            ) / SUM(pagu_anggaran)
        ) * 100, 2
    ) AS persentase_realisasi
FROM kegiatan
WHERE tahun = YEAR(CURDATE());
";
$result_anggaran_used = mysqli_query($conn, $query_anggaran_used);
$row_anggaran_used = mysqli_fetch_assoc($result_anggaran_used);

// SISA ANGGARAN
$query_anggaran_left = "SELECT 
    SUM(pagu_anggaran) -
    SUM(
        IFNULL(realisasi_anggaran_bulan1,0) +
        IFNULL(realisasi_anggaran_bulan2,0) +
        IFNULL(realisasi_anggaran_bulan3,0) +
        IFNULL(realisasi_anggaran_bulan4,0) +
        IFNULL(realisasi_anggaran_bulan5,0) +
        IFNULL(realisasi_anggaran_bulan6,0) +
        IFNULL(realisasi_anggaran_bulan7,0) +
        IFNULL(realisasi_anggaran_bulan8,0) +
        IFNULL(realisasi_anggaran_bulan9,0) +
        IFNULL(realisasi_anggaran_bulan10,0) +
        IFNULL(realisasi_anggaran_bulan11,0) +
        IFNULL(realisasi_anggaran_bulan12,0)
    ) AS sisa_anggaran,
    ROUND(
        (
            (
                SUM(pagu_anggaran) -
                SUM(
                    IFNULL(realisasi_anggaran_bulan1,0) +
                    IFNULL(realisasi_anggaran_bulan2,0) +
                    IFNULL(realisasi_anggaran_bulan3,0) +
                    IFNULL(realisasi_anggaran_bulan4,0) +
                    IFNULL(realisasi_anggaran_bulan5,0) +
                    IFNULL(realisasi_anggaran_bulan6,0) +
                    IFNULL(realisasi_anggaran_bulan7,0) +
                    IFNULL(realisasi_anggaran_bulan8,0) +
                    IFNULL(realisasi_anggaran_bulan9,0) +
                    IFNULL(realisasi_anggaran_bulan10,0) +
                    IFNULL(realisasi_anggaran_bulan11,0) +
                    IFNULL(realisasi_anggaran_bulan12,0)
                )
            ) / SUM(pagu_anggaran)
        ) * 100, 2
    ) AS persentase_sisa
FROM kegiatan
WHERE tahun = YEAR(CURDATE());

";
$result_anggaran_left = mysqli_query($conn, $query_anggaran_left);
$row_anggaran_left = mysqli_fetch_assoc($result_anggaran_left);



?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DINSOS-PM | Rekapitulasi</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .main-content { margin-left: 250px; }
    .navbar { background-color: #fff; border-bottom: 1px solid #dee2e6; }
    .card-summary h4 { font-weight: bold; }
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

    .account-dropdown .dropdown-content p {
      margin: 8px 0;
      padding: 5px 10px;
    }

    .account-dropdown .dropdown-content a {
      color: black;
      text-decoration: none;
    }

    .account-dropdown .dropdown-content a:hover {
      color: #007bff;
    }

    /* Saat ikon 👤 di-hover, tampilkan dropdown */
    .account-dropdown:hover .dropdown-content {
      display: block;
    }

    /* Styling tambahan opsional */
    .account-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
    }

    .account-btn:hover {
      cursor: pointer;
    }     

  </style>
</head>

<body>

<!-- Sidebar -->
<?php include "includes/sidebar.php"; ?>

<div class="main-content">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light d-flex mt-0">
    <div class="container-fluid">
      <h5 class="mb-0">Rekapitulasi</h5>
      <span class="date">
        <i class="bi bi-clock"></i> Mon, 01 Jan 2025, 08.30 AM
      </span>

      <div class="d-flex align-items-center">
        <i class="bi bi-bell me-3 fs-5"></i>

        <!-- Account Dropdown -->
        
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
    </div>
  </nav>

  <!-- FILTER -->
  <div class="container mt-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <form class="row g-3">

          <div class="col-md-3">
            <label class="form-label">Periode Awal</label>
            <input type="date" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">Periode Akhir</label>
            <input type="date" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">Program Bantuan</label>
            <select class="form-select">
              <option>Semua Program</option>
              <option>Bantuan Sembako</option>
              <option>BLT</option>
              <option>Bantuan Pendidikan</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select">
              <option>Semua Status</option>
              <option>Disetujui</option>
              <option>Diproses</option>
              <option>Ditolak</option>
            </select>
          </div>

          <div class="col-md-12 d-flex justify-content-end gap-2">
            <button class="btn btn-primary">
              <i class="bi bi-filter"></i> Tampilkan
            </button>
            <button class="btn btn-success">
              <i class="bi bi-file-earmark-excel"></i> Export Excel
            </button>
            <button class="btn btn-danger">
              <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- SUMMARY REKAP -->
  <div class="container mt-3">
    <div class="row g-3">

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <small>Total Kegiatan</small>
            <h4 class="text-primary"><?= $total_data; ?></h4>
            <div style="display: flex; justify-content: space-between;">
              <small class="text-muted" style="font-size: 11px;">
                  -
              </small>
              <span class="text-muted" style="font-size: 11px;">

              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <small>Total Kegiatan Terealisasi 30%</small>
              <span
                class="btn bg-warning btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalRealisasiRendah"
                style="font-size: 14px; padding: 0 3px;"
              >
                <i class="bi bi-info-circle"></i>
              </span>
            </div>
            <h4 class="text-success"><?= $row_data_up_30['jumlah']; ?></h4>
            <div style="display: flex; justify-content: space-between;">
              <small class="text-muted" style="font-size: 11px;">
                  <?= $persentase_data_up_30; ?>% kegiatan sudah terealisasi 
              </small>
              <span class="text-muted" style="font-size: 11px;">
                  
              </span>
            </div>            
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <small>Total Anggaran</small>
            <h4 class="text-warning"><?= number_format($total_pagu_anggaran, 0, ',', '.'); ?></h4>
            <div style="display: flex; justify-content: space-between;">
              <small class="text-muted" style="font-size: 11px;">
                  total anggaran digunakan <?= number_format($row_anggaran_used['total_realisasi'], 0, ',', '.'); ?>
              </small>
              <span class="text-muted" style="font-size: 11px;">
                  <?= $row_anggaran_used['persentase_realisasi']; ?>%
              </span>
            </div>

          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <small>Sisa Anggaran</small>
            <h4 class="text-danger"><?= number_format($row_anggaran_left['sisa_anggaran'], 0, ',', '.'); ?></h4>
            <div style="display: flex; justify-content: space-between;">
              <small class="text-muted" style="font-size: 11px;">
                  <?= $row_anggaran_left['persentase_sisa']; ?>% anggaran tersisa
              </small>
              <span class="text-muted" style="font-size: 11px;">

              </span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- TABEL REKAP -->
  <div class="container mt-3 mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white">
        <h6 class="mb-0">Tabel Rekapitulasi Bantuan Sosial</h6>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama Penerima</th>
              <th>Program</th>
              <th>Wilayah</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Nominal</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Ahmad</td>
              <td>Bantuan Sembako</td>
              <td>Kecamatan A</td>
              <td>2025-01-02</td>
              <td><span class="badge bg-success">Disetujui</span></td>
              <td>Rp 500.000</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Siti</td>
              <td>BLT</td>
              <td>Kecamatan B</td>
              <td>2025-01-02</td>
              <td><span class="badge bg-warning">Diproses</span></td>
              <td>Rp 1.000.000</td>
            </tr>
            <tr>
              <td>3</td>
              <td>Budi</td>
              <td>Bantuan Pendidikan</td>
              <td>Kecamatan C</td>
              <td>2025-01-01</td>
              <td><span class="badge bg-danger">Ditolak</span></td>
              <td>Rp 750.000</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

 <!-- MODAL DETAIL SUB-KEGIATAN DIBAWAH REALISASI  -->
<div class="modal fade" id="modalRealisasiRendah" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title">
          Daftar Kegiatan dengan Realisasi di Bawah 30%
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="table-responsive">
          <table 
          class="table table-bordered table-hover small"
          data-toggle="table"
          data-pagination="true">
            <thead class="table-light text-center">
              <tr>
                <th>No</th>
                <th>Sub Kegiatan</th>
                <th>Bidang</th>
                <th>Pagu Anggaran</th>
                <th>Total Realisasi</th>
                <th>Persentase</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($result) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['sub_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['bidang']) ?></td>
                    <td class="text-end">
                      <?= number_format($row['pagu_anggaran'], 0, ',', '.') ?>
                    </td>
                    <td class="text-end">
                      <?= number_format($row['total_realisasi'], 0, ',', '.') ?>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-warning text-dark">
                        <?= $row['persentase_realisasi'] ?>%
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">
                    Tidak ada data
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">
          Tutup
        </button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.js"></script>
</body>
</html>