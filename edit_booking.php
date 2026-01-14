<?php
include "config.php";

// Ambil id_booking dari URL
$id_booking = $_GET['id_booking'] ?? '';

if(!$id_booking) {
    header("Location: admin_dashboard.php");
    exit;
}

// Ambil data booking
$q = mysqli_query($koneksi, "SELECT * FROM booking WHERE id_booking='$id_booking'");
$booking = mysqli_fetch_assoc($q);

if(!$booking) {
    echo "Booking tidak ditemukan!";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container mt-4">
    <div class="page-title">Edit Booking</div>

    <div class="card card-custom p-4">
        <form action="update_booking.php" method="POST" class="row g-3" id="editForm">

            <input type="hidden" name="id_booking" value="<?= $booking['id_booking'] ?>">

            <div class="col-md-4">
                <label class="form-label">ID Booking</label>
                <input type="text" class="form-control" value="<?= $booking['id_booking'] ?>" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Jenis Booking</label>
                <select class="form-select" name="jenis_booking">
                    <option <?= $booking['jenis_booking']=='On the Spot'?'selected':'' ?>>On the Spot</option>
                    <option <?= $booking['jenis_booking']=='Via Web'?'selected':'' ?>>Via Web</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tanggal Booking</label>
                <input type="date" class="form-control" name="tgl_booking" value="<?= $booking['tgl_booking'] ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Jumlah Kursi</label>
                <input type="number" class="form-control" name="jumlah_kursi" value="<?= $booking['jumlah_kursi'] ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama" value="<?= $booking['nama'] ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">No HP</label>
                <input type="text" class="form-control" name="nohp" value="<?= $booking['nohp'] ?>" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" required><?= $booking['alamat'] ?></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Jam Kedatangan</label>
                <input type="time" class="form-control" name="jam_datang" value="<?= $booking['jam_datang'] ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="pending" <?= $booking['status']=='pending'?'selected':'' ?>>Pending</option>
                    <option value="datang" <?= $booking['status']=='datang'?'selected':'' ?>>Datang</option>
                    <option value="selesai" <?= $booking['status']=='selesai'?'selected':'' ?>>Selesai</option>
                    <option value="cancel" <?= $booking['status']=='cancel'?'selected':'' ?>>Cancel</option>
                </select>
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-success btn-lg">Update Booking</button>
            </div>

        </form>
    </div>
</div>

<script>
// Popup konfirmasi update
document.getElementById("editForm").addEventListener("submit", function(e){
    e.preventDefault();
    const form = this;

    Swal.fire({
        title: 'Update Booking?',
        text: 'Apakah Anda yakin ingin menyimpan perubahan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if(result.isConfirmed){
            form.submit();
        }
    });
});
</script>

</body>
</html>
