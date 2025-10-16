<?php
session_start();
include "koneksi.php";

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil nama kasir
$id_user = $_SESSION['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM users WHERE id_user='$id_user'"));
$nama_kasir = $user['username'];

// Pastikan keranjang tersedia
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah produk ke keranjang
if (isset($_POST['tambah'])) {
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];

    if ($jumlah > 0) {
        $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'"));
        if ($produk['stok'] >= $jumlah) {
            $_SESSION['keranjang'][] = [
                'id_produk' => $produk['id_produk'],
                'nama' => $produk['nama'],
                'harga' => $produk['harga'],
                'jumlah' => $jumlah,
                'subtotal' => $produk['harga'] * $jumlah
            ];
        } else {
            echo "<script>alert('Stok tidak cukup!');</script>";
        }
    }
}

// Simpan transaksi penjualan
if (isset($_POST['simpan'])) {
    if (!empty($_SESSION['keranjang'])) {
        $total = 0;
        foreach ($_SESSION['keranjang'] as $item) {
            $total += $item['subtotal'];
        }

        $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);

        mysqli_query($conn, "INSERT INTO penjualan (nama_pelanggan, nama_kasir, total, id_user) 
            VALUES ('$nama_pelanggan', '$nama_kasir', '$total', '$id_user')
        ");
        $id_penjualan = mysqli_insert_id($conn);

        foreach ($_SESSION['keranjang'] as $item) {
            $id_produk = $item['id_produk'];
            $jumlah = $item['jumlah'];
            $subtotal = $item['subtotal'];

            mysqli_query($conn, "INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah, subtotal)
                                 VALUES ('$id_penjualan', '$id_produk', '$jumlah', '$subtotal')");
            mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id_produk = '$id_produk'");
        }

        unset($_SESSION['keranjang']);
        header("Location: struk.php?id_penjualan=" . $id_penjualan);
        exit;
    } else {
        echo "<script>alert('Keranjang masih kosong!');</script>";
    }
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $index = $_GET['hapus'];
    unset($_SESSION['keranjang'][$index]);
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Penjualan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        form {
            margin-bottom: 20px;
        }
        input, select, button {
            padding: 10px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-right: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            text-decoration: none;
            color: #dc3545;
        }
        a:hover {
            text-decoration: underline;
        }
        .total {
            font-weight: bold;
            background-color: #f1f1f1;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
        }
        .back-btn:hover { background-color: #5a6268; }

        @media print {
            .back-btn, button, form { display: none; }
            body {
                background: white;
            }
            table {
                border: 1px solid black;
            }
            th {
                background-color: #ddd !important;
                color: black;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🛒 Transaksi Penjualan</h2>
    <p><b>Kasir:</b> <?= htmlspecialchars($nama_kasir) ?></p>

    <form method="POST">
        <label><b>Nama Pelanggan:</b></label>
        <input type="text" name="nama_pelanggan" placeholder="Masukkan nama pelanggan" required>
        <br><br>
        <select name="id_produk" required>
            <option value="">-- Pilih Produk --</option>
            <?php
            $produk = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0");
            while ($p = mysqli_fetch_assoc($produk)) {
                echo "<option value='{$p['id_produk']}'>{$p['nama']} - Rp{$p['harga']} (Stok: {$p['stok']})</option>";
            }
            ?>
        </select>
        <input type="number" name="jumlah" placeholder="Jumlah" min="1" required>
        <button name="tambah">➕ Tambah ke Keranjang</button>
    </form>

    <h3>🧾 Keranjang Belanja</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $total = 0;
        foreach ($_SESSION['keranjang'] as $index => $item) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$item['nama']}</td>
                    <td>Rp" . number_format($item['harga'], 0, ',', '.') . "</td>
                    <td>{$item['jumlah']}</td>
                    <td>Rp" . number_format($item['subtotal'], 0, ',', '.') . "</td>
                    <td><a href='?hapus={$index}'>❌ Hapus</a></td>
                  </tr>";
            $total += $item['subtotal'];
            $no++;
        }
        if ($total == 0) {
            echo "<tr><td colspan='6'>Keranjang kosong</td></tr>";
        }
        ?>
        <tr class="total">
            <td colspan="4">Total</td>
            <td colspan="2">Rp<?= number_format($total, 0, ',', '.') ?></td>
        </tr>
    </table>

    <form method="POST">
        <input type="hidden" name="nama_pelanggan" value="<?= isset($_POST['nama_pelanggan']) ? htmlspecialchars($_POST['nama_pelanggan']) : '' ?>">
        <button name="simpan">💾 Simpan Transaksi & Cetak</button>
    </form>

    <a href="dashboard.php" class="back-btn">⬅ Kembali</a>
</div>
</body>
</html>