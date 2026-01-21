<?php
require 'config/database.php';
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $jabatan = explode("_", $username);
}

if (isset($_POST['submit'])) {

    // Ambil data dari form
    $judul_kegiatan   = mysqli_real_escape_string($conn, $_POST['judul_kegiatan']);
    $tanggal          = $_POST['tanggal'];
    $waktu            = $_POST['waktu'];
    $tempat           = mysqli_real_escape_string($conn, $_POST['tempat']);
    $pihak_mengundang = mysqli_real_escape_string($conn, $_POST['pihak_mengundang']);

    // Checkbox bidang terkait
    if (!empty($_POST['bidang_terkait'])) {
        $bidang_terkait = implode(', ', $_POST['bidang_terkait']);
    } else {
        echo "<script>
                alert('Minimal pilih satu bidang terkait');
                history.back();
              </script>";
        exit;
    }

    // Query insert
    $sql = "INSERT INTO undangan
            (judul_kegiatan, tanggal, waktu, tempat, pihak_mengundang, bidang_terkait, created_at, updated_at)
            VALUES
            ('$judul_kegiatan', '$tanggal', '$waktu', '$tempat', '$pihak_mengundang', '$bidang_terkait', NOW(), NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data undangan berhasil disimpan');
                window.location.href = 'input_undangan.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan data: " . mysqli_error($conn) . "');
                history.back();
              </script>";
    }
}

/* ======================
   PROSES EDIT DATA
====================== */
if (isset($_POST['update'])) {

    $id               = (int) $_POST['id'];
    $judul_kegiatan   = mysqli_real_escape_string($conn, $_POST['judul_kegiatan']);
    $tanggal          = $_POST['tanggal'];
    $waktu            = $_POST['waktu'];
    $tempat           = mysqli_real_escape_string($conn, $_POST['tempat']);
    $pihak_mengundang = mysqli_real_escape_string($conn, $_POST['pihak_mengundang']);

    // Validasi checkbox
    if (empty($_POST['bidang_terkait'])) {
        echo "<script>
                alert('Minimal pilih satu bidang terkait');
                history.back();
              </script>";
        exit;
    }

    $bidang_terkait = implode(', ', $_POST['bidang_terkait']);

    $sql = "UPDATE undangan SET
              judul_kegiatan   = '$judul_kegiatan',
              tanggal          = '$tanggal',
              waktu            = '$waktu',
              tempat           = '$tempat',
              pihak_mengundang = '$pihak_mengundang',
              bidang_terkait   = '$bidang_terkait',
              updated_at       = NOW()
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data undangan berhasil diperbarui');
                window.location.href = 'input_undangan.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal update data: " . mysqli_error($conn) . "');
                history.back();
              </script>";
    }
}
/* ======================
   PROSES HAPUS DATA
====================== */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM undangan WHERE id='$id'");
    header("Location: input_kegiatan.php");
    exit;
}

