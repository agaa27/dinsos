<?php
session_start();
require 'config/database.php';

// proteksi
if (!isset($_SESSION['role'])) {
    die('Akses ditolak');
}

$role    = $_SESSION['role'];
$isAdmin = ($role === 'Admin');
$search  = $_GET['search'] ?? '';

// keamanan
$role  = mysqli_real_escape_string($conn, $role);
$search = mysqli_real_escape_string($conn, $search);

// nama file dinamis
$tanggal   = date('Y-m-d_H-i');
$namaRole  = strtolower(str_replace(' ', '_', $role));
$filename  = "undangan_{$namaRole}_{$tanggal}.xls";

// header excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");

// WHERE clause
$where = '';

if (!$isAdmin) {
    $where = "WHERE bidang_terkait LIKE '%$role%'";
}

if (!empty($search)) {
    $where .= ($where ? " AND " : "WHERE ");
    $where .= "(judul_kegiatan LIKE '%$search%'
                OR tempat LIKE '%$search%'
                OR pihak_mengundang LIKE '%$search%')";
}

// query
$sql = "SELECT *
        FROM undangan
        $where
        ORDER BY tanggal ASC, waktu ASC";

$query = mysqli_query($conn, $sql);
?>

<table border="1">
    <thead>
        <tr style="background:#eee; font-weight:bold;">
            <th>No</th>
            <th>Kegiatan</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Tempat</th>
            <th>Pihak Mengundang</th>
            <th>Bidang Terkait</th>
            <th>Menghadiri</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['judul_kegiatan']); ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
            <td><?= substr($row['waktu'], 0, 5); ?></td>
            <td><?= htmlspecialchars($row['tempat']); ?></td>
            <td><?= htmlspecialchars($row['pihak_mengundang']); ?></td>
            <td><?= htmlspecialchars($row['bidang_terkait']); ?></td>
            <td><?= htmlspecialchars($row['menghadiri'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['status_kegiatan']); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
