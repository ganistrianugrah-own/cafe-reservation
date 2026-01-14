<?php
// index.php - Halaman dashboard utama
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kinasih Cafe & Space - Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: url('assets/cafebg.jpg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        color: #fff;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .dashboard-container {
        background: rgba(0,0,0,0.7);
        padding: 50px 40px;
        border-radius: 15px;
        text-align: center;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.5);
    }

    .dashboard-container h1 {
        margin-bottom: 40px;
        font-size: 28px;
        font-weight: 700;
    }

    .dashboard-container a {
        display: block;
        margin: 15px 0;
        padding: 15px;
        font-size: 18px;
        border-radius: 10px;
        text-decoration: none;
        color: #fff;
        transition: all 0.3s;
    }

    .dashboard-container a.admin {
        background: #007bff;
    }

    .dashboard-container a.admin:hover {
        background: #0056b3;
    }

    .dashboard-container a.laporan {
        background: #28a745;
    }

    .dashboard-container a.laporan:hover {
        background: #1e7e34;
    }
</style>
</head>
<body>

<div class="dashboard-container">
    <h1>🟢 Kinasih Cafe & Space - Dashboard</h1>

    <a href="admin_dashboard.php" class="admin">💻 Admin Dashboard</a>
    <a href="laporan/laporan.php" class="laporan">📄 Laporan Booking</a>
</div>

</body>
</html>
