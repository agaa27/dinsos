<?php
require 'config/database.php';
require 'fungsi.php';
session_start();

if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $jabatan = explode(" ", $username);  
}

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}
// var_dump($_SESSION);die;
$role = $_SESSION['role'];
$isAdmin = ($role === 'Admin');


$limit = 5; // jumlah card per halaman
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page  = max($page, 1);
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

$role = mysqli_real_escape_string($conn, $role);
$safe = mysqli_real_escape_string($conn, $search);

$where = "WHERE bidang_terkait LIKE '%$role%'";

if (!empty($search)) {
  $where .= " AND (
    judul_kegiatan LIKE '%$safe%'
    OR tempat LIKE '%$safe%'
    OR pihak_mengundang LIKE '%$safe%'
  )";
}


// total data
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) total FROM undangan $where");
$totalData  = mysqli_fetch_assoc($totalQuery)['total'];
$totalPage  = ceil($totalData / $limit);

if ($isAdmin) {
    // ADMIN → tampilkan semua undangan
    $sql_tampil = "SELECT * FROM undangan ORDER BY status_kegiatan ASC, tanggal ASC, waktu ASC";
} else {
    // USER → tampilkan berdasarkan role
    $sql_tampil = " SELECT *
                    FROM undangan
                    $where
                    ORDER BY status_kegiatan ASC, tanggal ASC, waktu ASC
                    LIMIT $start, $limit";
}

$query = mysqli_query($conn, $sql_tampil);



if (isset($_POST['status'])){
  $id_undangan = $_POST['id_undangan'];
  $bukti     = upload_undangan();

    if ($bukti === false) {
        exit; // upload error
    }

    if ($bukti === null) {
        // tidak upload → jangan update kolom bukti
        $sql = "UPDATE undangan 
          SET menghadiri = '$role', status_kegiatan = 'Terlaksana'
          WHERE id = '$id_undangan'";
        
    } else {
        // upload ada → update bukti
        $sql = "UPDATE undangan 
          SET menghadiri = '$role', status_kegiatan = 'Terlaksana', bukti = '$bukti'
          WHERE id = '$id_undangan'";
    }

    if (mysqli_query($conn, $sql)) {      
          $_SESSION['notif'] = [
              'type' => 'success',
              'message' => 'Data berhasil disimpan!'
          ];
          header("Location: dashboard.php");
          exit;
      } else {
          $_SESSION['notif'] = [
              'type' => 'gagal',
              'message' => 'Data gagal disimpan!'
          ];
      }  
}

function formatTanggal($tanggal) {
    return date('d F Y', strtotime($tanggal));
}

// kegiatan dengan realisasi tertinggi 
$query_data_total = "SELECT 
    bidang,
    SUM(
        COALESCE(realisasi_bulan1,0) +
        COALESCE(realisasi_bulan2,0) +
        COALESCE(realisasi_bulan3,0) +
        COALESCE(realisasi_bulan4,0) +
        COALESCE(realisasi_bulan5,0) +
        COALESCE(realisasi_bulan6,0) +
        COALESCE(realisasi_bulan7,0) +
        COALESCE(realisasi_bulan8,0) +
        COALESCE(realisasi_bulan9,0) +
        COALESCE(realisasi_bulan10,0) +
        COALESCE(realisasi_bulan11,0) +
        COALESCE(realisasi_bulan12,0)
    ) AS total_realisasi,
    SUM(target) AS total_target,
    ROUND(
        (SUM(
            COALESCE(realisasi_bulan1,0) +
            COALESCE(realisasi_bulan2,0) +
            COALESCE(realisasi_bulan3,0) +
            COALESCE(realisasi_bulan4,0) +
            COALESCE(realisasi_bulan5,0) +
            COALESCE(realisasi_bulan6,0) +
            COALESCE(realisasi_bulan7,0) +
            COALESCE(realisasi_bulan8,0) +
            COALESCE(realisasi_bulan9,0) +
            COALESCE(realisasi_bulan10,0) +
            COALESCE(realisasi_bulan11,0) +
            COALESCE(realisasi_bulan12,0)
        ) / SUM(target)) * 100, 2
    ) AS persentase_realisasi
FROM kegiatan
WHERE tahun = YEAR(CURDATE())
GROUP BY bidang
ORDER BY persentase_realisasi DESC
LIMIT 1;
";
$result_data_total = mysqli_query($conn, $query_data_total);
$row_data_total = mysqli_fetch_assoc($result_data_total);



// sub kegiatan dengan 0 persen realisasi
$query_anggaran_left = "SELECT
  bidang,
    COUNT(*) AS jumlah_sub_kegiatan_0_persen
