<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "dinsos_tarakan";

$conn = new mysqli($host, $username, $password, $database);


if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone untuk Kalimantan Utara
date_default_timezone_set('Asia/Makassar');
?>