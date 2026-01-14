<?php include "config.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Total Kursi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
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
            <button type="button" class="btn btn-primary btn-lg" onclick="openConfirm()">Simpan</button>
        </div>

    </form>

</div>

</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Apakah Anda yakin ingin menyimpan jumlah kursi saat ini?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
        <button type="button" class="btn btn-success" onclick="submitForm()">Ya, Simpan</button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openConfirm() {
    var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    myModal.show();
}

function submitForm() {
    document.querySelector("form").submit();
}
</script>

</body>
</html>
