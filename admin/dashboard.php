<?php
session_start();
require '../database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Ambil data untuk statistik dashboard
// Query untuk mendapatkan total produk
$queryProduk = $conn->query("SELECT COUNT(*) as total FROM produk");
$totalProduk = $queryProduk->fetch()['total'];

// // Query untuk mendapatkan total transaksi
// $queryTransaksi = $conn->query("SELECT COUNT(*) as total FROM transaksi");
// $totalTransaksi = $queryTransaksi->fetch()['total'];

// // Query untuk mendapatkan pendapatan total
// $queryPendapatan = $conn->query("SELECT SUM(total_harga) as total FROM transaksi");
// $totalPendapatan = $queryPendapatan->fetch()['total'] ?: 0;

// // Query untuk data grafik penjualan per bulan (6 bulan terakhir)
// $queryGrafikPenjualan = $conn->query("
//     SELECT 
//         DATE_FORMAT(tanggal_transaksi, '%Y-%m') as bulan,
//         SUM(total_harga) as total
//     FROM transaksi
//     WHERE tanggal_transaksi >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
//     GROUP BY DATE_FORMAT(tanggal_transaksi, '%Y-%m')
//     ORDER BY bulan ASC
// ");
// $dataPenjualan = $queryGrafikPenjualan->fetchAll(PDO::FETCH_ASSOC);

// // Query untuk produk terlaris
// $queryProdukTerlaris = $conn->query("
//     SELECT 
//         b.nama_barang, 
//         SUM(td.jumlah) as total_terjual
//     FROM transaksi_detail td
//     JOIN barang b ON td.id_barang = b.id
//     GROUP BY td.id_barang
//     ORDER BY total_terjual DESC
//     LIMIT 5
// ");
// $produkTerlaris = $queryProdukTerlaris->fetchAll(PDO::FETCH_ASSOC);