FROM kegiatan
WHERE 
    COALESCE(realisasi_bulan1,0) +
    COALESCE(realisasi_bulan2,0) +
    COALESCE(realisasi_bulan3,0) +
    COALESCE(realisasi_bulan4,0) +
    COALESCE(realisasi_bulan5,0) +
    COALESCE(realisasi_bulan6,0) +
    COALESCE(realisasi_bulan7,0) +
    COALESCE(realisasi_bulan8,0) +
    COALESCE(realisasi_bulan9,0) +
    COALESCE(realisasi_bulan10,0) +
    COALESCE(realisasi_bulan11,0) +
    COALESCE(realisasi_bulan12,0) = 0
    AND tahun = YEAR(CURDATE());

";
$result_anggaran_left = mysqli_query($conn, $query_anggaran_left);
$row_anggaran_left = mysqli_fetch_assoc($result_anggaran_left);


// bidang paling banyak 0 nya
$query_anggaran_used = "SELECT 
    bidang,
    COUNT(*) AS jumlah_sub_kegiatan_0_persen
FROM kegiatan
WHERE 
    COALESCE(realisasi_bulan1,0) +
    COALESCE(realisasi_bulan2,0) +
    COALESCE(realisasi_bulan3,0) +
    COALESCE(realisasi_bulan4,0) +
    COALESCE(realisasi_bulan5,0) +
    COALESCE(realisasi_bulan6,0) +
    COALESCE(realisasi_bulan7,0) +
    COALESCE(realisasi_bulan8,0) +
    COALESCE(realisasi_bulan9,0) +
    COALESCE(realisasi_bulan10,0) +
    COALESCE(realisasi_bulan11,0) +
    COALESCE(realisasi_bulan12,0) = 0
    AND tahun = YEAR(CURDATE())
GROUP BY bidang
ORDER BY jumlah_sub_kegiatan_0_persen DESC
LIMIT 1;

";
$result_anggaran_used = mysqli_query($conn, $query_anggaran_used);
$row_anggaran_used = mysqli_fetch_assoc($result_anggaran_used);

// total undangan bulan ini
$query_total_undangan = "SELECT 
    COUNT(*) AS total_undangan_bulan_ini
FROM undangan
";
$result_total_undangan = mysqli_query($conn, $query_total_undangan);
$row_total_undangan = mysqli_fetch_assoc($result_total_undangan);



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DINSOS-PM | Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css">

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
}/* notif */
.notif-wrapper {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1055;
    width: auto;
    max-width: 90%;
}

.notif-wrapper .alert {
    min-width: 300px;
    text-align: center;
}


  </style>
</head>

