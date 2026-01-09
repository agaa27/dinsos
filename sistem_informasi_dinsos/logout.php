<?php
session_start(); // Mulai session agar bisa dihapus

// ---- Hapus semua data session ----
$_SESSION = array();


// ---- Akhiri session ----
session_destroy();

// ---- Redirect ke login.php ----
header("Location: index.php");
exit;
?>
