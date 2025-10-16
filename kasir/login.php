<?php
session_start();
include "koneksi.php";

$msg = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id_user, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $msg = "❌ Username/Password salah";
        }
    } else {
        $msg = "❌ Username/Password salah";
    }
    $stmt->close();
}
?>
<!-- HTML form login tetap sama, tambahkan link ke registrasi -->
<!DOCTYPE html><html><body>
<h2>Login</h2>
<?php if ($msg) echo "<div style='color:red;'>$msg</div>"; ?>
<form method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button name="login">Login</button>
</form>
<p>Belum punya akun? <?php
// jika DB kosong, tampilkan link registrasi, kalau tidak kosong tetap tampil link tapi registrasi.php akan menolak kecuali admin
$cek = $conn->query("SELECT COUNT(*) AS jumlah FROM users");
if (($cek->fetch_assoc()['jumlah'] ?? 0) == 0) {
    echo '<a href="registrasi.php">Buat akun admin</a>';
} else {
    echo '<a href="registrasi.php">Registrasi</a>';
}
?></p>
</body></html>