<body>
  <!-- notif  -->

    <?php if (isset($_SESSION['notif'])): ?>
        <div class="notif-wrapper">
            <div class="alert alert-<?= $_SESSION['notif']['type']; ?> alert-dismissible fade show auto-close shadow"
                role="alert">
                <?= $_SESSION['notif']['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php
        unset($_SESSION['notif']);
        endif;
        ?>

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
              <i class="bi bi-graph-up-arrow fs-5 mx-2"></i>
              Persetase realisasi kinerja tertinggi
            </h6>
            <h5 class="d-flex justify-content-end text-primary">
              <?= $row_data_total['bidang']; ?>
            </h5>
            <small class="text-muted" style="font-size: small;">Dengan <?= $row_data_total['persentase_realisasi']; ?>% dari total target</small>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm mb-2">
          <div class="card-body">
            <h6>
              <i class="bi bi-graph-down-arrow fs-5 mx-2"></i>
              Jumlah Sub Kegiatan 0% realisasi
            </h6>
            <h5 class="d-flex justify-content-end text-primary">
              <?= $row_anggaran_left['jumlah_sub_kegiatan_0_persen']; ?>
            </h5>
            <small class="text-muted" style="font-size: small;">
                <?= $row_anggaran_used['jumlah_sub_kegiatan_0_persen'] ?> di bidang <?= $row_anggaran_used['bidang']; ?>
            </small>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm mb-2">
          <div class="card-body">
            <h6>
              <i class="bi bi-table fs-5 mx-2"></i>
              Total undangan
            </h6>
            <h5 class="d-flex justify-content-end text-primary">
              <?= $row_total_undangan['total_undangan_bulan_ini'] ?>
            </h5>
            <small class="text-muted" style="font-size: small;">-</small>
          </div>
        </div>
      </div>



    </div> <!-- end row -->
  </div> <!-- end container -->

  <div class="container mt-3">
    <div class="card shadow rounded-3">
      <div class="card-body">

        <div class="d-flex justify-content-between my-1">
          <h4 class=" ">Undangan</h4>
          <div class="d-flex">
            
            <form method="get" class="">
            <div class="input-group">
              
              <input type="hidden" name="role" value="<?= $role; ?>">

              <input
                type="text"
                name="search"
                class="form-control form-control-md"
                placeholder="Cari kegiatan, lokasi, atau pengundang..."
                value="<?= htmlspecialchars($search); ?>"
              >
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i>
              </button>
              <button type="submit" formaction="export_undangan.php" class="btn btn-success ms-1">
                  <i class="bi bi-download"></i> Export
              </button>
            </div>
          </form>
          <a href="dashboard.php" class="btn btn-success ms-1"><i class="bi bi-arrow-clockwise"></i></a>
          </div>
        </div>
        <hr class="border border-1 border-dark opacity-100">

        

        
          <?php if (!$isAdmin): ?>
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


                  <form method="post" class="d-inline mt-auto" enctype="multipart/form-data">

                  <?php if (!empty($row['bukti']) || $row['status_kegiatan'] != 'Belum Terlaksana'): ?>

                    <!-- Bukti sudah ada & kegiatan terlaksana -->
                    <a 
                      href="<?= !empty($row['bukti']) ? 'uploads/' . htmlspecialchars($row['bukti']) : '#' ?>" 
                      target="_blank"
                      class="btn btn-info btn-sm rounded-4 <?= empty($row['bukti']) ? 'disabled' : '' ?>"
                    >
                      <i class="bi bi-file-earmark-text"></i>
                      Lihat Bukti: <?= !empty($row['bukti']) ? htmlspecialchars($row['bukti']) : 'Tidak ada bukti'; ?>
                    </a>


                  <?php else: ?>

                    <!-- Bukti belum ada -->
                    <div class="mb-2">
                      <input 
                        type="file" 
                        name="gambar" 
                        class="form-control form-control-sm w-75"
                        <?= $row['status_kegiatan'] === 'Terlaksana' ? 'disabled' : ''; ?>
                      >
                    </div>

                  <?php endif; ?>


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

              <?php if ($totalPage > 1): ?>
              <nav>
                <ul class="pagination justify-content-center mt-4">

                  <!-- Prev -->
                  <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                      href="?page=<?= $page - 1 ?>&search=<?= urlencode($search); ?>">
                      Prev
                    </a>
                  </li>

                  <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                      <a class="page-link"
                        href="?page=<?= $i ?>&search=<?= urlencode($search); ?>">
                        <?= $i ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <!-- Next -->
                  <li class="page-item <?= $page >= $totalPage ? 'disabled' : '' ?>">
                    <a class="page-link"
                      href="?page=<?= $page + 1 ?>&search=<?= urlencode($search); ?>">
                      Next
                    </a>
                  </li>

                </ul>
              </nav>
              <?php endif; ?>



              <?php endif; ?>
          <?php endif; ?>
          

        <?php if ($isAdmin): ?>
              <div class="mt-1">
                <div class="table-responsive">
                  <table id="table-undangan" 
                    class="table table-bordered table-striped small"
                    data-toggle="table"
                    data-search="true"
                    data-pagination="true"
                    data-page-size="10"
                    data-show-columns="true"
                    data-show-toggle="true"
                    data-show-refresh="true"
                    data-resizable="true"
                    data-mobile-responsive="true"
                    data-toolbar="#toolbar">

                    <thead class="table-light text-center">
                      <tr>
                          <th>No</th>
                          <th>Kegiatan</th>
                          <th>Tanggal</th>
                          <th>Waktu</th>
                          <th>Tempat</th>
                          <th>Pihak Yang Mengundang</th>
                          <th>Bidang Yang Terkait</th>
                          <th>menghadiri</th>
                          <th>Status</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                      <tr>
                          <td class="text-center"><?= $no++; ?></td>
                          <td><?= htmlspecialchars($row['judul_kegiatan']); ?></td>
                          <td class="text-center">
                              <?= date('d-m-Y', strtotime($row['tanggal'])); ?>
                          </td>
                          <td class="text-center">
                              <?= substr($row['waktu'], 0, 5); ?>
                          </td>
                          <td><?= htmlspecialchars($row['tempat']); ?></td>
                          <td><?= htmlspecialchars($row['pihak_mengundang']); ?></td>
                          <td><?= htmlspecialchars($row['bidang_terkait']); ?></td>
                          <td><?= htmlspecialchars($row['menghadiri']); ?></td>
                          <td><?= htmlspecialchars($row['status_kegiatan']); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                  </table>
        <?php endif; ?>
            

      </div>
    </div>
  </div>


<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.js"></script>
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

  
// notif 
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        let alert = document.querySelector('.auto-close');
        if (alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 2000); // durasi 3 detik
});
</script>

</body>
</html>
