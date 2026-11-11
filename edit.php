
<?php
include "koneksi.php";

function e($data) {
    return htmlspecialchars(
        (string)$data,
        ENT_QUOTES,
        "UTF-8"
    );
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("ID laporan tidak valid.");
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama_pelapor = trim($_POST["nama_pelapor"] ?? "");
    $kelas = trim($_POST["kelas"] ?? "");
    $jenis = $_POST["jenis_bullying"] ?? "";
    $korban = trim($_POST["nama_korban"] ?? "");
    $pelaku = trim($_POST["nama_pelaku"] ?? "");
    $tanggal = $_POST["tanggal_kejadian"] ?? "";
    $lokasi = trim($_POST["lokasi"] ?? "");
    $kronologi = trim($_POST["kronologi"] ?? "");

    $jenis_valid = [
        "Verbal",
        "Fisik",
        "Cyberbullying",
        "Lainnya"
    ];

    if (
        !in_array($jenis, $jenis_valid, true) ||
        $tanggal == "" ||
        $lokasi == "" ||
        $kronologi == ""
    ) {
        $error = "Mohon lengkapi data laporan dengan benar.";
    } else {

        $sql = "UPDATE laporan_bullying SET
                nama_pelapor = ?,
                kelas = ?,
                jenis_bullying = ?,
                nama_korban = ?,
                nama_pelaku = ?,
                tanggal_kejadian = ?,
                lokasi = ?,
                kronologi = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssi",
            $nama_pelapor,
            $kelas,
            $jenis,
            $korban,
            $pelaku,
            $tanggal,
            $lokasi,
            $kronologi,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal memperbarui laporan.";
        }
    }

    // Menampilkan kembali data yang sudah diinput
    $data["nama_pelapor"] = $nama_pelapor;
    $data["kelas"] = $kelas;
    $data["jenis_bullying"] = $jenis;
    $data["nama_korban"] = $korban;
    $data["nama_pelaku"] = $pelaku;
    $data["tanggal_kejadian"] = $tanggal;
    $data["lokasi"] = $lokasi;
    $data["kronologi"] = $kronologi;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Laporan - SISBULL</title>

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
            padding: 30px 20px;
        }

        .container {
            max-width: 750px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .header {
            margin-bottom: 25px;
        }

        .header h2 {
            color: #1e3a8a;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .kode {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .kode span {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .kode strong {
            color: #1e3a8a;
            font-size: 16px;
        }

        .section-title {
            font-size: 16px;
            color: #1e3a8a;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            color: #334155;
            outline: none;
            transition: 0.2s;
            background: white;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
            line-height: 1.6;
        }

        .info {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 13px;
            border-radius: 5px;
            color: #1e40af;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .error {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 12px;
            border-radius: 6px;
            color: #b91c1c;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 13px;
            border: none;
            border-radius: 7px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        .btn-save {
            background: #2563eb;
            color: white;
        }

        .btn-save:hover {
            background: #1d4ed8;
        }

        @media (max-width: 550px) {
            .card {
                padding: 22px 18px;
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

        <div class="header">
            <h2>Edit Laporan Bullying</h2>

            <p>
                Perbarui informasi laporan bullying yang
                sebelumnya telah kamu kirimkan.
            </p>
        </div>

        <div class="kode">
            <span>Kode Laporan</span>
            <strong><?= e($data["kode_laporan"]); ?></strong>
        </div>

        <div class="info">
            <strong>Informasi:</strong>
            Pastikan informasi yang kamu masukkan benar
            agar laporan dapat ditangani dengan baik.
        </div>

        <?php if ($error != "") { ?>
            <div class="error">
                <?= e($error); ?>
            </div>
        <?php } ?>

        <h3 class="section-title">Informasi Pelapor</h3>

        <form method="POST">

            <div class="form-group">
                <label>Nama Pelapor</label>
                <input
                    type="text"
                    name="nama_pelapor"
                    value="<?= e($data["nama_pelapor"]); ?>"
                >
            </div>

            <div class="form-group">
                <label>Kelas</label>
                <input
                    type="text"
                    name="kelas"
                    value="<?= e($data["kelas"]); ?>"
                >
            </div>

            <h3 class="section-title">Detail Kejadian</h3>

            <div class="form-group">
                <label>Jenis Bullying</label>

                <select name="jenis_bullying" required>
                    <option value="">-- Pilih Jenis Bullying --</option>

                    <?php
                    $jenis_list = [
                        "Verbal",
                        "Fisik",
                        "Cyberbullying",
                        "Lainnya"
                    ];

                    foreach ($jenis_list as $j) {
                    ?>
                        <option value="<?= e($j); ?>"
                            <?= $data["jenis_bullying"] == $j
                            ? "selected" : ""; ?>>
                            <?= e($j); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Korban</label>
                <input
                    type="text"
                    name="nama_korban"
                    value="<?= e($data["nama_korban"]); ?>"
                >
            </div>

            <div class="form-group">
                <label>Nama Pelaku</label>
                <input
                    type="text"
                    name="nama_pelaku"
                    value="<?= e($data["nama_pelaku"]); ?>"
                >
            </div>

            <div class="form-group">
                <label>Tanggal Kejadian</label>
                <input
                    type="date"
                    name="tanggal_kejadian"
                    value="<?= e($data["tanggal_kejadian"]); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Lokasi Kejadian</label>
                <input
                    type="text"
                    name="lokasi"
                    value="<?= e($data["lokasi"]); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Kronologi Kejadian</label>
                <textarea
                    name="kronologi"
                    placeholder="Ceritakan kronologi kejadian..."
                    required><?= e($data["kronologi"]); ?></textarea>
            </div>

            <div class="buttons">

                <a href="index.php" class="btn btn-back">
                    Kembali
                </a>

                <button type="submit" class="btn btn-save">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>