/* ======================
   AMBIL DATA
====================== */
$query = mysqli_query($conn, "SELECT * FROM undangan ORDER BY created_at DESC");
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

        .sidebar a:hover,
        .sidebar a.active {
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
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
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

        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <h5 class="mb-0">Input Data Surat Undangan</h5>

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

        <?php if ($_SESSION['role'] == 'Admin' || 1 == "1") : ?>

            <div class="container mt-4">

                <div id="toolbar">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUndangan">
                        <i class="bi bi-plus-lg"></i> Tambah Undangan
                    </button>
                </div>

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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $no = 1; foreach ($data as $u) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($u['judul_kegiatan']); ?></td>
                                    <td class="text-center">
                                        <?= date('d-m-Y', strtotime($u['tanggal'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <?= substr($u['waktu'], 0, 5); ?>
                                    </td>
                                    <td><?= htmlspecialchars($u['tempat']); ?></td>
                                    <td><?= htmlspecialchars($u['pihak_mengundang']); ?></td>
                                    <td><?= htmlspecialchars($u['bidang_terkait']); ?></td>

                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-1">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $u['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>   

                                            <a href="?hapus=<?= $u['id']; ?>" onclick="return confirm('Yakin hapus data?')" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                
                                <?php
                                $bidangTerpilih = explode(', ', $u['bidang_terkait']);
                                ?>

                                <div class="modal fade" id="edit<?= $u['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $u['id']; ?>">

                                        <div class="modal-header">
                                        <h5 class="modal-title">Edit Undangan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Kegiatan</label>
                                            <textarea name="judul_kegiatan" class="form-control" rows="2" required><?= htmlspecialchars($u['judul_kegiatan']); ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control" value="<?= $u['tanggal']; ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                            <label class="form-label">Waktu</label>
                                            <input type="time" name="waktu" class="form-control" value="<?= $u['waktu']; ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                            <label class="form-label">Tempat</label>
                                            <input type="text" name="tempat" class="form-control" value="<?= htmlspecialchars($u['tempat']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Pihak Yang Mengundang</label>
                                            <input type="text" name="pihak_mengundang" class="form-control" value="<?= htmlspecialchars($u['pihak_mengundang']); ?>" required>
                                        </div>

                                        <!-- BIDANG TERKAIT (CHECKBOX) -->
                                        <div class="mb-3">
                                            <label class="form-label">Bidang Yang Terkait</label>

                                            <?php
                                            $daftarBidang = [
                                            'Perencanaan dan Keuangan',
                                            'Umum dan Kepegawaian',
                                            'Rehabilitasi Sosial',
                                            'Perlindungan dan Jaminan Sosial',
                                            'Pemberdayaan Sosial',
                                            'Kepala Sub Bagian',
                                            'Kepala Bidang',
                                            'Kepala Dinas'
                                            ];

                                            foreach ($daftarBidang as $i => $bidang) :
                                            ?>
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                type="checkbox"
                                                name="bidang_terkait[]"
                                                value="<?= $bidang; ?>"
                                                id="editBidang<?= $u['id'] . $i; ?>"
                                                <?= in_array($bidang, $bidangTerpilih) ? 'checked' : ''; ?>>

                                                <label class="form-check-label" for="editBidang<?= $u['id'] . $i; ?>">
                                                <?= $bidang; ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>

                                        </div>

                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="update" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Update
                                        </button>
                                        </div>

                                    </form>
                                    </div>
                                </div>
                                </div>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                    
                    
                    <div class="modal fade" id="tambahUndangan" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Surat Undangan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Kegiatan</label>
                                            <textarea name="judul_kegiatan" class="form-control" rows="2" required></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Waktu</label>
                                                <input type="time" name="waktu" class="form-control" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tempat</label>
                                                <input type="text" name="tempat" class="form-control" placeholder="Ruang / Lokasi kegiatan" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pihak Yang Mengundang</label>
                                            <input type="text" name="pihak_mengundang" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                        <label class="form-label">Bidang Yang Terkait</label>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Perencanaan dan Keuangan"
                                            id="bidang1">
                                          <label class="form-check-label" for="bidang1">
                                            Perencanaan dan Keuangan
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Umum dan Kepegawaian"
                                            id="bidang2">
                                          <label class="form-check-label" for="bidang2">
                                            Umum dan Kepegawaian
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Rehabilitasi Sosial"
                                            id="bidang3">
                                          <label class="form-check-label" for="bidang3">
                                            Rehabilitasi Sosial
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Perlindungan dan Jaminan Sosial"
                                            id="bidang4">
                                          <label class="form-check-label" for="bidang4">
                                            Perlindungan dan Jaminan Sosial
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Pemberdayaan Sosial"
                                            id="bidang5">
                                          <label class="form-check-label" for="bidang5">
                                            Pemberdayaan Sosial
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Pemberdayaan Sosial"
                                            id="bidang5">
                                          <label class="form-check-label" for="bidang5">
                                            Kepala Sub Bagian
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Pemberdayaan Sosial"
                                            id="bidang5">
                                          <label class="form-check-label" for="bidang5">
                                            Kepala Dinas
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox"
                                            name="bidang_terkait[]"
                                            value="Pemberdayaan Sosial"
                                            id="bidang5">
                                          <label class="form-check-label" for="bidang5">
                                            Kepala Bidang
                                          </label>
                                        </div>

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
                </div>

            <?php else : ?>
                <div class="text-mute d-flex justify-content-center align-items-center vh-100 fs-4">
                    <i class="bi bi-info-circle-fill me-2"></i> Anda tidak punya akses di halaman ini!.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.0/dist/bootstrap-table.min.js"></script>

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
            };
            document.getElementById('currentDateTime').innerHTML =
                `<i class="bi bi-clock"></i> ${now.toLocaleString('id-ID', options)}`;
        }
        updateDateTime();
        setInterval(updateDateTime, 60 * 1000);
    </script>

</body>

</html>