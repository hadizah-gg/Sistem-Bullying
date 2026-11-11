
<?php
require "auth.php";
include "../koneksi.php";

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
        $error = "Mohon lengkapi data wajib dengan benar.";
    } else {

        $kode = "BL-" . strtoupper(bin2hex(random_bytes(5)));

        $sql = "INSERT INTO laporan_bullying (
                    kode_laporan,
                    nama_pelapor,
                    kelas,
                    jenis_bullying,
                    nama_korban,
                    nama_pelaku,
                    tanggal_kejadian,
                    lokasi,
                    kronologi,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')";

        $stmt = mysqli_prepare($koneksi, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "sssssssss",
                $kode,
                $nama_pelapor,
                $kelas,
                $jenis,
                $korban,
                $pelaku,
                $tanggal,
                $lokasi,
                $kronologi
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal menambahkan laporan.";
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Terjadi kesalahan pada database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Laporan | SisBull Admin</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
            padding: 35px 20px;
        }

        .container {
            max-width: 850px;
            margin: auto;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-size: 25px;
            color: #172554;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #64748b;
            font-size: 14px;
        }

        .form-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .optional {
            color: #94a3b8;
            font-size: 11px;
            font-weight: normal;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            background: #ffffff;
            color: #334155;
            transition: 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 13px 15px;
            border-radius: 7px;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 13px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
            padding-top: 22px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 11px 20px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
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

        @media (max-width: 600px) {
            body {
                padding: 20px 12px;
            }

            .form-card {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <h2>Tambah Laporan Bullying</h2>
        <p>Tambahkan data laporan bullying ke dalam sistem SisBull.</p>
    </div>

    <div class="form-card">

        <div class="section-title">
            Formulir Data Laporan
        </div>

        <div class="info-box">
            <strong>Informasi:</strong>
            Silakan isi formulir laporan dengan benar.
            Data yang memiliki tanda (*) wajib diisi.
        </div>

        <?php if ($error != "") { ?>
            <div class="error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">
                    <label>
                        Nama Pelapor
                        <span class="optional">(Opsional)</span>
                    </label>

                    <input
                        type="text"
                        name="nama_pelapor"
                        placeholder="Masukkan nama pelapor"
                        value="<?= htmlspecialchars($_POST['nama_pelapor'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label>
                        Kelas
                        <span class="optional">(Opsional)</span>
                    </label>

                    <input
                        type="text"
                        name="kelas"
                        placeholder="Contoh: XII RPL 1"
                        value="<?= htmlspecialchars($_POST['kelas'] ?? '') ?>"
                    >
                </div>

                <div class="form-group full">
                    <label>Jenis Bullying *</label>

                    <select name="jenis_bullying" required>
                        <option value="">-- Pilih Jenis Bullying --</option>

                        <?php
                        $jenis_list = [
                            "Verbal",
                            "Fisik",
                            "Cyberbullying",
                            "Lainnya"
                        ];

                        foreach ($jenis_list as $item) {
                            $selected = (($_POST['jenis_bullying'] ?? '') == $item)
                                ? 'selected'
                                : '';

                            echo "<option value='" . htmlspecialchars($item) . "' $selected>"
                                . htmlspecialchars($item) .
                                "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Korban</label>

                    <input
                        type="text"
                        name="nama_korban"
                        placeholder="Masukkan nama korban"
                        value="<?= htmlspecialchars($_POST['nama_korban'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Nama Pelaku</label>

                    <input
                        type="text"
                        name="nama_pelaku"
                        placeholder="Masukkan nama pelaku"
                        value="<?= htmlspecialchars($_POST['nama_pelaku'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Tanggal Kejadian *</label>

                    <input
                        type="date"
                        name="tanggal_kejadian"
                        value="<?= htmlspecialchars($_POST['tanggal_kejadian'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Lokasi Kejadian *</label>

                    <input
                        type="text"
                        name="lokasi"
                        placeholder="Contoh: Ruang kelas"
                        value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group full">
                    <label>Kronologi Kejadian *</label>

                    <textarea
                        name="kronologi"
                        placeholder="Jelaskan kronologi kejadian bullying secara lengkap..."
                        required
                    ><?= htmlspecialchars($_POST['kronologi'] ?? '') ?></textarea>
                </div>

            </div>

            <div class="button-group">

                <a href="index.php" class="btn btn-back">
                    Kembali
                </a>

                <button type="submit" class="btn btn-save">
                    Simpan Laporan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>
```