<?php
$host = "localhost";
$user = "root";        // sesuaikan
$pass = "";            // sesuaikan
$db   = "dinsos_tarakan";   // nama database

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}
