<?php
session_start();
require '../database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Ambil daftar produk
$query = $conn->query("SELECT * FROM produk ORDER BY id DESC");
$produk = $query->fetchAll(PDO::FETCH_ASSOC);

// Tambah produk baru
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $query = $conn->prepare("INSERT INTO produk (nama, harga, stok) VALUES (?, ?, ?)");
    $result = $query->execute([$nama, $harga, $stok]);
    
    if ($result) {
        $_SESSION['success'] = "Produk berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan produk!";
    }
    
    header("Location: admin_barang.php");
    exit();
}

// Update produk
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $query = $conn->prepare("UPDATE produk SET nama = ?, harga = ?, stok = ? WHERE id = ?");
    $result = $query->execute([$nama, $harga, $stok, $id]);
    
    if ($result) {
        $_SESSION['success'] = "Produk berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui produk!";
    }
    
    header("Location: admin_barang.php");
    exit();
}

// Hapus produk
if (isset($_GET['delete']) && isset($_GET['id'])) {
    // Check if product is used in orders before deleting
    $id = $_GET['id'];
    $checkOrders = $conn->prepare("SELECT COUNT(*) FROM pesanan WHERE produk_id = ?");
    $checkOrders->execute([$id]);
    $orderCount = $checkOrders->fetchColumn();
    
    if ($orderCount > 0) {
        $_SESSION['error'] = "Produk tidak dapat dihapus karena masih terkait dengan pesanan!";
    } else {
        $query = $conn->prepare("DELETE FROM produk WHERE id = ?");
        $result = $query->execute([$id]);
        
        if ($result) {
            $_SESSION['success'] = "Produk berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus produk!";
        }
    }
    
    header("Location: admin_barang.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - Admin Panel</title>
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
        
        /* Content Styles */
        .content-wrapper {
            margin-bottom: 20px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .content-title h2 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .add-new-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .add-new-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
        }
        
        .add-new-btn i {
            margin-right: 8px;
        }
        
        /* Table Styles */
        .data-table-wrapper {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead th {
            padding: 15px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .data-table tbody td {
            padding: 15px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(245, 247, 251, 0.5);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-badge.in-stock {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }
        
        .status-badge.low-stock {
            background-color: rgba(246, 194, 62, 0.1);
            color: #f6c23e;
        }
        
        .status-badge.out-of-stock {
            background-color: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }
        
        .btn-edit {
            background-color: rgba(78, 115, 223, 0.1);
            color: #4e73df;
        }
        
        .btn-edit:hover {
            background-color: #4e73df;
            color: white;
        }
        
        .btn-delete {
            background-color: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }
        
        .btn-delete:hover {
            background-color: #e74a3b;
            color: white;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: var(--card-bg);
            margin: 5% auto;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 50%;
            max-width: 600px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .modal-header h2 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .close {
            color: #777;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close:hover {
            color: var(--text-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            font-size: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(71, 118, 230, 0.1);
        }
        
        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn-cancel {
            background-color: #f0f0f0;
            color: #555;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-cancel:hover {
            background-color: #e0e0e0;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
        }
        
        /* Alert Styles */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
            border: 1px solid rgba(28, 200, 138, 0.2);
        }
        
        .alert-error {
            background-color: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
            border: 1px solid rgba(231, 74, 59, 0.2);
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .pagination-item {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .pagination-item.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .pagination-item:not(.active) {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid #e0e0e0;
        }
        
        .pagination-item:not(.active):hover {
            background-color: #f0f0f0;
        }
        
        /* Responsive Styles */
        @media screen and (max-width: 1200px) {
            .modal-content {
                width: 70%;
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
            
            .modal-content {
                width: 90%;
                margin: 10% auto;
            }
        }
        
        @media screen and (max-width: 576px) {
            .action-buttons {
                flex-direction: column;
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
                <h1>Manajemen Barang</h1>
                <p>Kelola barang dan produk toko Anda</p>
            </div>
            
            <div class="header-actions">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Cari produk...">
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
        
        <!-- Alert Messages -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="content-title">
                    <h2>Daftar Produk</h2>
                </div>
                
                <button class="add-new-btn" id="addProductBtn">
                    <i class="fas fa-plus"></i> Tambah Produk Baru
                </button>
            </div>
            
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($produk as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo $item['nama']; ?></td>
                            <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $item['stok']; ?></td>
                            <td>
                                <?php if($item['stok'] > 10): ?>
                                    <span class="status-badge in-stock">Tersedia</span>
                                <?php elseif($item['stok'] > 0): ?>
                                    <span class="status-badge low-stock">Stok Menipis</span>
                                <?php else: ?>
                                    <span class="status-badge out-of-stock">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-edit edit-product" data-id="<?php echo $item['id']; ?>"
                                            data-nama="<?php echo $item['nama']; ?>"
                                            data-harga="<?php echo $item['harga']; ?>"
                                            data-stok="<?php echo $item['stok']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete delete-product" data-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($produk)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Belum ada data produk</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination">
                <div class="pagination-item"><i class="fas fa-chevron-left"></i></div>
                <div class="pagination-item active">1</div>
                <div class="pagination-item">2</div>
                <div class="pagination-item">3</div>
                <div class="pagination-item"><i class="fas fa-chevron-right"></i></div>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Produk Baru</h2>
                <span class="close">&times;</span>
            </div>
            
            <form id="productForm" method="POST">
                <input type="hidden" id="productId" name="id">
                
                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" id="nama" name="nama" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" class="form-control" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" id="cancelBtn">Batal</button>
                    <button type="submit" class="btn-submit" id="submitBtn" name="tambah">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Konfirmasi Hapus</h2>
                <span class="close">&times;</span>
            </div>
            
            <p>Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
            <p class="alert alert-error" style="margin-top: 10px;">Perhatian: Produk yang terkait dengan pesanan tidak dapat dihapus.</p>
            
            <div class="form-buttons">
                <button type="button" class="btn-cancel" id="cancelDeleteBtn">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn-submit" style="text-decoration: none; text-align: center;">Hapus</a>
            </div>
        </div>
    </div>

    <script>
        // Get modal elements
        const productModal = document.getElementById('productModal');
        const deleteModal = document.getElementById('deleteModal');
        const modalTitle = document.getElementById('modalTitle');
        const productForm = document.getElementById('productForm');
        const productId = document.getElementById('productId');
        const submitBtn = document.getElementById('submitBtn');
        
        // Add product button
        document.getElementById('addProductBtn').addEventListener('click', function() {
            modalTitle.innerText = 'Tambah Produk Baru';
            productForm.reset();
            productId.value = '';
            submitBtn.name = 'tambah';
            submitBtn.innerText = 'Simpan';
            productModal.style.display = 'block';
        });
        
        // Edit product buttons
        const editButtons = document.querySelectorAll('.edit-product');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const harga = this.getAttribute('data-harga');
                const stok = this.getAttribute('data-stok');
                
                modalTitle.innerText = 'Edit Produk';
                productId.value = id;
                document.getElementById('nama').value = nama;
                document.getElementById('harga').value = harga;
                document.getElementById('stok').value = stok;
                submitBtn.name = 'update';
                submitBtn.innerText = 'Update';
                productModal.style.display = 'block';
            });
        });
        
        // Delete product buttons
        const deleteButtons = document.querySelectorAll('.delete-product');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('confirmDeleteBtn').href = `?delete=1&id=${id}`;
                deleteModal.style.display = 'block';
            });
        });
        
        // Close buttons
        const closeButtons = document.querySelectorAll('.close');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                productModal.style.display = 'none';
                deleteModal.style.display = 'none';
            });
        });
        
        // Cancel buttons
        document.getElementById('cancelBtn').addEventListener('click', function() {
            productModal.style.display = 'none';
        });
        
        document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
            deleteModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target == productModal) {
                productModal.style.display = 'none';
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            
            tableRows.forEach(row => {
                const productName = row.children[1].textContent.toLowerCase();
                if (productName.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>