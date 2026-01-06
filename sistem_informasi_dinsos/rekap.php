<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DINSOS-PM | Rekapitulasi Bantuan Sosial</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
        <div class="account-dropdown position-relative">
          <button class="btn account-btn d-flex align-items-center">
            <i class="bi bi-person-circle fs-4 me-2"></i>
            <h6 class="mb-0">Hello, Administrator</h6>
          </button>

          <div class="dropdown-content">
            <p><strong>Administrator</strong></p>
            <p>admin@dinsos.go.id</p>
            <p>0856736263</p>
            <p><a class="login-logout" href="#">Logout</a></p>
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
            <small>Total Pengajuan</small>
            <h4 class="text-primary">1.245</h4>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <small>Disetujui</small>
            <h4 class="text-success">980</h4>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <small>Diproses</small>
            <h4 class="text-warning">185</h4>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card card-summary shadow-sm border-0">
          <div class="card-body">
            <small>Ditolak</small>
            <h4 class="text-danger">80</h4>
            <small class="text-muted">Data dummy</small>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
