
<?php
require "auth.php";
include "../koneksi.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("ID laporan tidak valid.");
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Laporan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }

        .container {
            max-width: 450px;
            background: white;
            margin: 50px auto;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
        }

        button {
            background: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a {
            color: #2563eb;
            text-decoration: none;
            margin-left: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Hapus Laporan</h2>

    <p>
        Yakin ingin menghapus laporan
        <strong>
            <?= htmlspecialchars($data["kode_laporan"]); ?>
        </strong>?
    </p>

    <form method="POST">

        <button type="submit">
            Ya, Hapus
        </button>

        <a href="index.php">
            Batal
        </a>

    </form>

</div>

</body>
</html>
