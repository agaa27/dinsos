<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "dinsos_tarakan";

$conn = new mysqli("127.0.0.1", "root", "", "dinsos_tarakan");


if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone untuk Kalimantan Utara
date_default_timezone_set('Asia/Makassar');
?>