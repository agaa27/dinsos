<?php
require 'config/database.php';

function upload_file(){

    // jika tidak ada file diupload
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === 4) {
        return null; // ⬅️ penting
    }

    $namaFile = $_FILES['gambar']['name'];
    $tmpFile  = $_FILES['gambar']['tmp_name'];
    $error    = $_FILES['gambar']['error'];
    $size     = $_FILES['gambar']['size'];

    // validasi ekstensi
    $ekstensiyangboleh = ['pdf'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiyangboleh)) {
        echo "<script>alert('Hanya menerima file PDF');</script>";
        return false;
    }

    // (opsional) validasi size, contoh 50MB
    // if ($size > 52428800) {
    //     echo "<script>alert('Maksimal 50MB');</script>";
    //     return false;
    // }

    // rename file agar aman
    $namaFileBaru = $namaFile . '.' . $ekstensi;

    move_uploaded_file($tmpFile, "file bukti/" . $namaFile);

    return $namaFile;
}
