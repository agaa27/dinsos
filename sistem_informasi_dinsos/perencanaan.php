<?php
require 'config/database.php';
session_start();

if (isset($_POST['submit'])){  
  $sasaran   = $_POST['sasaran_strategis'];
  $indikator = $_POST['indikator_kinerja'];
  $satuan    = $_POST['satuan'];
  $target    = $_POST['target_tahunan'];
  $tahun     = $_POST['tahun'];
  $bidang    = $_POST['bidang'];

  $sql = "INSERT INTO indikator
          (sasaran_strategis, indikator_kinerja, satuan, target_tahunan, tahun, bidang)
          VALUES
          ('$sasaran','$indikator','$satuan','$target','$tahun','$bidang')";

  if (mysqli_query($conn, $sql)) {
      echo "<script>
            alert('data berhasil ditambah');
        </script>";
  } else {
      echo "<script>
            alert('data gagal ditambah: " . mysqli_error($conn) . "');
        </script>";
  }

  header("Location: input_data.php");
}



$sql = "
    SELECT
    i.id as indikator_id,
    i.sasaran_strategis,
    i.indikator_kinerja AS indikator,
    i.satuan,
    i.program,
    i.target_tahunan AS target,
    i.tahun,


    rt.id as rt_id,
    rt.triwulan,
    rt.realisasi,

    -- Persentase capaian indikator
    ROUND(
        CASE 
            WHEN i.target_tahunan > 0 
            THEN (rt.realisasi / i.target_tahunan) * 100
            ELSE 0
        END
    , 2) AS persentase,

    rt.pagu_anggaran,
    rt.realisasi_anggaran,

    -- Persentase realisasi anggaran
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


