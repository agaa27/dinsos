<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $jabatan = explode(" ", $username);  
}

if (isset($_POST['submit'])) {

    $sasaran   = $_POST['sasaran'];
    $indikator = $_POST['indikator'];
    $program   = $_POST['program'];
    $kegiatan  = $_POST['kegiatan'];
    $subkegiatan = $_POST['subkegiatan'];
    $satuan    = $_POST['satuan'];
    $target    = $_POST['target'];
    $tahun     = $_POST['tahun'];
    $pagu      = $_POST['pagu_anggaran'];
    $bidang    = $_POST['bidang'];

    $sql = "INSERT INTO kegiatan 
        (sasaran_strategis, indikator_kinerja, program, kegiatan, sub_kegiatan, satuan, target, tahun, pagu_anggaran, bidang)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssidis",
        $sasaran,
        $indikator,
        $program,
        $kegiatan,
        $subkegiatan,
        $satuan,
        $target,
        $tahun,
        $pagu,
        $bidang
    );

    if ($stmt->execute()) {
        $_SESSION['notif'] = [
            'type' => 'success',
            'message' => 'Data berhasil ditambah!'
        ];
    } else {
        $_SESSION['notif'] = [
            'type' => 'danger',
            'message' => 'Data gagal ditambah!'
        ];
    }
    header("Location: input_data.php");
    exit;
}

/* ======================
   PROSES EDIT DATA
====================== */
if (isset($_POST['update'])) {

    $id        = $_POST['id'];
    $sasaran   = $_POST['sasaran'];
    $indikator = $_POST['indikator'];
    $program   = $_POST['program'];
    $kegiatan  = $_POST['kegiatan'];
    $subkegiatan = $_POST['subkegiatan'];
    $satuan    = $_POST['satuan'];
    $target    = $_POST['target'];
    $tahun     = $_POST['tahun'];
    $pagu      = $_POST['pagu_anggaran'];
    $bidang    = $_POST['bidang'];

    $sql = "UPDATE kegiatan SET
        sasaran_strategis=?,
        indikator_kinerja=?,
        program=?,
        kegiatan=?,
        sub_kegiatan=?,
        satuan=?,
        target=?,
        tahun=?,
        pagu_anggaran=?,
        bidang=?
        WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssdidsi",
        $sasaran,
        $indikator,
        $program,
        $kegiatan,
        $subkegiatan,
        $satuan,
        $target,
        $tahun,
        $pagu,
        $bidang,
        $id
    );

    
    if ($stmt->execute()) {
        $_SESSION['notif'] = [
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ];
    } else {
        $_SESSION['notif'] = [
            'type' => 'danger',
            'message' => 'Data gagal diubah!'
        ];
    }
    header("Location: input_data.php");
    exit;
}

/* ======================
   PROSES HAPUS DATA
====================== */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    if (mysqli_query($conn, "DELETE FROM kegiatan WHERE id='$id'")) {
        $_SESSION['notif'] = [
            'type' => 'warning',
            'message' => 'Data berhasil dihapus!'
        ];
    } else {
        $_SESSION['notif'] = [
            'type' => 'danger',
            'message' => 'Data gagal dihapus!'
        ];
    }
    header("Location: input_data.php");
    exit;
}

