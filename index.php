<?php
include 'config.php';

// Nomor WhatsApp
$wa_number = "6285715102910";
$wa_text = urlencode("Halo kak, saya mau booking café via WhatsApp.");

function getKursiStatus($koneksi) {
    $today = date("Y-m-d");

    // Ambil total kursi terakhir
    $qTotal = mysqli_query($koneksi, 
        "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1"
    );
    $dTotal = mysqli_fetch_assoc($qTotal);
    $total = intval($dTotal['total'] ?? 0);

    // Hitung kursi terpakai (persis seperti admin_dashboard)
    $qTerpakai = mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah_kursi),0) AS terpakai
         FROM booking
         WHERE DATE(created_at) = '$today'
         AND status IN ('pending','datang')"
    );
    $dTerpakai = mysqli_fetch_assoc($qTerpakai);
    $terpakai = intval($dTerpakai['terpakai'] ?? 0);

    // Hitung sisa kursi
    $sisa = max(0, $total - $terpakai);

    // Tentukan status tampilan
    if ($terpakai == 0) {
        $status = "Kursi Tersedia : $total kursi";
        $color  = "#28a745";
    } elseif ($sisa > 0) {
        $status = "Kursi Tersedia : $sisa kursi";
        $color  = "#28a745";
    } else {
        $status = "Kursi Sedang Penuh";
        $color  = "#dc3545";
    }

    return [
        'status'    => $status,
        'color'     => $color,
        'total'     => $total,
        'terpakai'  => $terpakai,
        'sisa'      => $sisa
    ];
}

$kursi = getKursiStatus($koneksi);
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

        <!-- Clock -->
        <div class="clock" id="clock">Loading time...</div>

        <!-- STATUS KURSI (Realtime + Detail) -->
        <div class="status-text" id="statusText" style="color: <?= $kursi['color'] ?>">
            <?= $kursi['status'] ?><br>
            <span style="color:#444; font-size:14px;">
                Total: <?= $kursi['total'] ?> |
                Terpakai: <?= $kursi['terpakai'] ?> |
                Sisa: <?= $kursi['sisa'] ?>
            </span>
        </div>

        <!-- Tombol Booking -->
        <a class="wa-btn" 
           href="https://api.whatsapp.com/send?phone=<?= $wa_number ?>&text=<?= $wa_text ?>" 
           target="_blank">
            <img src="https://img.icons8.com/color/48/000000/whatsapp.png" width="28">
            Booking via WhatsApp
        </a>

    </div>
</div>

<script>
// Clock realtime
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

// Realtime status kursi
async function updateStatus() {
    try {
        const res = await fetch('get_status.php');
        const data = await res.json();

        const statusText = document.getElementById("statusText");

        statusText.style.color = data.color;
        statusText.innerHTML = `
            ${data.status}<br>
            <span style="color:#444; font-size:14px;">
                Total: ${data.total} |
                Terpakai: ${data.terpakai} |
                Sisa: ${data.sisa}
            </span>
        `;
    } catch (err) {
        console.error("Gagal update status:", err);
    }
}

setInterval(updateStatus, 8000);
updateStatus();
</script>

</body>
</html>
