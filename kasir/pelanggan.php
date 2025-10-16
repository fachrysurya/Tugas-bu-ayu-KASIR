<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    mysqli_query($conn, "INSERT INTO pelanggan(nama,alamat,telepon) VALUES('$nama','$alamat','$telepon')");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pelanggan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        input {
            padding: 10px;
            margin: 5px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 250px;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
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
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }

        @media print {
            form, .back-btn {
                display: none;
            }
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
    <h2>👥 Data Pelanggan</h2>
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama Pelanggan" required><br>
        <input type="text" name="alamat" placeholder="Alamat" required><br>
        <input type="text" name="telepon" placeholder="Telepon" required><br>
        <button name="simpan">💾 Simpan</button>
    </form>

    <table>
        <tr><th>No</th><th>Nama</th><th>Alamat</th><th>Telepon</th></tr>
        <?php
        $no=1;
        $data = mysqli_query($conn,"SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
        while($d=mysqli_fetch_assoc($data)){
            echo "<tr>
                    <td>$no</td>
                    <td>{$d['nama']}</td>
                    <td>{$d['alamat']}</td>
                    <td>{$d['telepon']}</td>
                  </tr>";
            $no++;
        }
        ?>
    </table>

    <a href="dashboard.php" class="back-btn">⬅ Kembali</a>
</div>
</body>
</html>