/* ======================
   AMBIL DATA
====================== */
$query = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY created_at DESC");
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DINSOS-PM | Input Data</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.css">


  <style>
    body { background-color: #f8f9fa; }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      background-color: #202f5b;
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
      background-color: #1151d3;
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

/* notif */
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

<!-- Sidebar -->
<?php include "includes/sidebar.php"; ?>

<div class="main-content">

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


  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-primary text-white">
    <div class="container-fluid">
      <h5 class="mb-0">Kelola Data Kegiatan</h5>

      <!-- Realtime Clock -->
      <span class="date" id="currentDateTime">
        <i class="bi bi-clock"></i> --
      </span>

      <div class="d-flex align-items-center">

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
    </div>
  </nav>  
  
  <?php if ($_SESSION['role'] == 'Admin'):?>

    <!-- Table Preview -->
    <div class="container mt-4">          
          <div id="toolbar">
            <button id="btn-add" class="btn btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#tambahKegiatan">
              <i class="bi bi-plus-lg"></i> Tambah Data Kegiatan
            </button>
          </div>

          <table class="table table-bordered table-striped small"
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
            <thead>
              <tr>
                <th>#</th>
                <th>Sasaran Strategis</th>
                <th>Indikator</th>
                <th>Program</th>
                <th>Satuan</th>
                <th>Target</th>
                <th>Pagu</th>
                <th>Kegiatan</th>
                <th>Sub Kegiatan</th>
                <th>Bidang</th>
                <th>Aksi</th>                  
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; foreach($data as $n) : ?>
              <tr>
                <td><?=  $no; ?></td>
                <td><?= $n['sasaran_strategis'];  ?></td>
                <td><?= $n['indikator_kinerja'];  ?></td>
                <td><?= $n['program'];  ?></td>
                <td><?= $n['satuan'];  ?></td>
                <td><?= number_format($n['target'], 2, ',', '.');  ?></td>
                <td><?= number_format($n['pagu_anggaran'], 2, ',', '.');  ?></td>
                <td><?= $n['kegiatan'];  ?></td>           
                <td><?= $n['sub_kegiatan'];  ?></td>           
                <td><?= $n['bidang'];  ?></td>
                <td class="bg-white">
                  <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-primary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#add<?= $n['id']; ?>">
                      <i class="bi bi-plus-circle-fill"></i>
                    </button>

                    <button class="btn btn-warning btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#edit<?= $n['id']; ?>">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <a href="?hapus=<?= $n['id']; ?>"
                      class="btn btn-danger btn-sm"
                      onclick="return confirm('Yakin hapus data?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>

         
                <?php $no++; ?>               
              </tr>
              <div class="modal fade" id="edit<?= $n['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">

                    <form method="POST">
                      <input type="hidden" name="id" value="<?= $n['id']; ?>">

                      <div class="modal-header">
    
                        <h5 class="modal-title">Edit Data</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">

                        <div class="mb-3">
                          <label class="form-label">Sasaran Strategis</label>
                          <textarea name="sasaran" class="form-control" rows="2"><?= $n['sasaran_strategis']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Indikator Kinerja</label>
                          <textarea name="indikator" class="form-control" rows="2"><?= $n['indikator_kinerja']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Program</label>
                          <textarea name="program" class="form-control" rows="2"><?= $n['program']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Kegiatan</label>
                          <textarea name="kegiatan" class="form-control" rows="2"><?= $n['kegiatan']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Sub Kegiatan</label>
                          <textarea name="subkegiatan" class="form-control" rows="2"><?= $n['sub_kegiatan']; ?></textarea>
                        </div>

                        <div class="row">
                          <div class="col-md-4 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="<?= $n['satuan']; ?>">
                          </div>

                          <div class="col-md-4 mb-3">
                            <label class="form-label">Target</label>
                            <input type="number" name="target" class="form-control" value="<?= $n['target']; ?>">
                          </div>

                          <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= $n['tahun']; ?>">
                          </div>

                          <div class="col-md-4 mb-3">
                            <label class="form-label">Pagu Anggaran</label>
                            <input type="number" name="pagu_anggaran" class="form-control" value="<?= $n['pagu_anggaran']; ?>">
                          </div>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Bidang</label>
                          <select name="bidang" class="form-select">
                            <option value="<?= $n['bidang']; ?>"><?= $n['bidang']; ?></option>
                            <option value="Perencanaan dan Keuangan">Perencanaan dan Keuangan</option>
                            <option value="Umum dan Kepegawaian">Umum dan Kepegawaian</option>
                            <option value="Rehabilitasi Sosial">Rehabilitasi Sosial</option>
                            <option value="Perlindungan dan Jaminan Sosial">Perlindungan dan Jaminan Sosial</option>
                            <option value="Pemberdayaan Sosial">Pemberdayaan Sosial</option>
                            <option value="Pemberdayaan Masyarakat">Pemberdayaan Masyarakat</option>
                          </select>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
                <!-- MODAL EDIT DATA END  -->

<!-- MODAL TAMBAH DATA VERSI MUDAH -->
                <div class="modal fade" id="add<?= $n['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">

                  <!-- $sasaran   = $_POST['sasaran'];
                  $indikator = $_POST['indikator'];
                  $program   = $_POST['program'];
                  $kegiatan  = $_POST['kegiatan'];
                  $subkegiatan = $_POST['subkegiatan'];
                  $satuan    = $_POST['satuan'];
                  $target    = $_POST['target'];
                  $tahun     = $_POST['tahun'];
                  $pagu      = $_POST['pagu_anggaran'];
                  $bidang    = $_POST['bidang']; -->

                    <form method="POST">

                      <div class="modal-header">
    
                        <h5 class="modal-title">Tambah Data</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">

                        <div class="mb-3">
                          <label class="form-label">Sasaran Strategis</label>
                          <textarea name="sasaran" class="form-control" rows="2"><?= $n['sasaran_strategis']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Indikator Kinerja</label>
                          <textarea name="indikator" class="form-control" rows="2"><?= $n['indikator_kinerja']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Program</label>
                          <textarea name="program" class="form-control" rows="2"><?= $n['program']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Kegiatan</label>
                          <textarea name="kegiatan" class="form-control" rows="2"><?= $n['kegiatan']; ?></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Sub Kegiatan</label>
                          <textarea name="subkegiatan" class="form-control" rows="2"><?= $n['sub_kegiatan']; ?></textarea>
                        </div>

                        <div class="row">
                          <div class="col-md-4 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="<?= $n['satuan']; ?>">
                          </div>

                          <div class="col-md-4 mb-3">
                            <label class="form-label">Target</label>
                            <input type="number" name="target" class="form-control" value="<?= $n['target']; ?>">
                          </div>

                          <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= $n['tahun']; ?>">
                          </div>

                          <div class="col-md-4 mb-3">
                            <label class="form-label">Pagu Anggaran</label>
                            <input type="number" name="pagu_anggaran" class="form-control" value="<?= $n['pagu_anggaran']; ?>">
                          </div>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Bidang</label>
                          <select name="bidang" class="form-select">
                            <option value="<?= $n['bidang']; ?>"><?= $n['bidang']; ?></option>
                            <option value="Perencanaan dan Keuangan">Perencanaan dan Keuangan</option>
                            <option value="Umum dan Kepegawaian">Umum dan Kepegawaian</option>
                            <option value="Rehabilitasi Sosial">Rehabilitasi Sosial</option>
                            <option value="Perlindungan dan Jaminan Sosial">Perlindungan dan Jaminan Sosial</option>
                            <option value="Pemberdayaan Sosial">Pemberdayaan Sosial</option>
                            <option value="Pemberdayaan Masyarakat">Pemberdayaan Masyarakat</option>
                          </select>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="submit" class="btn btn-primary">Tambah</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>


<!-- END MODAL TAMBAH DATA VERSI MUDAH -->

              <?php endforeach; ?>
            </tbody>
          </table>
          
        </div>
      </div>
    </div>
  </div>
</div> <!-- end main-content -->


<div class="modal fade" id="tambahKegiatan" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Tambah Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Sasaran Strategis -->
          <div class="mb-3">
            <label class="form-label">Sasaran Strategis</label>
            <textarea name="sasaran" class="form-control" rows="2" required></textarea>
          </div>

          <!-- Indikator Kinerja -->
          <div class="mb-3">
            <label class="form-label">Indikator Kinerja</label>
            <textarea name="indikator" class="form-control" rows="2" required></textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Program</label>
            <textarea name="program" class="form-control" rows="2" required></textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Kegiatan</label>
            <textarea name="kegiatan" class="form-control" rows="2" required></textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Sub Kegiatan</label>
            <textarea name="subkegiatan" class="form-control" rows="2" required></textarea>
          </div>

          <div class="row">
            <!-- Satuan -->
            <div class="col-md-4 mb-3">
              <label class="form-label">Satuan</label>
              <input type="text" name="satuan" class="form-control" placeholder="%, Orang, Lembaga" required>
            </div>

            <!-- Target Tahunan -->
            <div class="col-md-4 mb-3">
              <label class="form-label">Target</label>
              <input type="number" step="0.01" name="target" class="form-control" required>
            </div>

            <!-- Tahun -->
            <div class="col-md-4 mb-3">
              <label class="form-label">Tahun</label>
              <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label">Pagu Anggaran</label>
              <input type="number" name="pagu_anggaran" class="form-control" placeholder="Rp. " required>
            </div>
          </div>

          <!-- Bidang -->
          <div class="mb-3">
            <label class="form-label">Bidang</label>
            <select name="bidang" class="form-select" required>
              <option value="">-- Pilih Bidang --</option>
              <option value="Perencanaan dan Keuangan">Perencanaan dan Keuangan</option>
              <option value="Umum dan Kepegawaian">Umum dan Kepegawaian</option>
              <option value="Rehabilitasi Sosial">Rehabilitasi Sosial</option>
              <option value="Perlindungan dan Jaminan Sosial">Perlindungan dan Jaminan Sosial</option>
              <option value="Pemberdayaan Sosial">Pemberdayaan Sosial</option>
              <option value="Pemberdayaan Masyarakat">Pemberdayaan Masyarakat</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
    <?php else : ?>
      <div class="text-mute d-flex justify-content-center align-items-center vh-100 text-secondary fs-4"><i class="bi bi-info-circle-fill me-2"></i> Anda tidak punya akses di halaman ini!.</div>
    <?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.js"></script>

<!-- Realtime Clock Script -->
<script>
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