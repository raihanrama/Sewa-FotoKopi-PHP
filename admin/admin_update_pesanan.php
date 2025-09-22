<?php
session_start();
require '../database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='admin_login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesanan_id = $_POST['pesanan_id'];
    $status = $_POST['status'];

    // Update status pesanan di database
    $query = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
    $query->execute([$status, $pesanan_id]);

    echo "<script>alert('Status pesanan diperbarui!'); window.location.href='admin_pesanan.php';</script>";
    exit();
}
?>
