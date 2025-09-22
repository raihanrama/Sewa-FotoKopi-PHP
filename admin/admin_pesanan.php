<?php
session_start();
require '../database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='admin_login.php';</script>";
    exit();
}

// Ambil semua pesanan dari database
$query = $conn->query("SELECT p.*, pr.nama as nama_produk FROM pesanan p 
                       LEFT JOIN produk pr ON p.produk_id = pr.id 
                       ORDER BY p.created_at DESC");
$pesanan = $query->fetchAll();

// Ambil data untuk statistik dashboard (sama seperti di dashboard)
$queryProduk = $conn->query("SELECT COUNT(*) as total FROM produk");
$totalProduk = $queryProduk->fetch()['total'];

// Hitung jumlah pesanan
$totalPesanan = count($pesanan);

// Hitung total pendapatan
$queryPendapatan = $conn->query("SELECT SUM(total_harga) as total FROM pesanan WHERE status != 'Dibatalkan'");
$totalPendapatan = $queryPendapatan->fetch()['total'] ?: 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Pesanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary-color: #4776e6;
            --secondary-color: #8e54e9;
            --bg-color: #f5f7fb;
            --text-color: #333;
            --card-bg: #fff;
            --border-radius: 15px;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }
        
        .sidebar-brand {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .sidebar-brand p {
            font-size: 13px;
            opacity: 0.7;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
        }
        
        .admin-avatar i {
            font-size: 24px;
        }
        
        .admin-info h3 {
            font-size: 16px;
            font-weight: 600;
        }
        
        .admin-info p {
            font-size: 12px;
            opacity: 0.7;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-category {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px 5px;
            opacity: 0.7;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: var(--transition);
            text-decoration: none;
            color: white;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }
        
        .menu-item i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            font-size: 18px;
        }
        
        .menu-item span {
            font-size: 15px;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: var(--transition);
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }
        
        .header-title h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .header-title p {
            font-size: 14px;
            color: #777;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
        }
        
        .search-bar {
            position: relative;
            margin-right: 20px;
        }
        
        .search-bar input {
            padding: 10px 15px 10px 40px;
            border-radius: 50px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            width: 250px;
            transition: var(--transition);
        }
        
        .search-bar input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(71, 118, 230, 0.1);
        }
        
        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .notification-btn, .profile-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 10px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        
        .notification-btn:hover, .profile-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background-color: #ff5757;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Dashboard Cards Styles */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }
        
        .stat-card {
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
            font-size: 24px;
            color: white;
        }
        
        .stat-icon.products {
            background: linear-gradient(135deg, #36b9cc, #1a8799);
        }
        
        .stat-icon.transactions {
            background: linear-gradient(135deg, #4e73df, #2e59d9);
        }
        
        .stat-icon.revenue {
            background: linear-gradient(135deg, #1cc88a, #13855c);
        }
        
        .stat-icon.customers {
            background: linear-gradient(135deg, #f6c23e, #dda20a);
        }
        
        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            font-size: 14px;
            color: #777;
        }
        
        /* Table Styles */
        .table-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            margin-bottom: 20px;
            overflow-x: auto;
        }
        
        .table-container h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, 
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .data-table th {
            background-color: #f8f9fc;
            font-weight: 600;
            font-size: 14px;
            color: #555;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fc;
        }
        
        .data-table td {
            font-size: 14px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #ffe8cc;
            color: #ff9500;
        }
        
        .status-waiting {
            background-color: #e2f5ff;
            color: #0095ff;
        }
        
        .status-paid {
            background-color: #d4ffea;
            color: #00c853;
        }
        
        .status-completed {
            background-color: #d4edff;
            color: #007aff;
        }
        
        .status-cancelled {
            background-color: #ffe2e2;
            color: #ff3b30;
        }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .update-btn {
            background-color: var(--primary-color);
            color: white;
        }
        
        .update-btn:hover {
            background-color: var(--secondary-color);
        }
        
        /* Status Selection Dropdown */
        .status-select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            font-size: 13px;
            background-color: white;
            transition: var(--transition);
            margin-right: 5px;
            outline: none;
        }
        
        .status-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(71, 118, 230, 0.1);
        }
        
        /* Responsive Styles */
        @media screen and (max-width: 1200px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media screen and (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .sidebar-brand h2, .sidebar-brand p,
            .admin-info, .menu-category, .menu-item span {
                display: none;
            }
            
            .admin-avatar {
                margin-right: 0;
            }
            
            .admin-profile {
                justify-content: center;
            }
            
            .menu-item {
                justify-content: center;
                padding: 15px;
            }
            
            .menu-item i {
                margin-right: 0;
                font-size: 22px;
            }
            
            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }
        }
        
        @media screen and (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                margin-top: 15px;
            }
            
            .search-bar {
                width: 100%;
                margin-right: 0;
            }
            
            .search-bar input {
                width: 100%;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .data-table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h2>AdminPanel</h2>
            <p>Sistem Manajemen Toko</p>
        </div>
        
        <div class="admin-profile">
            <div class="admin-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="admin-info">
                <h3><?php echo $_SESSION['admin']; ?></h3>
                <p>Administrator</p>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <p class="menu-category">NAVIGASI</p>
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="admin_barang.php" class="menu-item">
                <i class="fas fa-box"></i>
                <span>Manajemen Barang</span>
            </a>
            <a href="admin_pesanan.php" class="menu-item">
                <i class="fas fa-tags"></i>
                <span>Pesanan</span>
            </a>
            <a href="admin_cetak.php" class="menu-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Print</span>
            </a>
            
            <p class="menu-category">LAPORAN</p>
            <a href="admin_laporan_penjualan.php" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <span>Laporan Penjualan</span>
            </a>
            <a href="admin_laporan_stok.php" class="menu-item">
                <i class="fas fa-inventory"></i>
                <span>Laporan Stok</span>
            </a>
            
            <p class="menu-category">PENGATURAN</p>
            <a href="admin_pengguna.php" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>
            <a href="admin_pengaturan.php" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>Manajemen Pesanan</h1>
                <p>Kelola semua pesanan dari pelanggan</p>
            </div>
            
            <div class="header-actions">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Cari pesanan...">
                    <i class="fas fa-search"></i>
                </div>
                
                <div class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="profile-btn">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($totalProduk); ?></h3>
                    <p>Total Produk</p>
                </div>
            </div>
            
            <div class="card stat-card">
                <div class="stat-icon transactions">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($totalPesanan); ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
            
            <div class="card stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
        </div>
        
        <!-- Pesanan Table -->
        <div class="table-container">
            <h2>Daftar Pesanan</h2>
            <table class="data-table" id="pesananTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pembeli</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>No. WhatsApp</th>
                        <th>Alamat</th>
                        <th>Metode Pembayaran</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesanan as $p): ?>
                    <tr>
                        <td>#<?= $p['id']; ?></td>
                        <td><?= htmlspecialchars($p['nama_pembeli']); ?></td>
                        <td><?= htmlspecialchars($p['nama_produk'] ?? 'Produk #'.$p['produk_id']); ?></td>
                        <td><?= $p['jumlah']; ?></td>
                        <td>Rp <?= number_format($p['total_harga'], 0, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($p['no_wa']); ?></td>
                        <td><?= htmlspecialchars($p['alamat']); ?></td>
                        <td><?= htmlspecialchars($p['metode_pembayaran'] ?? '-'); ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['created_at'])); ?></td>
                        <td>
                            <?php
                            $statusClass = '';
                            switch($p['status']) {
                                case 'Pending': $statusClass = 'status-pending'; break;
                                case 'Menunggu Pembayaran': $statusClass = 'status-waiting'; break;
                                case 'Dibayar': $statusClass = 'status-paid'; break;
                                case 'Selesai': $statusClass = 'status-completed'; break;
                                case 'Dibatalkan': $statusClass = 'status-cancelled'; break;
                                default: $statusClass = 'status-pending';
                            }
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($p['status']); ?></span>
                        </td>
                        <td>
                            <form action="admin_update_pesanan.php" method="POST">
                                <input type="hidden" name="pesanan_id" value="<?= $p['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="Pending" <?= $p['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Menunggu Pembayaran" <?= $p['status'] == 'Menunggu Pembayaran' ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                                    <option value="Dibayar" <?= $p['status'] == 'Dibayar' ? 'selected' : ''; ?>>Dibayar</option>
                                    <option value="Selesai" <?= $p['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="Dibatalkan" <?= $p['status'] == 'Dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                                </select>
                                <button type="submit" class="action-btn update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($pesanan) === 0): ?>
                    <tr>
                        <td colspan="11" style="text-align: center;">Tidak ada pesanan yang tersedia</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let table = document.getElementById('pesananTable');
            let rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                let foundMatch = false;
                let cells = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    let cellText = cells[j].textContent.toLowerCase();
                    
                    if (cellText.indexOf(searchValue) > -1) {
                        foundMatch = true;
                        break;
                    }
                }
                
                if (foundMatch) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>