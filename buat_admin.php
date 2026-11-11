
<?php
include "koneksi.php";

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $nama_admin = trim($_POST["nama_admin"]);
    $password = $_POST["password"];

    if (
        $username == "" ||
        $nama_admin == "" ||
        $password == ""
    ) {
        $pesan = "Semua data harus diisi!";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO admin
                (username, password, nama_admin)
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $username,
            $hash,
            $nama_admin
        );

        if (mysqli_stmt_execute($stmt)) {
            $pesan = "Akun admin berhasil dibuat!";
        } else {
            $pesan = "Gagal membuat akun. Username mungkin sudah digunakan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Akun Admin</title>
</head>
<body>

<h2>Buat Akun Guru BK</h2>

<p><?= htmlspecialchars($pesan); ?></p>

<form method="POST">

    <label>Nama Admin</label><br>
    <input type="text" name="nama_admin" required>
    <br><br>

    <label>Username</label><br>
    <input type="text" name="username" required>
    <br><br>

    <label>Password</label><br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Buat Akun</button>

</form>

</body>
</html>
