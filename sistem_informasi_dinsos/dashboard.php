<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DINSOS-PM | Dashboard</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

<!-- Main Content -->
<div class="main-content">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light d-flex mt-0">
    <div class="container-fluid">
      <h5 class="mb-0">Dashboard</h5>
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

  <!-- Dashboard Cards -->
  <div class="container mt-2">
    <div class="row g-3">

      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h6><i class="bi bi-cash-stack fs-4 mx-2"></i> Total income in a month</h6>
            <h4 class="d-flex justify-content-end text-primary">Rp. 125.000.000</h4>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h6><i class="bi bi-people fs-4 mx-2"></i> Total Users</h6>
            <h3 class="d-flex justify-content-end">248</h3>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h6><i class="bi bi-cart4 fs-4 mx-2"></i> Orders Today</h6>
            <h3 class="d-flex justify-content-end">17</h3>
            <small class="text-muted">+17 today</small>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Grafik (dummy canvas) -->
  <div class="container mt-2">
    <div class="row g-3">

      <div class="col-md-6">
        <div class="card shadow-sm bg-dark">
          <div class="card-header text-white">
            Produk Terlaris Bulan Ini
          </div>
          <div class="card-body">
            <div class="text-center p-5 border rounded text-light">
              Grafik Dummy
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm bg-dark">
          <div class="card-header text-white">
            Tren Penjualan Bulanan
          </div>
          <div class="card-body">
            <div class="text-center p-5 border rounded text-light">
              Grafik Dummy
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Table Preview -->
  <div class="container mt-2">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white">
        <h6 class="mb-0">Recent Orders</h6>
      </div>

      <div class="card-body">
        <table class="table table-hover table-borderless">
          <thead>
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Program</th>
              <th>Total</th>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Ahmad</td>
              <td>Bantuan Sembako</td>
              <td>Rp 500.000</td>
              <td>2025-01-02</td>
              <td>Selesai</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Siti</td>
              <td>BLT Tahap II</td>
              <td>Rp 1.000.000</td>
              <td>2025-01-02</td>
              <td>Proses</td>
            </tr>
            <tr>
              <td>3</td>
              <td>Budi</td>
              <td>Bantuan Pendidikan</td>
              <td>Rp 750.000</td>
              <td>2025-01-01</td>
              <td>Menunggu</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  \<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


  <script>
      const label_jual = <?= json_encode($label_jual); ?>;
      const dataValues = <?= json_encode($data_jual); ?>;
      const productLabels = <?= json_encode($labels); ?>;
      const productData   = <?= json_encode($data); ?>;
  </script>
  <script>
  const ctx = document.getElementById('chartPenjualan').getContext('2d');

  new Chart(ctx, {
      type: 'line',
      data: {
          labels: label_jual,
          datasets: [{
              label: 'Pendapatan',
              data: dataValues,
              borderWidth: 2,
              tension: 0.4
          }]
      },
      options: {
          responsive: true,
          scales: {
              y: {
                  beginAtZero: true,
                  grid: {
                      display: false   // ❌ matikan grid Y
                  }
              },
              x: {
                  grid: {
                      display: false   // ❌ matikan grid X
                  }
              }
          }
      }
  });


  const ctx2 = document.getElementById('chartProduk').getContext('2d');

new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'Jumlah Dipesan',
            data: productData,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                grid: { display: false }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
  </script>
</body>
</html>