// // Query untuk kategori terpopuler
// $queryKategoriPopuler = $conn->query("
//     SELECT 
//         k.nama_kategori, 
//         COUNT(td.id) as total_transaksi
//     FROM transaksi_detail td
//     JOIN barang b ON td.id_barang = b.id
//     JOIN kategori k ON b.id_kategori = k.id
//     GROUP BY b.id_kategori
//     ORDER BY total_transaksi DESC
//     LIMIT 5
// ");
// $kategoriPopuler = $queryKategoriPopuler->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.css">
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
        
        .trend-indicator {
            display: flex;
            align-items: center;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .trend-indicator.positive {
            color: #1cc88a;
        }
        
        .trend-indicator.negative {
            color: #e74a3b;
        }
        
        /* Chart Cards Styles */
        .dashboard-charts {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .chart-card, .list-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-header h2 {
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-header select {
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
        }
        
        .card-header select:focus {
            border-color: var(--primary-color);
        }
        
        .chart-container {
            height: 300px;
        }
        
        /* Data Tables Styles */
        .dashboard-tables {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .table-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
        }
        
        .top-list {
            list-style: none;
        }
        
        .top-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .top-list-item:last-child {
            border-bottom: none;
        }
        
        .list-item-info {
            display: flex;
            align-items: center;
        }
        
        .list-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: rgba(71, 118, 230, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
            color: var(--primary-color);
        }
        
        .list-item-details h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .list-item-details p {
            font-size: 13px;
            color: #777;
        }
        
        .list-item-value {
            font-size: 16px;
            font-weight: 600;
        }
        
        /* Responsive Styles */
        @media screen and (max-width: 1200px) {
            .dashboard-charts {
                grid-template-columns: 1fr;
            }
            
            .dashboard-tables {
                grid-template-columns: 1fr;
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
            
            .notification-btn, .profile-btn {
                margin-top: 15px;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
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
            <a href="admin_dashboard.php" class="menu-item active">
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
                <h1>Dashboard</h1>
                <p>Selamat datang kembali, <?php echo $_SESSION['admin']; ?>!</p>
            </div>
            
            <div class="header-actions">
                <div class="search-bar">
                    <input type="text" placeholder="Cari...">
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
                    <div class="trend-indicator positive">
                        <i class="fas fa-arrow-up"></i> 12% dibanding bulan lalu
                    </div>
                </div>
            </div>
            
            <div class="card stat-card">
                <div class="stat-icon transactions">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <!-- <h3><?php echo number_format($totalTransaksi); ?></h3> -->
                    <p>Total Transaksi</p>
                    <div class="trend-indicator positive">
                        <i class="fas fa-arrow-up"></i> 8% dibanding bulan lalu
                    </div>
                </div>
            </div>
            
            <div class="card stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <!-- <h3>Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></h3> -->
                    <p>Total Pendapatan</p>
                    <div class="trend-indicator positive">
                        <i class="fas fa-arrow-up"></i> 15% dibanding bulan lalu
                    </div>
                </div>
            </div>
            
            <div class="card stat-card">
                <div class="stat-icon customers">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>127</h3>
                    <p>Total Pelanggan</p>
                    <div class="trend-indicator positive">
                        <i class="fas fa-arrow-up"></i> 5% dibanding bulan lalu
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Charts -->
        <div class="dashboard-charts">
            <div class="chart-card">
                <div class="card-header">
                    <h2>Grafik Penjualan</h2>
                    <select id="salesPeriod">
                        <option value="daily">Harian</option>
                        <option value="weekly">Mingguan</option>
                        <option value="monthly" selected>Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div class="chart-container" id="salesChart"></div>
            </div>
            
            <div class="chart-card">
                <div class="card-header">
                    <h2>Kategori Terpopuler</h2>
                </div>
                <div class="chart-container" id="categoryChart"></div>
            </div>
        </div>
        
        <!-- Dashboard Tables -->
        <div class="dashboard-tables">
            <div class="table-card">
                <div class="card-header">
                    <h2>Produk Terlaris</h2>
                    <select>
                        <option value="week">Minggu Ini</option>
                        <option value="month" selected>Bulan Ini</option>
                        <option value="year">Tahun Ini</option>
                    </select>
                </div>
                
                <ul class="top-list">
                    <?php foreach ($produkTerlaris as $produk): ?>
                    <li class="top-list-item">
                        <div class="list-item-info">
                            <div class="list-item-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="list-item-details">
                                <h4><?php echo $produk['nama_barang']; ?></h4>
                                <p>Kode: <?php echo substr(md5($produk['nama_barang']), 0, 8); ?></p>
                            </div>
                        </div>
                        <div class="list-item-value"><?php echo number_format($produk['total_terjual']); ?> unit</div>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($produkTerlaris)): ?>
                    <li class="top-list-item">
                        <div class="list-item-info">
                            <div class="list-item-details">
                                <h4>Belum ada data produk terlaris</h4>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="table-card">
                <div class="card-header">
                    <h2>Kategori Terpopuler</h2>
                    <select>
                        <option value="week">Minggu Ini</option>
                        <option value="month" selected>Bulan Ini</option>
                        <option value="year">Tahun Ini</option>
                    </select>
                </div>
                
                <ul class="top-list">
                    <?php foreach ($kategoriPopuler as $kategori): ?>
                    <li class="top-list-item">
                        <div class="list-item-info">
                            <div class="list-item-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="list-item-details">
                                <h4><?php echo $kategori['nama_kategori']; ?></h4>
                                <p>Popularitas tinggi</p>
                            </div>
                        </div>
                        <div class="list-item-value"><?php echo number_format($kategori['total_transaksi']); ?> transaksi</div>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($kategoriPopuler)): ?>
                    <li class="top-list-item">
                        <div class="list-item-info">
                            <div class="list-item-details">
                                <h4>Belum ada data kategori populer</h4>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk grafik dari PHP
            const salesData = <?php echo json_encode(array_map(function($item) {
                return [
                    'x' => $item['bulan'],
                    'y' => (float)$item['total']
                ];
            }, $dataPenjualan)); ?>;
            
            // Data untuk kategori populer (dummy data jika kosong)
            let categoryData = <?php 
                if (empty($kategoriPopuler)) {
                    echo json_encode([
                        ['x' => 'Elektronik', 'y' => 45],
                        ['x' => 'Pakaian', 'y' => 25],
                        ['x' => 'Makanan', 'y' => 15],
                        ['x' => 'Aksesoris', 'y' => 10],
                        ['x' => 'Lainnya', 'y' => 5]
                    ]);
                } else {
                    echo json_encode(array_map(function($item) {
                        return [
                            'x' => $item['nama_kategori'],
                            'y' => (int)$item['total_transaksi']
                        ];
                    }, $kategoriPopuler));
                }
            ?>;
            
            // Grafik Penjualan
            const salesChartOptions = {
                series: [{
                    name: 'Penjualan',
                    data: salesData.length > 0 ? salesData : [
                        { x: '2025-01', y: 2500000 },
                        { x: '2025-02', y: 3200000 },
                        { x: '2025-03', y: 4100000 },
                        { x: '2025-04', y: 3800000 },
                        { x: '2025-05', y: 4800000 },
                        { x: '2025-06', y: 5500000 }
                    ]
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#4e73df'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    type: 'category',
                    labels: {
                        formatter: function(val) {
                            const date = new Date(val);
                            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
                        }
                    },
                    tickAmount: 6
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'MMM yyyy'
                    },
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            };

            // Grafik Kategori
            const categoryChartOptions = {
                series: [{
                    name: 'Transaksi',
                    data: categoryData.map(item => item.y)
                }],
                chart: {
                    type: 'pie',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                labels: categoryData.map(item => item.x),
                colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom'
                }
            };

            // Render grafik
            const salesChart = new ApexCharts(document.querySelector("#salesChart"), salesChartOptions);
            salesChart.render();

            const categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryChartOptions);
            categoryChart.render();
        });
    </script>
</body>
</html>