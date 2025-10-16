<?php
session_start();
require 'koneksi.php'; // file koneksi database

// Cek apakah user sudah login dan role = admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Tambah user
if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // aman pakai hash
    $role = $_POST['role'];

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    mysqli_query($conn, $query);
    header("Location: manage_users.php");
    exit;
}

// Hapus user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // keamanan biar integer
    mysqli_query($conn, "DELETE FROM users WHERE id_user=$id");
    header("Location: manage_users.php");
    exit;
}

// Ambil semua user
$users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
</head>
<body>
    <h2>👥 Manage Users</h2>
    <a href="dashboard.php">⬅ Kembali</a> | 
    <a href="logout.php">Logout</a>

    <h3>Tambah User</h3>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="kasir">Kasir</option>
        </select>
        <button type="submit" name="add">Tambah</button>
    </form>

    <h3>Daftar User</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><?= $row['id_user'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <a href="manage_users.php?delete=<?= $row['id_user'] ?>" 
                   onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>