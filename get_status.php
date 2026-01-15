<?php
include 'config.php';

// Nomor WhatsApp
$wa_number = "6285715102910";
$wa_text   = urlencode("Halo kak, saya mau booking café via WhatsApp.");

function getKursiStatus($koneksi) {
    $today = date("Y-m-d");

    // Ambil total kursi terakhir dari admin
    $qTotal = mysqli_query($koneksi, 
        "SELECT total 
         FROM total_kursi 
         ORDER BY id DESC 
         LIMIT 1"
    );
    $dTotal = mysqli_fetch_assoc($qTotal);
    $totalKursi = intval($dTotal['total'] ?? 0);

    // Hitung kursi yang terpakai hari ini
    $qTerpakai = mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah_kursi),0) AS terpakai
         FROM booking
         WHERE tgl_booking = '$today'
         AND LOWER(status) IN ('pending','datang')"
    );
    $dTerpakai = mysqli_fetch_assoc($qTerpakai);
    $terpakai = intval($dTerpakai['terpakai'] ?? 0);

    // Hitung sisa kursi
    $sisa = max(0, $totalKursi - $terpakai);

    // Tentukan status teks
    if ($terpakai == 0) {
        $status = "Kursi Tersedia : $totalKursi kursi";
        $color  = "#28a745";
    } 
    else if ($sisa > 0) {
        $status = "Kursi Tersedia : $sisa kursi";
        $color  = "#28a745";
    } 
    else {
        $status = "Kursi Sedang Penuh";
        $color  = "#dc3545";
    }

    return [
        'status'    => $status,
        'color'     => $color,
        'total'     => $totalKursi,
        'terpakai'  => $terpakai,
        'sisa'      => $sisa
    ];
}

// Load pertama kali
$kursi = getKursiStatus($koneksi);
?>
