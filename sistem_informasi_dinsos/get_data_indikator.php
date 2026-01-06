<?php
require 'config/database.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "
        SELECT
            i.id AS indikator_id,

            rt.id AS rt_id,
            rt.triwulan,
            rt.realisasi,
            rt.pagu_anggaran,
            rt.realisasi_anggaran

        FROM indikator i
        LEFT JOIN realisasi_triwulan rt
            ON rt.indikator_id = i.id
        WHERE i.id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode($result);
}
