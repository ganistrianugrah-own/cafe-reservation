<?php
include 'config.php';

function getKursiStatus($koneksi) {
    $qTotal = mysqli_query($koneksi, "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1");
    $dTotal = mysqli_fetch_assoc($qTotal);
    $totalKursi = intval($dTotal['total'] ?? 0);

    $qTerpakai = mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah_kursi), 0) AS terpakai 
         FROM booking 
         WHERE LOWER(status) IN ('pending','datang')"
    );
    $dTerpakai = mysqli_fetch_assoc($qTerpakai);
    $terpakai = intval($dTerpakai['terpakai'] ?? 0);

    $sisa = max(0, $totalKursi - $terpakai);

    if ($sisa > 0) {
        $status = "Kursi Masih Tersedia ($sisa kursi tersisa)";
        $color  = "#28a745"; 
    } else {
        $status = "Kursi Sedang Penuh";
        $color  = "#dc3545";
    }

    return ['status'=>$status, 'color'=>$color];
}

header('Content-Type: application/json');
echo json_encode(getKursiStatus($koneksi));
