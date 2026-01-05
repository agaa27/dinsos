<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DINSOS-PM | Perencanaan</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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

<!-- Sidebar -->
<?php include "includes/sidebar.php"; ?>

<!-- Main Content -->
<div class="main-content">

  <!-- Navbar (SAMA PERSIS DENGAN DASHBOARD) -->
  <nav class="navbar navbar-expand-lg navbar-light d-flex mt-0">
    <div class="container-fluid">
      <h5 class="mb-0">Keuangan dan Perencanaan</h5>

      <span class="date" id="currentDateTime">
        <i class="bi bi-clock"></i> Mon, 01 Jan 2025, 08.30 AM
      </span>

      <div class="d-flex align-items-center">
        <i class="bi bi-bell me-3 fs-5"></i>

        <div class="account-dropdown position-relative">
          <button class="btn account-btn d-flex align-items-center">
            <i class="bi bi-person-circle fs-4 me-2"></i>
            <h6 class="mb-0">Hello, Administrator</h6>
          </button>

          <div class="dropdown-content">
            <p><strong>Administrator</strong></p>
            <p>admin@dinsos.go.id</p>
            <p>0856736263</p>
            <p><a href="#">Logout</a></p>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- ISI HALAMAN -->
  <div class="container mt-2">

    <div class="card shadow-sm border-0">
      <div class="card-header bg-white">
        <h6 class="mb-0">
          <i class="bi bi-clipboard-check me-2"></i>
          Form Perencanaan Kegiatan
        </h6>
      </div>

      <div class="card-body">
        <form method="POST">

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Nama Kegiatan</label>
              <input type="text" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Bidang / Seksi</label>
              <select class="form-select" required>
                <option value="">-- Pilih --</option>
                <option>Seksi Rehabilitasi Sosial</option>
                <option>Seksi Perlindungan & Jaminan Sosial</option>
                <option>Seksi Pemberdayaan Sosial</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Deskripsi Kegiatan</label>
              <textarea class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Bulan</label>
              <select class="form-select">
                <option>Januari</option>
                <option>Februari</option>
                <option>Maret</option>
                <option>April</option>
                <option>Mei</option>
                <option>Juni</option>
                <option>Juli</option>
                <option>Agustus</option>
                <option>September</option>
                <option>Oktober</option>
                <option>November</option>
                <option>Desember</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Triwulan</label>
              <select class="form-select">
                <option>Triwulan I</option>
                <option>Triwulan II</option>
                <option>Triwulan III</option>
                <option>Triwulan IV</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Anggaran (Rp)</label>
  <input
    type="number"
    name="anggaran"
    class="form-control"
    placeholder="Contoh: 10000000"
    required
  >
</div>

            <div class="col-12 text-end mt-3">
              <button class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
              </button>
            </div>

          </div>

        </form>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
document.addEventListener("DOMContentLoaded", function () {

  function updateDateTime() {
    const el = document.getElementById("currentDateTime");
    if (!el) return; // pengaman

    const now = new Date();
    const options = {
      weekday: 'short',
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    };

    el.innerHTML = `<i class="bi bi-clock"></i> ${now.toLocaleString('id-ID', options)}`;
  }

  updateDateTime();
  setInterval(updateDateTime, 1000);

});
</script>
</body>
</html>
