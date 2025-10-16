<?php include "koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>📊 Laporan Keluar Masuk Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #333;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        h2 {
            color: #444;
            margin-top: 30px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        a {
            text-decoration: none;
            color: purple;
        }
        a:hover {
            text-decoration: underline;
        }

        /* Tombol print */
        .print-btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .print-btn:hover {
            background: #45a049;
        }

        /* ==== CSS UNTUK CETAK ==== */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                color: black;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: avoid; /* Biar tabel tidak kepecah antar halaman */
            }

            th, td {
                border: 1px solid #000;
                padding: 6px;
            }

            h1, h2 {
                page-break-after: avoid; /* Biar judul tidak terpisah dari tabel */
            }

            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <h1>📦 Laporan Keluar Masuk Barang</h1>

    <!-- Tombol Print dan Kembali -->
    <div class="no-print">
        <a href="#" class="print-btn" onclick="window.print()">🖨 Cetak Laporan</a>
        <a href="dashboard.php" class="print-btn" style="background:#6a1b9a;">⬅ Kembali ke Dashboard</a>
    </div>

    <!-- Barang Keluar -->
    <h2>📤 Barang Keluar (Produk Terjual)</h2>
    <table>
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Nama Produk</th>
            <th>Jumlah Keluar</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
            <th>Petugas</th>
        </tr>
        <?php
        $sql_keluar = "SELECT p.id_penjualan, p.tanggal, pr.nama, dp.jumlah, pr.harga, dp.subtotal, u.username 
                       FROM detail_penjualan dp
                       JOIN penjualan p ON dp.id_penjualan = p.id_penjualan
                       JOIN produk pr ON dp.id_produk = pr.id_produk
                       JOIN users u ON p.id_user = u.id_user
                       ORDER BY p.tanggal DESC";
        $data_keluar = mysqli_query($conn, $sql_keluar);
        while($d = mysqli_fetch_assoc($data_keluar)){
            echo "<tr>
                    <td>{$d['id_penjualan']}</td>
                    <td>{$d['tanggal']}</td>
                    <td>{$d['nama']}</td>
                    <td>{$d['jumlah']}</td>
                    <td>Rp".number_format($d['harga'],0,',','.')."</td>
                    <td>Rp".number_format($d['subtotal'],0,',','.')."</td>
                    <td>{$d['username']}</td>
                </tr>";
        }
        ?>
    </table>

    <!-- Barang Masuk -->
    <h2>📥 Barang Masuk (Produk Dibeli)</h2>
    <table>
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Nama Produk</th>
            <th>Jumlah Masuk</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
            <th>Petugas</th>
        </tr>
        <?php
        $sql_masuk = "SELECT pb.id_pembelian, pb.tanggal, pr.nama, dp.jumlah, pr.harga, dp.subtotal, u.username 
                      FROM detail_pembelian dp
                      JOIN pembelian pb ON dp.id_pembelian = pb.id_pembelian
                      JOIN produk pr ON dp.id_produk = pr.id_produk
                      JOIN users u ON pb.id_user = u.id_user
                      ORDER BY pb.tanggal DESC";
        $data_masuk = mysqli_query($conn, $sql_masuk);
        while($d = mysqli_fetch_assoc($data_masuk)){
            echo "<tr>
                    <td>{$d['id_pembelian']}</td>
                    <td>{$d['tanggal']}</td>
                    <td>{$d['nama']}</td>
                    <td>{$d['jumlah']}</td>
                    <td>Rp".number_format($d['harga'],0,',','.')."</td>
                    <td>Rp".number_format($d['subtotal'],0,',','.')."</td>
                    <td>{$d['username']}</td>
                </tr>";
        }
        ?>
    </table>

    <br>
    <div class="no-print">
        <a href="dashboard.php" class="print-btn" style="background:#6a1b9a;">⬅ Kembali ke Dashboard</a>
    </div>

    <!-- Opsional: auto print setelah halaman selesai dimuat -->
    <script>
    window.onload = function() {
        // Delay sedikit agar tabel selesai dirender dulu
        setTimeout(() => {
            // Uncomment baris di bawah ini kalau mau otomatis print
            // window.print();
        }, 500);
    };
    </script>

</body>
</html>