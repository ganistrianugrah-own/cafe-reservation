<?php include "config.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">

<?php
$q = mysqli_query($koneksi, "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1");
$d = mysqli_fetch_assoc($q);
$total_kursi = $d ? $d['total'] : 0;

$q2 = mysqli_query($koneksi, "SELECT SUM(jumlah_kursi) AS terpakai FROM booking WHERE status IN('pending','datang')");
$d2 = mysqli_fetch_assoc($q2);
$terpakai = $d2['terpakai'] ? $d2['terpakai'] : 0;

$tersedia = $total_kursi - $terpakai;
?>

<div class="page-title">Dashboard Kursi</div>

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
            <div class="info-label">Kursi Terpakai</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="info-box">
            <h3 class="text-success"><?= $tersedia ?></h3>
            <div class="info-label">Kursi Tersedia</div>
        </div>
    </div>

</div>

<!-- ============================
        TABEL LIST BOOKING + ACTION
============================= -->
    <div class="mt-4 mb-3">
    <label class="form-label fw-bold">🔍 Cari Booking</label>
    <input type="text" id="searchBox" class="form-control" placeholder="Cari booking (ID, nama, no hp, alamat, jenis)...">
    </div>
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
                    <th width="150">Action</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $q = mysqli_query($koneksi, "SELECT * FROM booking ORDER BY id DESC");

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

                    <!-- ====================
                         ACTION UPDATE STATUS
                    ===================== -->
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

                </tr>
                <?php } ?>

            </tbody>

        </table>
    </div>
</div>

</div>
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
</script>

<script>
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
