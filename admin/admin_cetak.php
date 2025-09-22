<?php
session_start();
require '../database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Ambil daftar dokumen yang diunggah
$query = $conn->query("SELECT * FROM dokumen ORDER BY id DESC");
$dokumen = $query->fetchAll();

// Update status cetak
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $query = $conn->prepare("UPDATE dokumen SET status = ? WHERE id = ?");
    $query->execute([$status, $id]);
    
    // Kirim notifikasi WhatsApp (simulasi)
    echo "<script>alert('Status dokumen diperbarui dan notifikasi dikirim!'); window.location.href='admin_cetak.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Cetak Dokumen - AdminPanel</title>
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
        
        /* Tabel Dokumen */
        .dokumen-table {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .dokumen-table h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        table th {
            background-color: rgba(71, 118, 230, 0.1);
            color: var(--primary-color);
            font-weight: 600;
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        table tr:hover {
            background-color: rgba(245, 247, 251, 0.5);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-menunggu {
            background-color: rgba(246, 194, 62, 0.2);
            color: #dda20a;
        }
        
        .status-proses {
            background-color: rgba(78, 115, 223, 0.2);
            color: #2e59d9;
        }
        
        .status-selesai {
            background-color: rgba(28, 200, 138, 0.2);
            color: #13855c;
        }
        
        .file-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .file-link:hover {
            text-decoration: underline;
        }
        
        .file-link i {
            margin-right: 5px;
        }
        
        .form-group {
            display: flex;
            gap: 10px;
        }
        
        select {
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            background-color: white;
        }
        
        select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(71, 118, 230, 0.1);
        }
        
        button {
            padding: 8px 15px;
            border-radius: 8px;
            border: none;
            background-color: var(--primary-color);
            color: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }
        
        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        /* Responsive Styles */
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
            
            .table-responsive {
                overflow-x: scroll;
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
                <h1>Kelola Cetak Dokumen</h1>
                <p>Manajemen dokumen yang diunggah oleh pelanggan</p>
            </div>
            
            <div class="header-actions">
                <div class="search-bar">
                    <input type="text" placeholder="Cari dokumen..." id="searchInput" onkeyup="searchTable()">
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
        
        <!-- Dokumen Table -->
        <div class="dokumen-table">
            <h2>Daftar Dokumen Cetak</h2>
            
            <div class="table-responsive">
                <table id="dokumenTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pelanggan</th>
                            <th>No. WhatsApp</th>
                            <th>File Dokumen</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dokumen as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo $item['nama_pembeli']; ?></td>
                                <td><?php echo $item['no_wa']; ?></td>
                                <td>
                                    <a href="../uploads/<?php echo $item['file_path']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-file-pdf"></i> Lihat Dokumen
                                    </a>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($item['uploaded_at'])); ?></td>
                                <td>
                                    <span class="status-badge <?php 
                                        if ($item['status'] == 'Menunggu') echo 'status-menunggu';
                                        else if ($item['status'] == 'Sedang Diproses') echo 'status-proses';
                                        else echo 'status-selesai';
                                    ?>">
                                        <?php echo $item['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="form-group">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <select name="status">
                                            <option value="Menunggu" <?php if ($item['status'] == 'Menunggu') echo 'selected'; ?>>Menunggu</option>
                                            <option value="Sedang Diproses" <?php if ($item['status'] == 'Sedang Diproses') echo 'selected'; ?>>Sedang Diproses</option>
                                            <option value="Selesai" <?php if ($item['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($dokumen)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada dokumen yang diunggah</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("dokumenTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                // Skip the header row
                if (i === 0) continue;
                
                // Check multiple columns (name, phone, status)
                var nameCol = tr[i].getElementsByTagName("td")[1];
                var phoneCol = tr[i].getElementsByTagName("td")[2];
                var statusCol = tr[i].getElementsByTagName("td")[5];
                
                if (nameCol && phoneCol && statusCol) {
                    var nameText = nameCol.textContent || nameCol.innerText;
                    var phoneText = phoneCol.textContent || phoneCol.innerText;
                    var statusText = statusCol.textContent || statusCol.innerText;
                    
                    if (
                        nameText.toUpperCase().indexOf(filter) > -1 || 
                        phoneText.toUpperCase().indexOf(filter) > -1 ||
                        statusText.toUpperCase().indexOf(filter) > -1
                    ) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>