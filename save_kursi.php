<?php
include "config.php";

$total = $_POST['total'];

mysqli_query($koneksi, "INSERT INTO total_kursi(total) VALUES('$total')");

header("Location: admin_dashboard.php");
?>
