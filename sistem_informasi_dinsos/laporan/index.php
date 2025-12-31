<?php
require_once '../config/database.php';
require_once '../includes/header.php';
?>

<div class="main-content">
    <div class="page-header">
        <h2><i class="fas fa-file-alt"></i> Daftar Laporan</h2>
        <div class="page-actions">
            <a href="input.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Laporan
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Laporan</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>15 Jan 2024</td>
                            <td>Bansos Tunai</td>
                            <td>Penyaluran Bantuan Sosial Tahap 3</td>
                            <td><span class="badge badge-success">Approved</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>