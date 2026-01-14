<?php
$koneksi = mysqli_connect("localhost", "root", "", "cafe");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
