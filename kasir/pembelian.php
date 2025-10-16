<?php
session_start();
include "koneksi.php";

if (isset($_POST['simpan'])) {
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];

    // ambil data produk
    $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'"));
    $subtotal = $produk['harga'] * $jumlah;

    // ambil id_user dari session
    $id_user = $_SESSION['id_user'];

    // insert ke tabel pembelian (barang masuk)
    mysqli_query($conn, "INSERT INTO pembelian(total, id_user) VALUES('$subtotal', '$id_user')");
    $id_pembelian = mysqli_insert_id($conn);

    // insert detail pembelian
    mysqli_query($conn, "INSERT INTO detail_pembelian(id_pembelian,id_produk,jumlah,subtotal) 
                         VALUES('$id_pembelian','$id_produk','$jumlah','$subtotal')");

    // update stok (tambah stok karena barang masuk)
    mysqli_query($conn, "UPDATE produk SET stok=stok+$jumlah WHERE id_produk='$id_produk'");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>📦 Transaksi Pembelian Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #333;
            padding: 20px;
        }
        h2, h3 {
            color: #444;
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        select, input[type=number] {
            padding: 5px 10px;
            margin-right: 10px;
        }
        button {
            padding: 6px 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
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

        .print-btn {
            display: inline-block;
            background: #2196F3;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 5px;
        }
        .print-btn:hover {
            background: #1976D2;
        }

        /* ==== CSS UNTUK MODE CETAK ==== */
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
                page-break-inside: avoid;
            }

            th, td {
                border: 1px solid #000;
                padding: 6px;
            }

            h2, h3 {
                page-break-after: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <h2>📦 Transaksi Pembelian Barang</h2>

    <div class="no-print">
        <form method="POST">
            <select name="id_produk" required>
                <?php
                $produk = mysqli_query($conn,"SELECT * FROM produk");
                while($p=mysqli_fetch_assoc($produk)){
                    echo "<option value='{$p['id_produk']}'>{$p['nama']} - Rp{$p['harga']} (Stok: {$p['stok']})</option>";
                }
                ?>
            </select>
            <input type="number" name="jumlah" placeholder="Jumlah" required min="1">
            <button name="simpan">Simpan</button>
        </form>

        <div style="text-align:center;">
            <a href="#" class="print-btn" onclick="window.print()">🖨 Cetak Laporan</a>
            <a href="dashboard.php" class="print-btn" style="background:#6a1b9a;">⬅ Kembali</a>
        </div>
    </div>

    <hr>

    <h3>📜 Riwayat Pembelian</h3>
    <table>
        <tr>
            <th>ID Pembelian</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
        <?php
        $transaksi = mysqli_query($conn, 
            "SELECT d.id_pembelian, p.nama, d.jumlah, d.subtotal 
             FROM detail_pembelian d 
             JOIN produk p ON d.id_produk=p.id_produk 
             ORDER BY d.id_pembelian DESC");
        while($t = mysqli_fetch_assoc($transaksi)){
            echo "<tr>
                    <td>{$t['id_pembelian']}</td>
                    <td>{$t['nama']}</td>
                    <td>{$t['jumlah']}</td>
                    <td>Rp".number_format($t['subtotal'],0,',','.')."</td>
                  </tr>";
        }
        ?>
    </table>

    <br>
    <div class="no-print" style="text-align:center;">
        <a href="dashboard.php" class="print-btn" style="background:#6a1b9a;">⬅ Kembali</a>
    </div>

    <!-- Optional auto print delay -->
    <script>
    window.onload = function() {
        // Uncomment baris di bawah ini kalau mau otomatis print saat halaman dibuka
        // setTimeout(() => window.print(), 500);
    };
    </script>

</body>
</html>