<?php
include "config.php";

// ============================
// Ambil tanggal hari ini
// ============================
$today = date('Y-m-d');

// ============================
// Ambil filter dari form GET (status/tanggal)
// ============================
$statusFilter = $_GET['status_filter'] ?? '';
$tglFilter    = $_GET['tgl_filter'] ?? '';

// ============================
// Bangun WHERE SQL
// ============================
// Default: data yang diinput hari ini (created_at)
$where = ["DATE(created_at)='$today'"];

if($statusFilter) {
    $where[] = "status='$statusFilter'";
}
if($tglFilter) {
    $where[] = "tgl_booking='$tglFilter'";
}

$whereSql = '';
if(count($where) > 0){
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// ============================
// Ambil data booking sesuai filter
// ============================
$q = mysqli_query($koneksi, "SELECT * FROM booking $whereSql ORDER BY id DESC");

// ============================
// Hitung kursi hari ini (pending + datang)
// ============================
$resultTotal = mysqli_query($koneksi, "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1");
$rowTotal = mysqli_fetch_assoc($resultTotal);
$total_kursi = intval($rowTotal['total'] ?? 0);

$resultTerpakai = mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(jumlah_kursi),0) AS terpakai 
     FROM booking 
     WHERE DATE(created_at)='$today' AND status IN ('pending','datang')"
);
$rowTerpakai = mysqli_fetch_assoc($resultTerpakai);
$terpakai = intval($rowTerpakai['terpakai'] ?? 0);

$tersedia = max(0, $total_kursi - $terpakai);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Kinasih Cafe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">

    <div class="page-title mb-3">Dashboard Booking Hari Ini (Data Input: <?= $today ?>)</div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="info-box">
                <h3><?= $total_kursi ?></h3>
                <div class="info-label">Total Kursi</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <h3 class="text-danger"><?= $terpakai ?></h3>
                <div class="info-label">Booked</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <h3 class="text-success"><?= $tersedia ?></h3>
                <div class="info-label">Available</div>
            </div>
        </div>
    </div>

    <!-- ===========================
         Search & Filter
    ============================ -->
    <div class="mt-4 mb-3">
        <label class="form-label fw-bold">🔍 Cari Booking</label>
        <input type="text" id="searchBox" class="form-control" placeholder="Cari booking (ID, nama, no hp, alamat, jenis)...">
    </div>

    <div class="card card-custom p-3 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter Status</label>
                <select name="status_filter" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" <?= ($statusFilter=='pending')?'selected':'' ?>>Pending</option>
                    <option value="datang" <?= ($statusFilter=='datang')?'selected':'' ?>>Datang</option>
                    <option value="selesai" <?= ($statusFilter=='selesai')?'selected':'' ?>>Selesai</option>
                    <option value="cancel" <?= ($statusFilter=='cancel')?'selected':'' ?>>Cancel</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Tanggal Booking</label>
                <input type="date" name="tgl_filter" class="form-control" value="<?= $tglFilter ?>">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            <div class="col-md-2">
                <a href="admin_dashboard.php" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
    </div>

    <!-- ===========================
         Tabel Booking
    ============================ -->
    <div class="page-title mt-5 d-flex justify-content-between align-items-center">
        <span>Daftar Booking <class="text-muted" style="font-size:14px;"><?= $today ?></span>
    </div>

    <div class="card card-custom p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Booking</th>
                        <th>Jenis</th>
                        <th>Tgl Booking</th>
                        <th>Kursi</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Jam Datang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(mysqli_num_rows($q)==0){
                    echo '<tr><td colspan="9" class="text-center">Belum ada transaksi hari ini.</td></tr>';
                } else {
                    while($b=mysqli_fetch_assoc($q)){
                        echo '<tr>
                                <td>'.$b['id_booking'].'</td>
                                <td>'.$b['jenis_booking'].'</td>
                                <td>'.$b['tgl_booking'].'</td>
                                <td>'.$b['jumlah_kursi'].'</td>
                                <td>'.$b['nama'].'</td>
                                <td>'.$b['nohp'].'</td>
                                <td>'.$b['alamat'].'</td>
                                <td>'.$b['jam_datang'].'</td>
                                <td>';
                        if($b['status']=='pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                        elseif($b['status']=='datang') echo '<span class="badge bg-info text-dark">Datang</span>';
                        elseif($b['status']=='selesai') echo '<span class="badge bg-success">Selesai</span>';
                        else echo '<span class="badge bg-danger">Cancel</span>';
                        echo '</td></tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// Search realtime
document.getElementById("searchBox").addEventListener("keyup", function () {
    let keyword = this.value.toLowerCase();
    document.querySelectorAll("table tbody tr").forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
    });
});
</script>

</body>
</html>
