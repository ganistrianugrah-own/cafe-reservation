<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
include 'config.php';

if(!isset($_GET['id'])) {
    die("ID Booking tidak ditemukan.");
}

$id_booking = $_GET['id'];

// Ambil data booking
$q = mysqli_query($koneksi, "SELECT * FROM booking WHERE id_booking='$id_booking'");
$booking = mysqli_fetch_assoc($q);

if(!$booking){
    die("Booking tidak ditemukan.");
}

$dompdf = new Dompdf();

// HTML untuk PDF ukuran kecil (nota)
$html = '
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    h2 { text-align: center; font-size: 16px; margin-bottom: 5px; }
    .line { border-top: 1px dashed #000; margin: 5px 0; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 3px 0; }
</style>

<h2>Kinasih Cafe & Space</h2>
<p style="text-align:center; font-size:10px;">Jl. Boulevard No.12, BSD, Tangerang Selatan</p>
<div class="line"></div>

<table>
    <tr><td><strong>ID Booking</strong></td><td>: '.$booking['id_booking'].'</td></tr>
    <tr><td><strong>Nama</strong></td><td>: '.$booking['nama'].'</td></tr>
    <tr><td><strong>No HP</strong></td><td>: '.$booking['nohp'].'</td></tr>
    <tr><td><strong>Alamat</strong></td><td>: '.$booking['alamat'].'</td></tr>
    <tr><td><strong>Tgl Booking</strong></td><td>: '.$booking['tgl_booking'].'</td></tr>
    <tr><td><strong>Jam Datang</strong></td><td>: '.$booking['jam_datang'].'</td></tr>
    <tr><td><strong>Jenis Booking</strong></td><td>: '.$booking['jenis_booking'].'</td></tr>
    <tr><td><strong>Jumlah Kursi</strong></td><td>: '.$booking['jumlah_kursi'].'</td></tr>
    <tr><td><strong>Status</strong></td><td>: '.$booking['status'].'</td></tr>
</table>

<div class="line"></div>

<!-- FOOTER TAMBAHAN -->
<table style="width:100%; font-size:10px; text-align:center;">
    <tr><td>Terima kasih telah melakukan booking.</td></tr>
    <tr><td>Mohon datang 15 menit sebelum waktu yang sudah dibooking.</td></tr>
    <tr><td>WA : +62 857-1510-2910</td></tr>
</table>
';

$dompdf->loadHtml($html);

// Set ukuran kertas kecil, misal 80mm x 200mm (nota thermal)
$dompdf->setPaper([0, 0, 226, 400]); 

$dompdf->render();

// Stream ke browser
$filename = "bukti_booking_".$booking['id_booking']."_".$booking['nama'].".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
?>
