
<?php
include "koneksi.php";

function e($data) {
    return htmlspecialchars(
        (string)$data,
        ENT_QUOTES,
        "UTF-8"
    );
}

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM laporan_bullying ORDER BY created_at DESC"
);

if (!$query) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Pelaporan Bullying</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #334155;
            min-height: 100vh;
        }

        /* HEADER */
        header {
            background: linear-gradient(120deg, #172554, #1e3a8a);
            color: white;
            padding: 35px 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        header p {
            font-size: 14px;
            color: #dbeafe;
            line-height: 1.6;
        }

        /* CONTAINER */
        .container {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .container h2 {
            color: #1e3a8a;
            font-size: 23px;
            margin-bottom: 20px;
        }

        /* TOMBOL BUAT LAPORAN */
        .tombol {
            display: inline-block;
            background: #172554;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.15);
        }

        .tombol:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        /* TABEL CONTAINER */
        .tabel-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 15px 13px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        td {
            color: #475569;
        }

        tbody tr {
            transition: 0.2s;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* STATUS */
        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            background: #fef3c7;
            color: #92400e;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* AKSI */
        .aksi {
            white-space: nowrap;
        }

        .aksi a {
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            margin-right: 5px;
            padding: 7px 11px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .edit {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .edit:hover {
            background: #bfdbfe;
        }

        .hapus {
            color: #b91c1c;
            background: #fee2e2;
        }

        .hapus:hover {
            background: #fecaca;
        }

        /* FOOTER */
        footer {
            text-align: center;
            padding: 25px 15px;
            margin-top: 35px;
            color: #64748b;
            font-size: 13px;
            border-top: 1px solid #e2e8f0;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            header {
                padding: 28px 15px;
            }

            header h1 {
                font-size: 23px;
            }

            .container {
                margin: 25px auto;
                padding: 0 15px;
            }

            .container h2 {
                font-size: 20px;
            }

            .tabel-container {
                padding: 12px;
                border-radius: 10px;
            }

            th, td {
                padding: 12px;
            }

            footer {
                margin-top: 25px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Sistem Pelaporan Bullying</h1>
    <p>Wujudkan sekolah yang aman, nyaman, dan bebas bullying.</p>
</header>

<div class="container">

    <h2>Daftar Laporan Bullying</h2>

    <a href="create.php" class="tombol">
        + Buat Laporan
    </a>

    <div class="tabel-container">

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Laporan</th>
                    <th>Jenis Bullying</th>
                    <th>Tanggal Kejadian</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $no = 1;

            if (mysqli_num_rows($query) > 0) {
                while ($data = mysqli_fetch_assoc($query)) {
            ?>

                <tr>
                    <td><?= $no++; ?></td>

                    <td><?= e($data['kode_laporan']); ?></td>

                    <td><?= e($data['jenis_bullying']); ?></td>

                    <td><?= e($data['tanggal_kejadian']); ?></td>

                    <td><?= e($data['lokasi']); ?></td>

                    <td>
                        <span class="status">
                            <?= e($data['status']); ?>
                        </span>
                    </td>

                    <td class="aksi">
                        <a class="edit"
                           href="edit.php?id=<?= $data['id']; ?>">
                            Edit
                        </a>

                        <a class="hapus"
                           href="delete.php?id=<?= $data['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>

            <?php
                }
            } else {
            ?>

                <tr>
                    <td colspan="7" style="text-align:center;">
                        Belum ada laporan bullying.
                    </td>
                </tr>

            <?php } ?>

            </tbody>
        </table>

    </div>
</div>

<footer>
    Sistem Pelaporan Bullying Anonim
</footer>

</body>
</html>
