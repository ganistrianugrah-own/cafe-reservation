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
$where = ["DATE(created_at)='$today'"];

if($statusFilter)  $where[] = "status='$statusFilter'";
if($tglFilter)     $where[] = "tgl_booking='$tglFilter'";

$whereSql = 'WHERE ' . implode(' AND ', $where);

// ============================
// Ambil data booking
// ============================
$q = mysqli_query($koneksi, "SELECT * FROM booking $whereSql ORDER BY id DESC");

// ============================
// Hitung kursi hari ini
// ============================
$resultTotal = mysqli_query($koneksi, "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1");
$total_kursi = intval(mysqli_fetch_assoc($resultTotal)['total'] ?? 0);

$resultTerpakai = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(jumlah_kursi),0) AS terpakai 
    FROM booking 
    WHERE DATE(created_at)='$today'
      AND status IN ('pending','datang')
");
$terpakai = intval(mysqli_fetch_assoc($resultTerpakai)['terpakai'] ?? 0);

$tersedia = max(0, $total_kursi - $terpakai);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
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
        <div class="col-md-4"><div class="info-box"><h3><?= $total_kursi ?></h3><div>Total Kursi</div></div></div>
        <div class="col-md-4"><div class="info-box"><h3 class="text-danger"><?= $terpakai ?></h3><div>Booked</div></div></div>
        <div class="col-md-4"><div class="info-box"><h3 class="text-success"><?= $tersedia ?></h3><div>Available</div></div></div>
    </div>

    <!-- Search -->
    <div class="mt-4 mb-3">
        <label class="form-label fw-bold">🔍 Cari Booking</label>
        <input type="text" id="searchBox" class="form-control" placeholder="Cari booking...">
    </div>

    <!-- Filter -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter Status</label>
                <select name="status_filter" class="form-select">
                    <option value="">-- Semua --</option>
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

            <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
            <div class="col-md-2"><a href="admin_dashboard.php" class="btn btn-secondary w-100">Reset</a></div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="page-title mt-5 d-flex justify-content-between">
        <span>Daftar Booking <small><?= $today ?></small></span>
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
                        <th width="220px">Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if(mysqli_num_rows($q)==0): ?>

                    <tr><td colspan="10" class="text-center">Belum ada transaksi hari ini.</td></tr>

                <?php else: while($b=mysqli_fetch_assoc($q)): ?>

                    <tr>
                        <td><?= $b['id_booking'] ?></td>
                        <td><?= $b['jenis_booking'] ?></td>
                        <td><?= $b['tgl_booking'] ?></td>
                        <td><?= $b['jumlah_kursi'] ?></td>
                        <td><?= $b['nama'] ?></td>
                        <td><?= $b['nohp'] ?></td>
                        <td><?= $b['alamat'] ?></td>
                        <td><?= $b['jam_datang'] ?></td>

                        <td>
                            <form action="update_status.php" method="POST" class="d-flex gap-1">
                                <input type="hidden" name="id_booking" value="<?= $b['id_booking'] ?>">

                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" <?= $b['status']=='pending'?'selected':'' ?>>Pending</option>
                                    <option value="datang" <?= $b['status']=='datang'?'selected':'' ?>>Datang</option>
                                    <option value="selesai" <?= $b['status']=='selesai'?'selected':'' ?>>Selesai</option>
                                    <option value="cancel" <?= $b['status']=='cancel'?'selected':'' ?>>Cancel</option>
                                </select>

                                <button class="btn btn-primary btn-sm">OK</button>
                            </form>
                        </td>

                        <td>
                            <a href="edit_booking.php?id_booking=<?= $b['id_booking'] ?>" class="btn btn-warning btn-sm">Edit</a>

                            <a href="print_booking.php?id=<?= $b['id_booking'] ?>" target="_blank" class="btn btn-secondary btn-sm">Print</a>

                            <button class="btn btn-danger btn-sm" onclick="hapusBooking('<?= $b['id_booking'] ?>')">Delete</button>
                        </td>
                    </tr>

                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// Realtime search
document.getElementById("searchBox").addEventListener("keyup", function () {
    let key = this.value.toLowerCase();
    document.querySelectorAll("table tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(key) ? "" : "none";
    });
});

function hapusBooking(id) {
    Swal.fire({
        title: "Hapus Booking?",
        text: "Data tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_booking.php?id_booking=" + id;
        }
    });
}
</script>

</body>
</html>
