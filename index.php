<?php
include 'config.php';

// Ambil total kursi dari admin
$qTotal = mysqli_query($koneksi, "SELECT total FROM total_kursi WHERE id=1");
$dTotal = mysqli_fetch_assoc($qTotal);
$totalKursi = intval($dTotal['total']);

// Hitung terpakai (pending + datang)
$qTerpakai = mysqli_query($koneksi,
    "SELECT COALESCE(SUM(jumlah_kursi), 0) AS terpakai 
     FROM booking 
     WHERE LOWER(status) IN ('pending', 'datang')"
);
$dTerpakai = mysqli_fetch_assoc($qTerpakai);
$terpakai = intval($dTerpakai['terpakai']);

// Hitung sisa kursi
$sisa = max(0, $totalKursi - $terpakai);

// Tentukan status
if ($sisa > 0) {
    $status = "Kursi Masih Tersedia ($sisa kursi tersisa)";
    $color  = "#28a745"; 
} else {
    $status = "Kursi Sedang Penuh";
    $color  = "#dc3545";
}



// Nomor WA
$wa_number = "6285715102910";
$wa_text = urlencode("Halo kak, saya mau booking café.");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Status Kursi Café</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: #f5f5f5;
        font-family: 'Segoe UI', sans-serif;
        padding-top: 60px;
    }
    .container {
        max-width: 550px;
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    .cafe-name {
        font-size: 32px;
        font-weight: 800;
        color: #333;
    }
    .address {
        font-size: 15px;
        color: #666;
        margin-bottom: 10px;
    }
    .clock {
        font-size: 18px;
        font-weight: 600;
        color: #444;
        margin-bottom: 25px;
    }
    .status-text {
        font-size: 26px;
        font-weight: bold;
        margin: 25px 0;
        color: <?= $color ?>;
    }
    .wa-btn {
        background: #25D366;
        color: white;
        padding: 12px 20px;
        border-radius: 50px;
        font-size: 18px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 15px;
    }
    .wa-btn:hover {
        opacity: .87;
        color: white;
    }
</style>
</head>

<body>

<div class="container">
    
    <div class="cafe-name">Kinasih Cafe & Space</div>
    <div class="address">Jl. Mawar No. 12, Karawang</div>

    <!-- JAM REALTIME -->
    <div class="clock" id="clock">Loading time...</div>

    <div class="status-text"><?= $status ?></div>

   <a class="wa-btn" 
   href="https://api.whatsapp.com/send?phone=<?= $wa_number ?>&text=<?= $wa_text ?>" 
   target="_blank">

        <img src="https://img.icons8.com/color/48/000000/whatsapp.png" width="28">
        Booking via WhatsApp
    </a>

</div>

<script>
// ============= JAM REALTIME =============
function updateClock() {
    const now = new Date();

    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false
    };

    document.getElementById("clock").innerHTML =
        now.toLocaleString("id-ID", options);
}

setInterval(updateClock, 1000);
updateClock();
// ========================================
</script>

</body>
</html>
