<?php
include 'config/database.php';

$id = $_GET['id'];
$tahun = $_GET['tahun'];

$q = $conn->prepare("SELECT * FROM kegiatan WHERE id=? AND tahun=?");
$q->bind_param("ii",$id,$tahun);
$q->execute();
$data = $q->get_result()->fetch_assoc();

echo json_encode($data);
