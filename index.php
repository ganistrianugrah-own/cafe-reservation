<?php
include 'config.php';

// Nomor WhatsApp
$wa_number = "6285715102910";
$wa_text = urlencode("Halo kak, saya mau booking café via WhatsApp.");

// Fungsi untuk hitung status kursi
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

// Ambil status awal
$kursiStatus = getKursiStatus($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kinasih Cafe & Space</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/index.css">
</head>
<body>

<div class="overlay"></div>

<div class="main-container">
    <div class="card-status">
        <div class="cafe-name">Kinasih Cafe & Space - BSD</div>
        <div class="address">Jl. Boulevard No. 12, BSD, Tangerang Selatan</div>

        <!-- Jam Realtime -->
        <div class="clock" id="clock">Loading time...</div>

        <!-- Status Kursi -->
        <div class="status-text" id="statusText" style="color: <?= $kursiStatus['color'] ?>">
            <?= $kursiStatus['status'] ?>
        </div>

        <!-- Tombol WA Booking -->
        <a class="wa-btn" 
           href="https://api.whatsapp.com/send?phone=<?= $wa_number ?>&text=<?= $wa_text ?>" 
           target="_blank">
            <img src="https://img.icons8.com/color/48/000000/whatsapp.png" width="28">
            Booking via WhatsApp
        </a>
    </div>
</div>

<script>
// ===== Jam Realtime =====
function updateClock() {
    const now = new Date();
    const options = {
        weekday: "long", year: "numeric", month: "long", day: "numeric",
        hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false
    };
    document.getElementById("clock").innerHTML = now.toLocaleString("id-ID", options);
}
setInterval(updateClock, 1000);
updateClock();

// ===== Auto Update Status Kursi =====
async function updateStatus() {
    try {
        const res = await fetch('get_status.php');
        const data = await res.json();

        const statusText = document.getElementById('statusText');
        statusText.innerHTML = data.status;
        statusText.style.color = data.color;
    } catch (error) {
        console.error('Gagal update status:', error);
    }
}
setInterval(updateStatus, 10000); // update tiap 10 detik
updateStatus();
</script>

</body>
</html>
