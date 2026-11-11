```php
<?php
require "auth.php";
include "../koneksi.php";

function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, "UTF-8");
}

// Mengambil ID laporan
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("ID laporan tidak valid.");
}

// Mengambil data laporan
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM laporan_bullying WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Laporan tidak ditemukan.");
}

$error = "";
$berhasil = false;

// Proses update laporan
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status = $_POST["status"] ?? "";
    $tindak_lanjut = trim($_POST["tindak_lanjut"] ?? "");

    $status_valid = [
        "Menunggu",
        "Diproses",
        "Selesai"
    ];

    if (!in_array($status, $status_valid, true)) {

        $error = "Status laporan tidak valid.";

    } else {

        $sql = "UPDATE laporan_bullying
                SET status = ?, tindak_lanjut = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $status,
            $tindak_lanjut,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");
            exit;

        } else {

            $error = "Gagal memperbarui laporan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Laporan - SisBull</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
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
            transition: 0.25s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
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
            margin-left: 255px;
            padding: 30px 35px;
            min-height: 100vh;
        }

        /* HEADER */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            background: white;
            padding: 25px 28px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 3px 15px rgba(15, 23, 42, 0.04);
            border: 1px solid #edf1f7;
        }

        .topbar h2 {
            font-size: 24px;
            color: #172554;
            margin-bottom: 7px;
        }

        .topbar p {
            font-size: 13px;
            color: #64748b;
        }

        .admin-info {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 11px 17px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: bold;
        }

        /* FORM CONTAINER */
        .form-container {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid #edf1f7;
            box-shadow: 0 3px 15px rgba(15, 23, 42, 0.04);
        }

        .form-header {
            border-bottom: 1px solid #edf1f7;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .form-header h3 {
            color: #172554;
            font-size: 20px;
            margin-bottom: 7px;
        }

        .form-header p {
            color: #94a3b8;
            font-size: 12px;
        }

        /* INFORMASI LAPORAN */
        .info-box {
            background: #f1f5ff;
            border: 1px solid #dbeafe;
            padding: 20px;
            border-radius: 11px;
            margin-bottom: 25px;
        }

        .info-title {
            font-size: 13px;
            color: #1e40af;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 13px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item label {
            display: block;
            color: #64748b;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .info-item p {
            font-size: 13px;
            color: #334155;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        /* FORM */
        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 9px;
        }

        select,
        textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            color: #334155;
            background: #fff;
            outline: none;
            transition: 0.2s;
        }

        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.6;
        }

        .help-text {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 7px;
        }

        /* ERROR */
        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 13px 15px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }

        /* BUTTON */
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid #edf1f7;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-save {
            background: #2563eb;
            color: white;
        }

        .btn-save:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 25px 10px;
            font-size: 11px;
            color: #94a3b8;
        }

        /* RESPONSIVE */
        @media (max-width: 700px) {

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 20px;
            }

            .sidebar-desc {
                margin-bottom: 15px;
            }

            .admin-sidebar {
                margin-bottom: 18px;
            }

            .menu-title {
                margin-top: 15px;
            }

            .sidebar a {
                display: inline-flex;
                padding: 10px;
                margin: 3px;
            }

            .sidebar .logout {
                margin-top: 10px;
            }

            .main {
                margin-left: 0;
                padding: 18px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
                padding: 20px;
            }

            .form-container {
                padding: 20px;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .btn {
                text-align: center;
                width: 100%;
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

    <a href="index.php">
        <span class="menu-icon">🏠</span>
        Dashboard
    </a>

    <a href="profil.php">
        <span class="menu-icon">👤</span>
        Profil Admin
    </a>

    <a href="index.php#laporan">
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

    <!-- HEADER -->
    <div class="topbar">

        <div>
            <h2>Edit Laporan</h2>

            <p>
                Perbarui status dan tindak lanjut laporan bullying.
            </p>
        </div>

        <div class="admin-info">
            🛡️ Admin Guru BK
        </div>

    </div>


    <!-- FORM EDIT -->
    <div class="form-container">

        <div class="form-header">

            <h3>Formulir Edit Laporan</h3>

            <p>
                Periksa informasi laporan dan perbarui penanganannya.
            </p>

        </div>


        <!-- INFORMASI LAPORAN -->
        <div class="info-box">

            <div class="info-title">
                📋 Informasi Laporan
            </div>

            <div class="info-item">
                <label>Kode Laporan</label>

                <p>
                    <strong>
                        <?= e($data["kode_laporan"]); ?>
                    </strong>
                </p>
            </div>

            <div class="info-item">
                <label>Jenis Bullying</label>

                <p>
                    <?= e($data["jenis_bullying"]); ?>
                </p>
            </div>

            <div class="info-item">
                <label>Kronologi Kejadian</label>

                <p>
                    <?= nl2br(e($data["kronologi"])); ?>
                </p>
            </div>

        </div>


        <?php if ($error != ""): ?>

            <div class="error">
                ⚠️ <?= e($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <!-- STATUS -->
            <div class="form-group">

                <label for="status">
                    Status Laporan
                </label>

                <select name="status" id="status" required>

                    <option value="Menunggu"
                        <?= $data["status"] == "Menunggu" ? "selected" : ""; ?>>
                        Menunggu
                    </option>

                    <option value="Diproses"
                        <?= $data["status"] == "Diproses" ? "selected" : ""; ?>>
                        Diproses
                    </option>

                    <option value="Selesai"
                        <?= $data["status"] == "Selesai" ? "selected" : ""; ?>>
                        Selesai
                    </option>

                </select>

                <small class="help-text">
                    Pilih status penanganan laporan saat ini.
                </small>

            </div>


            <!-- TINDAK LANJUT -->
            <div class="form-group">

                <label for="tindak_lanjut">
                    Tindak Lanjut Guru BK
                </label>

                <textarea
                    name="tindak_lanjut"
                    id="tindak_lanjut"
                    placeholder="Tuliskan tindakan atau penanganan yang telah dilakukan oleh Guru BK..."
                ><?= e($data["tindak_lanjut"]); ?></textarea>

                <small class="help-text">
                    Tuliskan perkembangan penanganan laporan secara jelas.
                </small>

            </div>


            <!-- BUTTON -->
            <div class="button-group">

                <a href="index.php" class="btn btn-back">
                    ← Kembali
                </a>

                <button type="submit" class="btn btn-save">
                    💾 Simpan Perubahan
                </button>

            </div>

        </form>

    </div>


    <!-- FOOTER -->
    <div class="footer">

        © <?= date("Y"); ?> SisBull - Sistem Pelaporan Bullying

        <br>

        Bersama Ciptakan Lingkungan Sekolah yang Aman.

    </div>

</div>

</body>
</html>
```