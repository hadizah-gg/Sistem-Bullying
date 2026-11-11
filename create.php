
<?php
include "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama_pelapor = trim($_POST["nama_pelapor"] ?? "");
    $kelas = trim($_POST["kelas"] ?? "");
    $jenis_bullying = $_POST["jenis_bullying"] ?? "";
    $nama_korban = trim($_POST["nama_korban"] ?? "");
    $nama_pelaku = trim($_POST["nama_pelaku"] ?? "");
    $tanggal_kejadian = $_POST["tanggal_kejadian"] ?? "";
    $lokasi = trim($_POST["lokasi"] ?? "");
    $kronologi = trim($_POST["kronologi"] ?? "");

    $jenis_valid = [
        "Verbal",
        "Fisik",
        "Cyberbullying",
        "Lainnya"
    ];

    if (
        !in_array($jenis_bullying, $jenis_valid, true) ||
        empty($tanggal_kejadian) ||
        empty($lokasi) ||
        empty($kronologi)
    ) {
        $error = "Mohon lengkapi data laporan dengan benar.";
    } else {

        // Membuat kode laporan unik
        $kode_laporan = "BL-" . strtoupper(
            bin2hex(random_bytes(5))
        );

        $sql = "INSERT INTO laporan_bullying (
                    kode_laporan,
                    nama_pelapor,
                    kelas,
                    jenis_bullying,
                    nama_korban,
                    nama_pelaku,
                    tanggal_kejadian,
                    lokasi,
                    kronologi
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssss",
            $kode_laporan,
            $nama_pelapor,
            $kelas,
            $jenis_bullying,
            $nama_korban,
            $nama_pelaku,
            $tanggal_kejadian,
            $lokasi,
            $kronologi
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Laporan berhasil dikirim. Kode laporan: "
                . $kode_laporan .
                "');
                window.location='index.php';
            </script>";
            exit;
        } else {
            $error = "Laporan gagal dikirim.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan Bullying</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 650px;
            background: white;
            margin: 30px auto;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            color: #1e3a8a;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 7px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 120px;
        }

        button {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
        }

        a {
            color: #2563eb;
            text-decoration: none;
        }

        .error {
            color: #dc2626;
        }

        .info {
            background: #eff6ff;
            padding: 12px;
            border-radius: 5px;
            color: #1e40af;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Form Pelaporan Bullying</h2>

    <p class="info">
        Kamu dapat melaporkan bullying tanpa mencantumkan
        nama asli. Ceritakan kejadian dengan jujur agar
        guru BK dapat membantu.
    </p>

    <?php if ($error != "") { ?>
        <p class="error">
            <?= htmlspecialchars($error); ?>
        </p>
    <?php } ?>

    <form method="POST">

        <label>Nama Pelapor (Opsional)</label>
        <input type="text" name="nama_pelapor"
               placeholder="Kosongkan jika ingin anonim">

        <label>Kelas (Opsional)</label>
        <input type="text" name="kelas"
               placeholder="Contoh: XI RPL 1">

        <label>Jenis Bullying</label>
        <select name="jenis_bullying" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="Verbal">Verbal</option>
            <option value="Fisik">Fisik</option>
            <option value="Cyberbullying">Cyberbullying</option>
            <option value="Lainnya">Lainnya</option>
        </select>

        <label>Nama Korban (Opsional)</label>
        <input type="text" name="nama_korban"
               placeholder="Nama korban jika diketahui">

        <label>Nama Pelaku (Opsional)</label>
        <input type="text" name="nama_pelaku"
               placeholder="Nama pelaku jika diketahui">

        <label>Tanggal Kejadian</label>
        <input type="date" name="tanggal_kejadian" required>

        <label>Lokasi Kejadian</label>
        <input type="text" name="lokasi"
               placeholder="Contoh: Ruang kelas"
               required>

        <label>Kronologi Kejadian</label>
        <textarea name="kronologi"
                  placeholder="Ceritakan kejadian bullying..."
                  required></textarea>

        <button type="submit">
            Kirim Laporan
        </button>

    </form>

    <br>

    <a href="index.php">Kembali ke halaman utama</a>

</div>

</body>
</html>
