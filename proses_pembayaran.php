<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesanan_id = $_POST['pesanan_id'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Update status pesanan
    $query = $conn->prepare("UPDATE pesanan SET status = 'Menunggu Pembayaran', metode_pembayaran = ? WHERE id = ?");
    $query->execute([$metode_pembayaran, $pesanan_id]);

    echo "<script>alert('Pembayaran berhasil! Mohon tunggu konfirmasi admin.'); window.location.href='index.php';</script>";
    exit();
}
?>
