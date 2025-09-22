<?php
session_start();
require 'database.php';

// Ambil nomor WhatsApp dari session atau form
if (isset($_SESSION['no_wa'])) {
    $no_wa = $_SESSION['no_wa'];
} elseif (isset($_POST['no_wa'])) {
    $no_wa = $_POST['no_wa'];
} else {
    echo "<script>alert('Tidak ada pesanan ditemukan. Silakan lakukan pemesanan terlebih dahulu.'); window.location.href='pembelian.php';</script>";
    exit();
}

// Ambil pesanan terbaru berdasarkan nomor WhatsApp
$query = $conn->prepare("SELECT * FROM pesanan WHERE no_wa = ? ORDER BY created_at DESC LIMIT 1");
$query->execute([$no_wa]);
$pesanan = $query->fetch();

// Jika pesanan ditemukan, ambil detail produk
$queryProduk = $conn->prepare("SELECT produk.nama, produk.harga, pesanan.jumlah FROM pesanan JOIN produk ON pesanan.produk_id = produk.id WHERE pesanan.no_wa = ?");
$queryProduk->execute([$no_wa]);
$items = $queryProduk->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Detail Pesanan</h2>

    <?php if ($pesanan): ?>
        <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama_pembeli']); ?></p>
        <p><strong>No. WhatsApp:</strong> <?= htmlspecialchars($pesanan['no_wa']); ?></p>
        <p><strong>Alamat:</strong> <?= htmlspecialchars($pesanan['alamat']); ?></p>
        
        <h3>Produk yang Dipesan:</h3>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?= $item['jumlah'] . " x " . $item['nama'] . " - Rp" . number_format($item['harga'] * $item['jumlah']); ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Total Harga: Rp<?= number_format($pesanan['total_harga']); ?></h3>

        <h3>Pilih Metode Pembayaran:</h3>
        <form action="proses_pembayaran.php" method="POST">
            <input type="hidden" name="pesanan_id" value="<?= $pesanan['id']; ?>">
            <label>
                <input type="radio" name="metode_pembayaran" value="Transfer Bank" required> Transfer Bank
            </label><br>
            <label>
                <input type="radio" name="metode_pembayaran" value="E-Wallet" required> E-Wallet (GoPay, OVO, Dana)
            </label><br>
            <label>
                <input type="radio" name="metode_pembayaran" value="COD" required> Bayar di Tempat (COD)
            </label><br>
            <button type="submit">Lanjutkan Pembayaran</button>
        </form>
    <?php else: ?>
        <p>Pesanan tidak ditemukan.</p>
    <?php endif; ?>
</body>
</html>
