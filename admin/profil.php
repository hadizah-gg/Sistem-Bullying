
<?php
require "auth.php";
include "../koneksi.php";

$id_admin = $_SESSION["admin_id"];
$pesan = "";
$error = "";

// Fungsi untuk mengamankan output
function e($data) {
    return htmlspecialchars($data ?? "", ENT_QUOTES, "UTF-8");
}

// Mengambil data admin
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM admin WHERE id_admin = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id_admin);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("Data admin tidak ditemukan.");
}

// Proses update profil
if (isset($_POST["simpan"])) {

    $nama = trim($_POST["nama_admin"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $konfirmasi = $_POST["konfirmasi_password"];

    if ($nama == "" || $username == "") {
        $error = "Nama dan username wajib diisi.";
    } elseif ($password != $konfirmasi) {
        $error = "Konfirmasi password tidak cocok.";
    } else {

        // Cek username agar tidak sama dengan admin lain
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id_admin FROM admin
             WHERE username = ? AND id_admin != ?"
        );

        mysqli_stmt_bind_param($cek, "si", $username, $id_admin);
        mysqli_stmt_execute($cek);

        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {
            $error = "Username sudah digunakan admin lain.";
        } else {

            if ($password != "") {

                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE admin
                     SET nama_admin = ?, username = ?, password = ?
                     WHERE id_admin = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "sssi",
                    $nama,
                    $username,
                    $password_hash,
                    $id_admin
                );

            } else {

                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE admin
                     SET nama_admin = ?, username = ?
                     WHERE id_admin = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "ssi",
                    $nama,
                    $username,
                    $id_admin
                );
            }

            if (mysqli_stmt_execute($update)) {

                $_SESSION["nama_admin"] = $nama;

                $pesan = "Profil berhasil diperbarui!";

                $admin["nama_admin"] = $nama;
                $admin["username"] = $username;

            } else {
                $error = "Gagal memperbarui profil.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil Admin - SisBull</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        /* SIDEBAR */
                .sidebar {
            width: 255px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #172554;
            color: white;
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .logo {
            font-size: 29px;
            font-weight: 800;
            letter-spacing: 1px;
            padding-left: 10px;
        }

        .logo span {
            color: #60a5fa;
        }

        .sidebar-desc {
            color: #94a3b8;
            font-size: 12px;
            margin: 5px 0 30px 10px;
        }

        /* PROFIL ADMIN */
        .admin-sidebar {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #243b70;
            padding: 14px;
            border: 1px solid #344d82;
            border-radius: 12px;
            margin-bottom: 32px;
        }

        .admin-icon {
            width: 43px;
            height: 43px;
            border-radius: 12px;
            background: #dbeafe;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .admin-sidebar strong {
            display: block;
            font-size: 13px;
            color: white;
        }

        .admin-sidebar small {
            display: block;
            color: #93c5fd;
            font-size: 11px;
            margin-top: 5px;
        }

        .menu-title {
            color: #647da9;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1.5px;
            margin: 0 0 12px 12px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 14px;
            margin-bottom: 7px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 500;
            transition: 0.25s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
            transform: translateX(3px);
        }

        .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .sidebar .logout {
            margin-top: auto;
            background: #7f1d1d;
            color: #fecaca;
        }

        .sidebar .logout:hover {
            background: #dc2626;
            color: white;
        }
        /* MAIN */
        .main {
            margin-left: 250px;
            padding: 35px;
        }

        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 13px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .header h2 {
            font-size: 23px;
            margin-bottom: 7px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
        }

        .breadcrumb {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .breadcrumb span {
            color: #2563eb;
        }

        /* PROFILE CARD */
        .profil-card {
            max-width: 850px;
            background: white;
            padding: 30px;
            border-radius: 13px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .profil-header {
            display: flex;
            align-items: center;
            gap: 18px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 25px;
        }

        .avatar {
            width: 65px;
            height: 65px;
            background: #dbeafe;
            color: #2563eb;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 27px;
            font-weight: bold;
        }

        .profil-header h3 {
            font-size: 19px;
            margin-bottom: 5px;
        }

        .profil-header p {
            font-size: 13px;
            color: #64748b;
        }

        .section-title {
            font-size: 16px;
            margin-bottom: 22px;
        }

        .form-group {
            margin-bottom: 21px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 9px;
            color: #334155;
        }

        input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            transition: 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
        }

        input[readonly] {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
        }

        .keterangan {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 7px;
        }

        /* ALERT */
        .pesan,
        .error {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .pesan {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* BUTTON */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn-kembali {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-kembali:hover {
            background: #cbd5e1;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }

        /* RESPONSIVE */
        @media(max-width: 650px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar p {
                margin-bottom: 15px;
            }

            .sidebar a {
                display: inline-block;
                margin: 3px;
                padding: 10px;
            }

            .main {
                margin-left: 0;
                padding: 15px;
            }

            .header {
                padding: 20px;
            }

            .profil-card {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }

            .button-group .btn {
                text-align: center;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        Sis<span>Bull</span>
    </div>

    <p class="sidebar-desc">Sistem Pelaporan Bullying</p>

    <div class="admin-sidebar">
        <div class="admin-icon">👤</div>

        <div>
            <strong>
                <?= e($_SESSION["nama_admin"] ?? "Admin"); ?>
            </strong>
            <small>Admin Guru BK</small>
        </div>
    </div>

    <div class="menu-title">MENU UTAMA</div>

    <a href="index.php" >
        <span class="menu-icon">🏠</span>
        Dashboard
    </a>

    <a href="profil.php" class="active">
        <span class="menu-icon">👤</span>
        Profil Admin
    </a>

    <a href="#laporan">
        <span class="menu-icon">📋</span>
        Data Laporan
    </a>

    <a href="create.php">
        <span class="menu-icon">➕</span>
        Tambah Laporan
    </a>

    <a href="logout.php" class="logout"
       onclick="return confirm('Yakin ingin logout?')">
        <span class="menu-icon">🚪</span>
        Logout
    </a>

</div>


<!-- MAIN CONTENT -->
<div class="main">

    <div class="header">
        <div class="breadcrumb">
            Dashboard / <span>Profil Admin</span>
        </div>

        <h2>Profil Admin</h2>
        <p>Kelola informasi dan keamanan akun administrator SisBull.</p>
    </div>

    <div class="profil-card">

        <div class="profil-header">

            <div class="avatar">
                <?= strtoupper(substr($admin["nama_admin"], 0, 1)); ?>
            </div>

            <div>
                <h3><?= e($admin["nama_admin"]); ?></h3>
                <p>Administrator SisBull</p>
            </div>

        </div>

        <?php if ($pesan != ""): ?>
            <div class="pesan">
                <?= e($pesan); ?>
            </div>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <div class="error">
                <?= e($error); ?>
            </div>
        <?php endif; ?>

        <h3 class="section-title">Informasi Akun</h3>

        <form method="POST">

            <div class="form-group">
                <label>ID Admin</label>

                <input type="text"
                       value="<?= (int)$admin["id_admin"]; ?>"
                       readonly>
            </div>

            <div class="form-group">
                <label>Nama Admin</label>

                <input type="text"
                       name="nama_admin"
                       value="<?= e($admin["nama_admin"]); ?>"
                       placeholder="Masukkan nama admin"
                       required>
            </div>

            <div class="form-group">
                <label>Username</label>

                <input type="text"
                       name="username"
                       value="<?= e($admin["username"]); ?>"
                       placeholder="Masukkan username"
                       required>
            </div>

            <div class="form-group">
                <label>Password Baru</label>

                <input type="password"
                       name="password"
                       placeholder="Kosongkan jika tidak ingin mengganti password">

                <p class="keterangan">
                    Isi bagian ini hanya jika ingin mengganti password.
                </p>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru</label>

                <input type="password"
                       name="konfirmasi_password"
                       placeholder="Masukkan ulang password baru">
            </div>

            <div class="button-group">

                <button type="submit" name="simpan" class="btn">
                    Simpan Perubahan
                </button>

                <a href="index.php" class="btn btn-kembali">
                    Kembali
                </a>

            </div>

        </form>

    </div>

    <div class="footer">
        © <?= date("Y"); ?> SisBull - Sistem Pelaporan Bullying
    </div>

</div>

</body>
</html>
