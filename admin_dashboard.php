<?php include "config.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">

<?php
// Ambil filter dari form GET
$statusFilter = $_GET['status_filter'] ?? '';
$tglFilter    = $_GET['tgl_filter'] ?? '';

$where = [];
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

// Ambil data booking sesuai filter
$q = mysqli_query($koneksi, "SELECT * FROM booking $whereSql ORDER BY id DESC");

// Perhitungan kursi tetap sama, pakai status pending + datang
$resultTotal = mysqli_query($koneksi, "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1");
$rowTotal = mysqli_fetch_assoc($resultTotal);
$total_kursi = intval($rowTotal['total'] ?? 0);

$resultTerpakai = mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(jumlah_kursi),0) AS terpakai 
     FROM booking 
     WHERE status IN ('pending','datang')"
);
$rowTerpakai = mysqli_fetch_assoc($resultTerpakai);
$terpakai = intval($rowTerpakai['terpakai'] ?? 0);

$tersedia = max(0, $total_kursi - $terpakai);
?>

<div class="container container-fixed">

<div class="page-title">Dashboard Booking Kursi</div>

<div class="row g-3">

    <div class="col-md-4">
        <div class="info-box">
            <h3><?= $total_kursi ?></h3>
            <div class="info-label">Total</div>
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


<!-- ============================ SEARCH ============================ -->
<div class="mt-4 mb-3">
    <label class="form-label fw-bold">🔍 Cari Booking</label>
    <input type="text" id="searchBox" class="form-control" placeholder="Cari booking (ID, nama, no hp, alamat, jenis)...">
</div>

<!-- ============================ FILTER ============================ -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Filter Status</label>
            <select name="status_filter" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="pending" <?= (isset($_GET['status_filter']) && $_GET['status_filter']=='pending')?'selected':'' ?>>Pending</option>
                <option value="datang" <?= (isset($_GET['status_filter']) && $_GET['status_filter']=='datang')?'selected':'' ?>>Datang</option>
                <option value="selesai" <?= (isset($_GET['status_filter']) && $_GET['status_filter']=='selesai')?'selected':'' ?>>Selesai</option>
                <option value="cancel" <?= (isset($_GET['status_filter']) && $_GET['status_filter']=='cancel')?'selected':'' ?>>Cancel</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Tanggal Booking</label>
            <input type="date" name="tgl_filter" class="form-control" value="<?= $_GET['tgl_filter'] ?? '' ?>">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>

        <div class="col-md-2">
            <a href="admin_dashboard.php" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>
</div>


<!-- ============================ TABEL ============================ -->
<div class="page-title mt-5">Daftar Booking</div>
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
                    <th width="150">Ubah Status</th>
                    <th width="150">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (mysqli_num_rows($q) == 0) {
                    echo '<tr><td colspan="10" class="text-center">Belum ada data booking.</td></tr>';
                }

                while ($b = mysqli_fetch_assoc($q)) {
                ?>
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
                        <?php if ($b['status'] == "pending") { ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php } elseif ($b['status'] == "selesai") { ?>
                            <span class="badge bg-success">Selesai</span>
                        <?php } elseif ($b['status'] == "datang") { ?>
                            <span class="badge bg-info text-dark">Datang</span>
                        <?php } else { ?>
                            <span class="badge bg-danger">Cancel</span>
                        <?php } ?>
                    </td>

                    <!-- ACTION UPDATE STATUS -->
                    <td>
                      <form action="update_status.php" method="POST" class="d-flex gap-1 update-form">
                        <input type="hidden" name="id_booking" value="<?= $b['id_booking'] ?>">
                        <select name="status" class="form-select form-select-sm">
                            <option value="pending"  <?= $b['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="datang"   <?= $b['status']=='datang'?'selected':'' ?>>Datang</option>
                            <option value="selesai"  <?= $b['status']=='selesai'?'selected':'' ?>>Selesai</option>
                            <option value="cancel"   <?= $b['status']=='cancel'?'selected':'' ?>>Cancel</option>
                        </select>
                        <button class="btn btn-primary btn-sm">OK</button>
                      </form>
                    </td>

                                        <!-- ACTION -->
                    <td>
                        <button type="button" class="btn btn-warning btn-sm edit-btn" 
                        data-id="<?= $b['id_booking'] ?>">Edit</button>

                        <a href="print_booking.php?id=<?= $b['id_booking'] ?>" 
                            class="btn btn-success btn-sm" target="_blank">Cetak</a>

                    </td>

                    

                </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>
</div>

</div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        const idBooking = this.dataset.id;

        Swal.fire({
            title: 'Edit Booking?',
            text: 'Apakah Anda ingin mengubah data booking ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Edit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                // Arahkan ke halaman edit
                window.location.href = "edit_booking.php?id_booking=" + idBooking;
            }
        });
    });
});
</script>


<!-- ============================ SCRIPTS ============================ -->
<script>
document.getElementById("searchBox").addEventListener("keyup", function () {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        if (text.includes(keyword)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});

// SWEETALERT KONFIRMASI UPDATE STATUS
document.querySelectorAll('.update-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // stop submit dulu

        Swal.fire({
            title: "Ubah Status?",
            text: "Apakah Anda yakin ingin mengubah status booking ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, ubah",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // submit asli
            }
        });
    });
});
</script>

</body>
</html>
