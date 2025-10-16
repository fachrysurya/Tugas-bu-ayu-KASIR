<?php
session_start();

// Cek login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Kasir</title>
    <style>
        body { font-family: Arial, sans-serif; }
        header { margin-bottom: 20px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        .role-info { font-size: 14px; color: #555; }
    </style>
</head>
<body>
<header>
    <h1>💻 Aplikasi Kasir</h1>
    <p class="role-info">Login sebagai: <b><?= $_SESSION['role'] ?></b></p>
</header>

<div class="menu">
    <a href="produk.php">📦 Produk</a>
    <a href="pelanggan.php">👥 Pelanggan</a>
    <a href="transaksi_penjualan.php">📦 Transaksi penjualan</a>
    <a href="pembelian.php">🛒 transaksi pembelian</a>
    <a href="riwayat.php">📜 Generate laporan</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="manage_users.php">👥 Manage Users</a>
        <a href="registrasi.php">📝 Registrasi</a>
    <?php endif; ?>

    <a href="logout.php">🔑 Logout</a>
</div>

<footer>
    <p>© <?= date("Y") ?> Aplikasi Kasir</p>
</footer>
</body>
</html>