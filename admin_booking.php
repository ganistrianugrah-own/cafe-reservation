<?php include "config.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">

<div class="page-title">Form Input Booking</div>

<?php 
$idb = "BK-" . date("dmY");

// Cek ID terakhir yang punya prefix sama
$q = mysqli_query($koneksi, 
    "SELECT id_booking FROM booking 
     WHERE id_booking LIKE '$idb%' 
     ORDER BY id_booking DESC LIMIT 1");

$d = mysqli_fetch_assoc($q);

if ($d) {
    // Ambil 3 digit paling belakang lalu +1
    $lastNum = intval(substr($d['id_booking'], -3)) + 1;
} else {
    $lastNum = 1;
}

// Format 3 digit dan gabungkan
$idb = $idb . "-" . str_pad($lastNum, 3, "0", STR_PAD_LEFT);
?>


<div class="card card-custom p-4">

<form action="save_booking.php" method="POST" class="row g-3">

    <div class="col-md-4">
        <label class="form-label">ID Booking</label>
        <input type="text" class="form-control" name="id_booking" value="<?= $idb ?>" readonly>
    </div>

    <div class="col-md-4">
        <label class="form-label">Jenis Booking</label>
        <select class="form-select" name="jenis_booking">
            <option>On the Spot</option>
            <option>Via Web</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tanggal Booking</label>
        <input type="date" class="form-control" name="tgl_booking" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Jumlah Kursi</label>
        <input type="number" class="form-control" name="jumlah_kursi" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" name="nama" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">No HP</label>
        <input type="text" class="form-control" name="nohp" required>
    </div>

    <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" name="alamat" required></textarea>
    </div>

    <div class="col-md-4">
        <label class="form-label">Jam Kedatangan</label>
        <input type="time" class="form-control" name="jam_datang" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
            <option>pending</option>
            <option>datang</option>
            <option>selesai</option>
            <option>cancel</option>
        </select>
    </div>
    <div class="col-md-12 text-end mt-3">
        <button class="btn btn-primary btn-lg">Simpan Booking</button>
    </div>

</form>

</div>
</div>

</body>
</html>
