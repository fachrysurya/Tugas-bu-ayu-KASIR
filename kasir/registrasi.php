<?php
session_start();
include "koneksi.php"; // pastikan $conn di sini

// cek jumlah user
$cek = $conn->query("SELECT COUNT(*) AS jumlah FROM users");
$jml = $cek->fetch_assoc()['jumlah'] ?? 0;

// Jika sudah ada user, tutup registrasi publik
if ($jml > 0 && !isset($_SESSION['role'])) {
    die("Registrasi ditutup. Silakan hubungi admin atau login.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role     = $_POST['role'] ?? 'kasir';

    if ($username === '') $errors[] = "Username wajib diisi.";
    if ($password === '') $errors[] = "Password wajib diisi.";
    if ($password !== $confirm) $errors[] = "Password dan konfirmasi tidak cocok.";
    if (!in_array($role, ['admin','kasir'])) $errors[] = "Role tidak valid.";

    if (empty($errors)) {
        // cek username unik
        $stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username sudah dipakai.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hash, $role);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            // setelah buat akun pertama (biasanya admin) redirect ke login
            header("Location: login.php?registered=1");
            exit;
        } else {
            $errors[] = "Gagal menyimpan. Coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Registrasi</title></head>
<body>
<h2>Registrasi Pengguna</h2>
<?php if ($jml == 0): ?>
    <p>Database kosong — buat akun admin pertama.</p>
<?php else: ?>
    <p>Registrasi hanya bisa dilakukan oleh admin yang sudah login.</p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
    </div>
<?php endif; ?>

<form method="post">
    <label>Username</label><br>
    <input type="text" name="username" required><br>
    <label>Password</label><br>
    <input type="password" name="password" required><br>
    <label>Konfirmasi Password</label><br>
    <input type="password" name="confirm" required><br>
    <label>Role</label><br>
    <select name="role">
        <option value="admin">Admin</option>
        <option value="kasir" selected>Kasir</option>
    </select><br><br>
    <button type="submit">Daftar</button>
</form>
</body>
</html>
