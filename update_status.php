<?php
include "config.php";

$id_booking = $_POST['id_booking'];
$status     = $_POST['status'];

mysqli_query($koneksi, "UPDATE booking SET status='$status' WHERE id_booking='$id_booking'");

header("Location: admin_dashboard.php");
exit();
?>
