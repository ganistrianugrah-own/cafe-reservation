<?php
include "config.php";

$id = $_GET['id_booking'] ?? '';

if($id){
    mysqli_query($koneksi, "DELETE FROM booking WHERE id_booking='$id'");
}

header("Location: admin_dashboard.php");
exit;
?>
