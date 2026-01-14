<?php
include "config.php";

$id_booking   = $_POST['id_booking'];
$jenis        = $_POST['jenis_booking'];
$tgl          = $_POST['tgl_booking'];
$kursi        = $_POST['jumlah_kursi'];
$nama         = $_POST['nama'];
$alamat       = $_POST['alamat'];
$nohp         = $_POST['nohp'];
$jam          = $_POST['jam_datang'];
$status       = $_POST['status'];

mysqli_query($koneksi, "INSERT INTO booking
(id_booking, jenis_booking, tgl_booking, jumlah_kursi, nama, alamat, nohp, jam_datang, status)
VALUES
('$id_booking', '$jenis', '$tgl', '$kursi', '$nama', '$alamat', '$nohp', '$jam', '$status')
");

header("Location: admin_dashboard.php");
?>
