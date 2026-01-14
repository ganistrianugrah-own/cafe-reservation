<?php 
include __DIR__ . '/../config.php';
require __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;
?>


<!DOCTYPE html>
<html>
<head>
    <title>Laporan Booking Kinasih Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include "navbar_laporan.php"; ?>

<div class="container mt-4">

    <div class="page-title mb-3">📋 Laporan Booking</div>

    <!-- =======================
         FILTER STATUS & TANGGAL
    ======================== -->
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
                <a href="laporan.php" class="btn btn-secondary w-100">Reset</a>
            </div>

            <div class="col-md-2">
                <a href="cetak_laporan.php?<?= http_build_query($_GET) ?>" target="_blank" class="btn btn-success w-100">Cetak PDF</a>
            </div>
        </form>
    </div>

    <!-- =======================
         TABEL DATA BOOKING
    ======================== -->
    <div class="card card-custom p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Booking</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Tgl Booking</th>
                        <th>Jam Datang</th>
                        <th>Jenis Booking</th>
                        <th>Jumlah Kursi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $where = [];
                if(isset($_GET['status_filter']) && $_GET['status_filter']!='') {
                    $status = $_GET['status_filter'];
                    $where[] = "status='$status'";
                }
                if(isset($_GET['tgl_filter']) && $_GET['tgl_filter']!='') {
                    $tgl = $_GET['tgl_filter'];
                    $where[] = "tgl_booking='$tgl'";
                }
                $whereSql = count($where)>0 ? "WHERE ".implode(' AND ', $where) : "";

                $q = mysqli_query($koneksi, "SELECT * FROM booking $whereSql ORDER BY id DESC");

                if(mysqli_num_rows($q) == 0){
                    echo '<tr><td colspan="9" class="text-center">Belum ada data booking.</td></tr>';
                } else {
                    while($b = mysqli_fetch_assoc($q)){
                        echo '<tr>
                            <td>'.$b['id_booking'].'</td>
                            <td>'.$b['nama'].'</td>
                            <td>'.$b['nohp'].'</td>
                            <td>'.$b['alamat'].'</td>
                            <td>'.$b['tgl_booking'].'</td>
                            <td>'.$b['jam_datang'].'</td>
                            <td>'.$b['jenis_booking'].'</td>
                            <td>'.$b['jumlah_kursi'].'</td>
                            <td>'.$b['status'].'</td>
                        </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
