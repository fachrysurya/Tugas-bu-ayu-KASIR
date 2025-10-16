<?php
include "koneksi.php";

if (!isset($_GET['id_penjualan'])) {
    die("ID penjualan tidak ditemukan!");
}

$id_penjualan = $_GET['id_penjualan'];

// Ambil data penjualan
$penjualan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM penjualan WHERE id_penjualan = '$id_penjualan'
"));

// Ambil detail produk yang dibeli
$detail = mysqli_query($conn, "
    SELECT produk.nama AS nama_produk, detail_penjualan.jumlah, detail_penjualan.subtotal, produk.harga
    FROM detail_penjualan
    JOIN produk ON detail_penjualan.id_produk = produk.id_produk
    WHERE detail_penjualan.id_penjualan = '$id_penjualan'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 270px;
            margin: 0 auto;
            text-align: center;
        }
        h2 {
            margin-bottom: 5px;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
            font-size: 13px;
        }
        td, th {
            padding: 2px 0;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            margin-top: 10px;
            font-size: 12px;
            text-align: center;
        }
        @media print {
            button, a { 
                display: none; /* tombol & link tidak ikut ke print */
            }
        }
    </style>
</head>
<body>
    <h2>TOKO CN MANTAP</h2>
    <small>Jl. Tanah Baru No.123, Depok</small>
    <div class="line"></div>

    <table>
        <tr><td>No. Transaksi</td><td class="right">#<?= $penjualan['id_penjualan'] ?></td></tr>
        <tr><td>Tanggal</td><td class="right"><?= $penjualan['tanggal'] ?></td></tr>
        <tr><td>Pelanggan</td><td class="right"><?= htmlspecialchars($penjualan['nama_pelanggan']) ?></td></tr>
        <tr><td>Kasir</td><td class="right"><?= htmlspecialchars($penjualan['nama_kasir']) ?></td></tr>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <th>Produk</th>
            <th class="right">Jml</th>
            <th class="right">Subtotal</th>
        </tr>
        <?php
        $total = 0;
        while ($row = mysqli_fetch_assoc($detail)) {
            echo "<tr>
                    <td>{$row['nama_produk']}</td>
                    <td class='right'>{$row['jumlah']}</td>
                    <td class='right'>Rp" . number_format($row['subtotal'], 0, ',', '.') . "</td>
                  </tr>";
            $total += $row['subtotal'];
        }
        ?>
        <tr class="line"><td colspan="3"></td></tr>
        <tr class="total">
            <td colspan="2">TOTAL</td>
            <td class="right">Rp<?= number_format($total, 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="line"></div>
    <p class="footer">Terima kasih telah berbelanja!<br>Barang yang sudah dibeli tidak dapat dikembalikan.</p>

    <button onclick="window.print()">🖨 Cetak Struk</button>
    <br>
    <a href="transaksi_penjualan.php">← Kembali ke Transaksi</a>
</body>
</html>