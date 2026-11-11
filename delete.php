
<?php
include "koneksi.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("ID laporan tidak valid.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        die("ID laporan tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM laporan_bullying WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        die("Gagal menghapus laporan.");
    }
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT kode_laporan FROM laporan_bullying WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Laporan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Laporan - SISBULL</title>

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 480px;
        }

        .card {
            background: #ffffff;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .icon-warning {
            width: 75px;
            height: 75px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin: 0 auto 20px;
        }

        h2 {
            color: #1e3a8a;
            font-size: 23px;
            margin-bottom: 12px;
        }

        .description {
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .report-code {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 9px;
            margin-bottom: 22px;
        }

        .report-code span {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .report-code strong {
            color: #1e3a8a;
            font-size: 17px;
        }

        .warning {
            background: #fff7ed;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            border-radius: 5px;
            color: #92400e;
            font-size: 13px;
            text-align: left;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-cancel:hover {
            background: #cbd5e1;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }

        @media (max-width: 480px) {
            .card {
                padding: 25px 20px;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="card">

            <div class="icon-warning">
                !
            </div>

            <h2>Hapus Laporan</h2>

            <p class="description">
                Apakah kamu yakin ingin menghapus laporan bullying berikut?
                Silakan periksa kembali sebelum melanjutkan.
            </p>

            <div class="report-code">
                <span>Kode Laporan</span>
                <strong>
                    <?= htmlspecialchars($data["kode_laporan"]); ?>
                </strong>
            </div>

            <div class="warning">
                <strong>Perhatian!</strong><br>
                Laporan yang sudah dihapus tidak dapat dikembalikan lagi.
                Pastikan kamu benar-benar ingin menghapus laporan ini.
            </div>

            <form method="POST">

                <input type="hidden" name="id" value="<?= $id; ?>">

                <div class="buttons">

                    <a href="index.php" class="btn btn-cancel">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-delete">
                        Ya, Hapus
                    </button>

                </div>

            </form>

        </div>

    </div>

</body>
</html>
