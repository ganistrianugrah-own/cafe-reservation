<?php
include "config.php";

$id_booking    = $_POST['id_booking'];
$jenis         = $_POST['jenis_booking'];
$tgl           = $_POST['tgl_booking'];
$jumlah_kursi  = $_POST['jumlah_kursi'];
$nama          = $_POST['nama'];
$nohp          = $_POST['nohp'];
$alamat        = $_POST['alamat'];
$jam_datang    = $_POST['jam_datang'];
$status        = $_POST['status'];

mysqli_query($koneksi, "
    UPDATE booking SET
        jenis_booking='$jenis',
        tgl_booking='$tgl',
        jumlah_kursi='$jumlah_kursi',
        nama='$nama',
        nohp='$nohp',
        alamat='$alamat',
        jam_datang='$jam_datang',
        status='$status'
    WHERE id_booking='$id_booking'
");

header("Location: admin_dashboard.php");
exit;
?>
