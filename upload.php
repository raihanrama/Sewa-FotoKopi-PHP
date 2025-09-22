<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $no_wa = $_POST['no_wa'];
    
    // Cek apakah file diunggah
    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES['dokumen']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validasi jenis file
        if ($file_type != "pdf" && $file_type != "doc" && $file_type != "docx") {
            echo "<script>alert('Hanya file PDF, DOC, dan DOCX yang diperbolehkan!');</script>";
        } else {
            if (move_uploaded_file($_FILES['dokumen']['tmp_name'], $target_file)) {
                // Simpan ke database
                $query = $conn->prepare("INSERT INTO dokumen (nama_pembeli, no_wa, file_path, status) VALUES (?, ?, ?, 'Menunggu')");
                $query->execute([$nama, $no_wa, $file_name]);
                echo "<script>alert('Dokumen berhasil diunggah!'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Gagal mengunggah dokumen!');</script>";
            }
        }
    } else {
        echo "<script>alert('Harap pilih dokumen untuk diunggah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Unggah Dokumen</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Unggah Dokumen untuk Dicetak</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Nama:</label>
        <input type="text" name="nama" required>
        <label>No. WhatsApp:</label>
        <input type="text" name="no_wa" required>
        <label>Upload Dokumen (PDF/DOC/DOCX):</label>
        <input type="file" name="dokumen" required>
        <button type="submit">Unggah</button>
    </form>
</body>
</html>
