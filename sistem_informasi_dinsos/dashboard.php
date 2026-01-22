<?php
require 'config/database.php';
session_start();

if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $jabatan = explode(" ", $username);  
}

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];

if (isset($_POST['status'])){
  $id_undangan = $_POST['id_undangan'];

  $sql = "UPDATE undangan 
          SET menghadiri = '$role', status_kegiatan = 'Terlaksana'
          WHERE id = '$id_undangan'";

  if (mysqli_query($conn, $sql)) {
      header("Location: dashboard.php");
      exit;
  } else {
      echo "Gagal memperbarui data";
  }
}


$query = mysqli_query($conn, "
    SELECT *
    FROM undangan
    WHERE bidang_terkait LIKE '%$role%'
    ORDER BY status_kegiatan ASC, tanggal ASC, waktu ASC
");

function formatTanggal($tanggal) {
    return date('d F Y', strtotime($tanggal));
}




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

  <style>
    body { background-color: #f8f9fa; }
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
    .submenu a { padding-left: 40px; font-size: 14px; }
    .main-content { margin-left: 250px; }
    .navbar {
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
    }
    .account-dropdown { position: relative; display: inline-block; }
    .account-dropdown .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 200px;
      box-shadow: 0 8px 16px rgba(0,0,0,.2);
      padding: 10px;
      border-radius: 10px;
      z-index: 10;
    }
    .account-dropdown:hover .dropdown-content { display: block; }
    .account-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
    }
    /* ===== Dashboard Cards ===== */
.info-card h6 {
  font-weight: 600;
}
.info-card h3 {
  font-weight: 700;
}

/* ===== Undangan Card ===== */
.undangan-card {
  background-color: #ffffff;
  color: #111;
  border: 2px solid #000;     /* border hitam agak tebal */
  border-radius: 18px;        /* radius lebih halus */
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  font-family: 'Poppins', sans-serif;
  font-weight: 200;

}


.undangan-text p {
  margin-bottom: 6px;
  font-size: 15px;
}

.badge-status {
  margin-bottom: auto;
  background-color: #0d6efd;
  color:  #fff;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

  </style>
</head>

<body>

<!-- Sidebar -->
<?php include "includes/sidebar.php"; ?>

<!-- Main Content -->
<div class="main-content">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <h5 class="mb-0">Dashboard</h5>

      <!-- Realtime Clock -->
      <span class="date" id="currentDateTime">
        <i class="bi bi-clock"></i> --
      </span>

      <div class="d-flex align-items-center">
        <i class="bi bi-bell me-3 fs-5"></i>

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

  <!-- ===== DASHBOARD CARDS ===== -->
  <div class="container mt-2">
    <div class="row g-3">

      <div class="col-md-4">
        <div class="card shadow-sm mb-2">
          <div class="card-body">
            <h6>
              <i class="bi bi-cash-stack fs-4 mx-2"></i>
              Total Anggaran Terserap
            </h6>
            <h3 class="d-flex justify-content-end text-primary">
              Rp. 120.000.000
            </h3>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h6>
              <i class="bi bi-people fs-4 mx-2"></i>
              Jumlah Penerima Manfaat
            </h6>
            <h3 class="d-flex justify-content-end">248</h3>
            <small class="text-muted">Data dummy</small>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h6>
              <i class="bi bi-calendar-event fs-4 mx-2"></i>
              Kegiatan Berjalan
            </h6>
            <h3 class="d-flex justify-content-end">17</h3>
            <small class="text-muted">+17 today</small>
          </div>
        </div>
      </div>

    </div> <!-- end row -->
  </div> <!-- end container -->

  <div class="container mt-3">
    <div class="card shadow rounded-3">
      <div class="card-body">

        <h5 class="mb-3 ">Undangan</h5><hr class="border border-1 border-dark opacity-100">

        <?php if (mysqli_num_rows($query) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($query)): ?>

            <div class="undangan-card">
              <div class="undangan-text">
                <h6><strong>Kegiatan :</strong> <?= htmlspecialchars($row['judul_kegiatan']); ?></h6>
                <h6>
                  <strong>Waktu :</strong>
                  <?= formatTanggal($row['tanggal']); ?>,
                  <?= date('H:i', strtotime($row['waktu'])); ?> WIB
                </h6>
                <h6><strong>Lokasi :</strong> <?= htmlspecialchars($row['tempat']); ?></h6>
                <h6><strong>Mengundang :</strong> <?= htmlspecialchars($row['pihak_mengundang']); ?></h6>
                <h6>
                  <strong>Menghadiri :</strong>
                  <?= !empty($row['menghadiri']) ? htmlspecialchars($row['menghadiri']) : '-' ?>
                </h6>

              </div>

              <form method="post"  class="d-inline mb-auto">
                <input type="hidden" name="id_undangan" value="<?= $row['id']; ?>">
                <button
                  type="submit" 
                  name="status"
                  class="btn rounded-4 btn-sm <?= $row['status_kegiatan'] == 'Terlaksana' ? 'btn-success' : 'btn-primary'; ?>"
                  <?= $row['status_kegiatan'] == 'Terlaksana' ? 'disabled' : ''; ?>
                >
                  <?= $row['status_kegiatan'] == 'Terlaksana' ? 'Terlaksana' : 'Belum Terlaksana'; ?>
                </button>
              </form>

            </div>

          <?php endwhile; ?>
        <?php else: ?>
          <div class="alert alert-secondary">
            Tidak ada undangan untuk bidang Anda.
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Realtime Clock Script -->
<script>
  function updateDateTime() {
    const now = new Date();
    const options = {
      weekday: 'short',
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    document.getElementById('currentDateTime').innerHTML =
      `<i class="bi bi-clock"></i> ${now.toLocaleString('id-ID', options)}`;
  }
  updateDateTime();
  setInterval(updateDateTime, 60*1000);
</script>

</body>
</html>
