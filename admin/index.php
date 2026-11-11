```php
<?php
require "auth.php";
include "../koneksi.php";

function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, "UTF-8");
}

// Mengambil data laporan
$sql = "SELECT * FROM laporan_bullying ORDER BY created_at DESC";
$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

// Statistik laporan
$laporan = [];
$total = 0;
$menunggu = 0;
$diproses = 0;
$selesai = 0;

while ($data = mysqli_fetch_assoc($query)) {
    $laporan[] = $data;
    $total++;

    $status = strtolower(trim($data["status"]));

    if ($status == "menunggu") {
        $menunggu++;
    } elseif ($status == "diproses") {
        $diproses++;
    } elseif ($status == "selesai") {
        $selesai++;
    }
}

function statusClass($status) {
    $status = strtolower(trim($status));

    if ($status == "selesai") {
        return "selesai";
    } elseif ($status == "diproses") {
        return "diproses";
    }

    return "menunggu";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - SisBull</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
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
            white-space: nowrap;
        }

        /* JUDUL SECTION */
        .section-heading {
            margin-bottom: 17px;
        }

        .section-heading h3 {
            font-size: 17px;
            color: #172554;
            margin-bottom: 5px;
        }

        .section-heading p {
            font-size: 12px;
            color: #94a3b8;
        }

        /* STATISTIK */
        .statistik {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 32px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 14px;
            border: 1px solid #edf1f7;
            box-shadow: 0 3px 15px rgba(15, 23, 42, 0.04);
            transition: 0.25s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 17px;
        }

        .card h4 {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
        }

        .card-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .card h2 {
            font-size: 30px;
            color: #172554;
            margin-bottom: 5px;
        }

        .card small {
            font-size: 11px;
            color: #94a3b8;
        }

        .card.total .card-icon {
            background: #dbeafe;
        }

        .card.menunggu .card-icon {
            background: #fef3c7;
        }

        .card.diproses .card-icon {
            background: #e0f2fe;
        }

        .card.selesai .card-icon {
            background: #dcfce7;
        }

        /* WARNA ANGKA */
        .card.menunggu h2 {
            color: #d97706;
        }

        .card.diproses h2 {
            color: #0284c7;
        }

        .card.selesai h2 {
            color: #16a34a;
        }

        /* CONTENT TABEL */
        .content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #edf1f7;
            box-shadow: 0 3px 15px rgba(15, 23, 42, 0.04);
            scroll-margin-top: 25px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .content-header h3 {
            font-size: 17px;
            color: #172554;
            margin-bottom: 5px;
        }

        .content-header p {
            font-size: 12px;
            color: #94a3b8;
        }

        .tombol {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 11px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            transition: 0.2s;
        }

        .tombol:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        /* TABEL */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #edf1f7;
            border-radius: 10px;
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        th {
            background: #f1f5ff;
            color: #1e40af;
            padding: 15px 13px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 15px 13px;
            font-size: 12px;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        tbody tr {
            transition: 0.2s;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* STATUS */
        .badge {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
        }

        .badge.menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge.selesai {
            background: #dcfce7;
            color: #166534;
        }

        /* AKSI */
        .aksi {
            display: flex;
            gap: 7px;
            align-items: center;
        }

        .edit, .hapus {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 11px;
            font-weight: bold;
            transition: 0.2s;
        }

        .edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .edit:hover {
            background: #2563eb;
            color: white;
        }

        .hapus {
            background: #fee2e2;
            color: #dc2626;
        }

        .hapus:hover {
            background: #dc2626;
            color: white;
        }

        .kosong {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 25px 10px;
            font-size: 11px;
            color: #94a3b8;
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .statistik {
                grid-template-columns: repeat(2, 1fr);
            }

            .main {
                padding: 25px;
            }
        }

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

            .statistik {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .card {
                padding: 16px;
            }

            .card h2 {
                font-size: 25px;
            }

            .card h4 {
                font-size: 11px;
            }

            .content {
                padding: 17px;
            }
        }

        @media (max-width: 400px) {
            .statistik {
                grid-template-columns: 1fr;
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

    <a href="index.php" class="active">
        <span class="menu-icon">🏠</span>
        Dashboard
    </a>

    <a href="profil.php">
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

    <!-- HEADER -->
    <div class="topbar">

        <div>
            <h2>Dashboard Admin</h2>

            <p>
                Selamat datang,
                <?= e($_SESSION["nama_admin"] ?? "Admin"); ?>.
                Pantau dan kelola laporan bullying di sini.
            </p>
        </div>

        <div class="admin-info">
            🛡️ Admin Guru BK
        </div>

    </div>


    <!-- JUDUL STATISTIK -->
    <div class="section-heading">
        <h3>Ringkasan Laporan</h3>
        <p>Informasi jumlah laporan bullying yang masuk.</p>
    </div>


    <!-- STATISTIK -->
    <div class="statistik">

        <div class="card total">
            <div class="card-top">
                <h4>Total Laporan</h4>
                <div class="card-icon">📊</div>
            </div>

            <h2><?= $total; ?></h2>
            <small>Seluruh laporan masuk</small>
        </div>


        <div class="card menunggu">
            <div class="card-top">
                <h4>Menunggu</h4>
                <div class="card-icon">⏳</div>
            </div>

            <h2><?= $menunggu; ?></h2>
            <small>Menunggu penanganan</small>
        </div>


        <div class="card diproses">
            <div class="card-top">
                <h4>Diproses</h4>
                <div class="card-icon">🔄</div>
            </div>

            <h2><?= $diproses; ?></h2>
            <small>Sedang ditindaklanjuti</small>
        </div>


        <div class="card selesai">
            <div class="card-top">
                <h4>Selesai</h4>
                <div class="card-icon">✅</div>
            </div>

            <h2><?= $selesai; ?></h2>
            <small>Laporan telah diselesaikan</small>
        </div>

    </div>


    <!-- DAFTAR LAPORAN -->
    <div class="content" id="laporan">

        <div class="content-header">

            <div>
                <h3>Daftar Laporan Bullying</h3>
                <p>Kelola dan pantau seluruh laporan yang masuk.</p>
            </div>

            <a href="create.php" class="tombol">
                + Tambah Laporan
            </a>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pelapor</th>
                        <th>Kelas</th>
                        <th>Jenis Bullying</th>
                        <th>Korban</th>
                        <th>Pelaku</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Kronologi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($total > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($laporan as $data): ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            <strong><?= e($data["kode_laporan"]); ?></strong>
                        </td>

                        <td>
                            <?= !empty($data["nama_pelapor"])
                                ? e($data["nama_pelapor"])
                                : "Anonim"; ?>
                        </td>

                        <td><?= e($data["kelas"]); ?></td>

                        <td><?= e($data["jenis_bullying"]); ?></td>

                        <td><?= e($data["nama_korban"]); ?></td>

                        <td><?= e($data["nama_pelaku"]); ?></td>

                        <td><?= e($data["tanggal_kejadian"]); ?></td>

                        <td><?= e($data["lokasi"]); ?></td>

                        <td><?= nl2br(e($data["kronologi"])); ?></td>

                        <td>
                            <span class="badge <?= statusClass($data["status"]); ?>">
                                <?= e($data["status"]); ?>
                            </span>
                        </td>

                        <td>
                            <div class="aksi">

                                <a class="edit"
                                   href="edit.php?id=<?= (int)$data["id"]; ?>">
                                    Edit
                                </a>

                                <a class="hapus"
                                   href="delete.php?id=<?= (int)$data["id"]; ?>"
                                   onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                    Hapus
                                </a>

                            </div>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="12" class="kosong">
                            📭 Belum ada laporan bullying.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

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