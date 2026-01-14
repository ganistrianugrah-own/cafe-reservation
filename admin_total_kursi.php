<?php include "config.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Total Kursi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">

<div class="page-title">Update Total Kursi Café</div>

<?php
$q = mysqli_query($koneksi, "SELECT total FROM total_kursi ORDER BY id DESC LIMIT 1");
$d = mysqli_fetch_assoc($q);
$total = $d ? $d['total'] : 0;
?>

<div class="card card-custom p-4">

    <form action="save_kursi.php" method="POST" class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Total Kursi</label>
            <input type="number" name="total" class="form-control form-control-lg" required value="<?= $total ?>">
        </div>

        <div class="col-md-3 align-self-end">
            <button class="btn btn-success btn-lg w-100">Simpan</button>
        </div>

    </form>

</div>

</div>

</body>
</html>
