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

    // insert ke tabel penjualan (barang keluar)
    mysqli_query($conn, "INSERT INTO penjualan(total, id_user) VALUES('$subtotal', '$id_user')");
    $id_penjualan = mysqli_insert_id($conn);

    // insert detail penjualan
    mysqli_query($conn, "INSERT INTO detail_penjualan(id_penjualan,id_produk,jumlah,subtotal) 
                         VALUES('$id_penjualan','$id_produk','$jumlah','$subtotal')");

    // update stok
    mysqli_query($conn, "UPDATE produk SET stok=stok-$jumlah WHERE id_produk='$id_produk'");
}
?>
<!DOCTYPE html>
<html>
<head><title>Penjualan</title></head>
<body>
<h2>Transaksi Penjualan</h2>
<form method="POST">
    <select name="id_produk">
        <?php
        $produk = mysqli_query($conn,"SELECT * FROM produk");
        while($p=mysqli_fetch_assoc($produk)){
            echo "<option value='{$p['id_produk']}'>{$p['nama']} - Rp{$p['harga']} (Stok: {$p['stok']})</option>";
        }
        ?>
    </select>
    <input type="number" name="jumlah" placeholder="Jumlah" required>
    <button name="simpan">Simpan</button>
</form>

<hr>
<h3>Riwayat Transaksi</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID Penjualan</th>
        <th>Produk</th>
        <th>Jumlah</th>
        <th>Subtotal</th>
    </tr>
    <?php
    $transaksi = mysqli_query($conn, 
        "SELECT d.id_penjualan, p.nama, d.jumlah, d.subtotal 
         FROM detail_penjualan d 
         JOIN produk p ON d.id_produk=p.id_produk 
         ORDER BY d.id_penjualan DESC");
    while($t = mysqli_fetch_assoc($transaksi)){
        echo "<tr>
                <td>{$t['id_penjualan']}</td>
                <td>{$t['nama']}</td>
                <td>{$t['jumlah']}</td>
                <td>Rp".number_format($t['subtotal'],0,',','.')."</td>
              </tr>";
    }
    ?>
</table>

<a href="dashboard.php">⬅ Kembali</a>
</body>
</html>