if (isset($_POST['update'])) {


    $indikator_id       = $_POST['indikator_id'];
    $triwulan           = $_POST['triwulan'];
    $realisasi          = $_POST['realisasi'];
    $pagu_anggaran      = $_POST['pagu_anggaran'];
    $realisasi_anggaran = $_POST['realisasi_anggaran'];

    /* CEK: data triwulan sudah ada atau belum */
    $cek = $conn->prepare("
        SELECT id
        FROM realisasi_triwulan
        WHERE indikator_id = ?
          AND triwulan = ?
    ");
    $cek->bind_param("ii", $indikator_id, $triwulan);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {

        // UPDATE
        $sql = "
            UPDATE realisasi_triwulan
            SET
                realisasi = ?,
                pagu_anggaran = ?,
                realisasi_anggaran = ?
            WHERE indikator_id = ?
              AND triwulan = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "dddii",
            $realisasi,
            $pagu_anggaran,
            $realisasi_anggaran,
            $indikator_id,
            $triwulan
        );

    } else {

        // INSERT
        $sql = "
            INSERT INTO realisasi_triwulan
            (id,indikator_id, triwulan, realisasi, pagu_anggaran, realisasi_anggaran)
            VALUES ('', ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);
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
    echo "<script>
                alert('Order berhasil diperbarui!');
                window.location.href = 'perencanaan.php';
              </script>";

  }
        



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

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i>
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success']); endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); endif; ?>


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

  <!-- Table Preview -->
    <div class="container mt-4">          
          <div id="toolbar" class="mb-2 d-flex gap-2">
            <select id="filterTahun" class="form-select form-select-sm w-auto">
              <option value="">Semua Tahun</option>
                <?php
                $tahunUnik = array_unique(array_column($data, 'tahun'));
                foreach ($tahunUnik as $tahun):
                ?>
                  <option value="<?= $tahun ?>"><?= $tahun ?></option>
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

          <table class="table table-dark table-hover table-responsive small"
          data-toggle="table" 
          data-search="true" 
          data-pagination="true" 
          data-toolbar="#toolbar"
          style="background-color: #343a40;">
            <thead>
              <tr>
                <th>#</th>
                <th data-field="sasaran_strategis">Sasaran Strategis</th>
                <th data-field="indikator_kinerja">Indikator</th>
                <th data-field="program">Program</th>
                <th data-field="satuan">Satuan</th>
                <th data-field="target">Target TW</th>
                <th data-field="realisasi">Realisasi</th>
                <th data-field="persentase">%</th>
                <th data-field="pagu_anggaran">Pagu</th>
                <th data-field="realisasi_anggaran">Realisasi Anggaran</th>
                <th data-field="persentase_anggaran">% Anggaran</th>
                <th data-field="triwulan">TW</th>
                <th data-field="tahun">Tahun</th>
                <th>aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; foreach($data as $n) : ?>
              <tr>
                <td><?=  $no; ?></td>
                <td><?= $n['sasaran_strategis'];  ?></td>
                <td><?= $n['indikator'];  ?></td>
                <td><?= $n['program'];  ?></td>
                <td><?= $n['satuan'];  ?></td>
                <td><?= $n['target'];  ?></td>
                <td><?= $n['realisasi'];  ?></td>           
                <td><?= $n['persentase'];  ?></td>          
                <td><?= $n['pagu_anggaran'];  ?></td>           
                <td><?= $n['realisasi_anggaran'];  ?></td>           
                <td><?= $n['persentase_anggaran'];  ?></td> 
                <td><?= $n['triwulan'];  ?></td> 
                <td><?= $n['tahun'];  ?></td> 
                <td>
                 <!-- Tombol Edit -->
                <button 
                  class="btn btn-warning btn-sm tombolEdit"
                  data-id="<?= $n['indikator_id']; ?>"
                  data-bs-toggle="modal"
                  data-bs-target="#modalEdit">
                  <i class="bi bi-pencil"></i>
                </button>


                <a href="hapusData.php?id=<?= $n['indikator_id']; ?>" class="btn btn-danger btn-sm rounded-5" onclick="return confirm('Yakin hapus order ini?')"><i class="bi bi-trash"></i></a>
                </td>        
                <?php $no++; ?>               
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          
        </div>
      </div>
    </div>
  </div>
</div> <!-- end main-content -->



<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Edit Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
                    <!-- modal edit -->
          <form method="post" action="">

            <!-- HIDDEN -->
            <input type="hidden" name="indikator_id" id="indikator_id">

            <!-- VISIBLE -->
            <div class="mb-2">
            <label>Realisasi</label>
            <textarea name="realisasi" class="form-control" id="realisasi"></textarea>
            </div>

            <div class="row">
            <div class="col-md-4">
            <label>Triwulan</label>
            <input type="text" name="triwulan" class="form-control">
            </div>
            <div class="col-md-4">
            <label>Realisasi anggaran</label>
            <input type="realisasi_anggaran" name="realisasi_anggaran" class="form-control">
            </div>
            <div class="col-md-4">
            <label>Pagu anggaran</label>
            <input type="number" name="pagu_anggaran" class="form-control">
            </div>
            
            <button type="submit" name="update" class="btn btn-primary mt-3">
              Update
            </button>
          </form>
        </div>
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
      minute: '2-digit',
      // second: '2-digit'
    };
    document.getElementById('currentDateTime').innerHTML =
      `<i class="bi bi-clock"></i> ${now.toLocaleString('id-ID', options)}`;
  }
  updateDateTime();
  setInterval(updateDateTime, 60*1000);

  const $table = $('table');

$('#filterTahun, #filterTW').on('change', function () {
    const tahun = $('#filterTahun').val();
    const tw = $('#filterTW').val();

    let filters = {};

    if (tahun && tahun !== '') {
        filters.tahun = tahun;
    }

    if (tw && tw !== '') {
        filters.triwulan = tw;
    }

    // Jika tidak ada filter → reset
    if (Object.keys(filters).length === 0) {
        $table.bootstrapTable('clearFilterControl');
        return;
    }

    $table.bootstrapTable('filterBy', filters);
});


  $(document).on('click', '.tombolEdit', function () {
    let id = $(this).data('id');

    $.ajax({
        url: 'get_data_indikator.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (d) {
            $('#indikator_id').val(d.indikator_id);

            $('#realisasi').val(d.realisasi);
            $('#target').val(d.target_tahunan);
            $('#triwulan').val(d.triwulan);
            $('#realisasi_anggaran').val(d.realisasi_anggaran);
            $('#pagu_anggaran').val(d.pagu_anggaran);
            $('#tahun').val(d.tahun);
        }
    });
});


</script>

</body>
</html>