<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $no_wa = $_POST['no_wa'];

    $file = $_FILES['dokumen'];
    $fileName = time() . "_" . basename($file['name']);
    $targetFilePath = "uploads/" . $fileName;
    
    // Membuat direktori uploads jika belum ada
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        $query = $conn->prepare("INSERT INTO dokumen (nama_pembeli, no_wa, file_path, status) VALUES (?, ?, ?, 'Pending')");
        $query->execute([$nama, $no_wa, $fileName]);

        echo "<script>alert('Dokumen berhasil diunggah.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengunggah file.');</script>";
    }
}

// Mengambil data cetakan terbaru untuk ditampilkan di tabel
$query = $conn->query("SELECT * FROM dokumen ORDER BY id DESC LIMIT 10");
$cetakan = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen | ATK & Percetakan</title>
    <style>
        /* Reset dan Variabel Warna */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            box-shadow: var(--shadow);
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        header p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Navigasi */
        nav {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        nav li {
            margin: 0;
        }

        nav a {
            display: flex;
            align-items: center;
            color: var(--dark-color);
            text-decoration: none;
            padding: 1.2rem 2rem;
            font-weight: 600;
            transition: var(--transition);
        }

        nav a:hover, nav a.active {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        nav a i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Form */
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .form-container:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .form-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: var(--transition);
        }

        input[type="text"]:focus,
        input[type="file"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            font-size: 1rem;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        .btn-alt {
            background-color: var(--accent-color);
        }

        /* Table */
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            overflow-x: auto;
        }

        .table-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: var(--dark-color);
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
        }

        .status-pending {
            background-color: #FFC107;
            color: #000;
        }

        .status-processed {
            background-color: #2196F3;
            color: white;
        }

        .status-completed {
            background-color: #4CAF50;
            color: white;
        }

        .status-cancelled {
            background-color: #F44336;
            color: white;
        }

        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: var(--light-color);
            text-align: center;
            padding: 2rem;
            margin-top: 2rem;
        }

        footer p {
            opacity: 0.8;
        }

        /* Animasi Fade In */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animated {
            animation: fadeIn 0.8s ease forwards;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }

        /* Responsive */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
            
            nav ul {
                flex-direction: column;
            }
            
            .container {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.5rem;
            }
        }
    </style>
    <!-- Font Awesome untuk ikon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>
<body>
    <header class="animated">
        <h1>Sistem Penjualan ATK dan Percetakan</h1>
        <p>Layanan percetakan dokumen profesional dengan hasil berkualitas tinggi</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="pembelian.php"><i class="fas fa-shopping-cart"></i> Pembelian ATK</a></li>
            <li><a href="percetakan.php" class="active"><i class="fas fa-print"></i> Percetakan Dokumen</a></li>
            <li><a href="kontak.php"><i class="fas fa-envelope"></i> Kontak</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="form-container animated delay-1">
            <h2 class="form-title">Unggah Dokumen untuk Dicetak</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama"><i class="fas fa-user"></i> Nama Lengkap:</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap Anda">
                </div>
                
                <div class="form-group">
                    <label for="no_wa"><i class="fab fa-whatsapp"></i> Nomor WhatsApp:</label>
                    <input type="text" id="no_wa" name="no_wa" required placeholder="Contoh: 08123456789">
                </div>
                
                <div class="form-group">
                    <label for="dokumen"><i class="fas fa-file-pdf"></i> Unggah Dokumen:</label>
                    <input type="file" id="dokumen" name="dokumen" required accept=".pdf,.docx,.doc,.ppt,.pptx,.xls,.xlsx">
                    <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                        Format yang didukung: PDF, DOCX, DOC, PPT, PPTX, XLS, XLSX. Maksimal ukuran file: 10MB.
                    </p>
                </div>
                
                <button type="submit" class="btn btn-alt"><i class="fas fa-paper-plane"></i> Unggah & Proses Cetak</button>
            </form>
        </div>

        <div class="table-container animated delay-2">
            <h2 class="table-title">Status Percetakan Dokumen</h2>
            
            <?php if(empty($cetakan)): ?>
                <div style="text-align: center; padding: 2rem; color: #666;">
                    <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--primary-color);"></i>
                    <p>Belum ada data percetakan dokumen.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Dokumen</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cetakan as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['nama_pembeli']) ?></td>
                            <td><?= htmlspecialchars($item['file_path']) ?></td>
                            <td><?= isset($item['created_at']) ? date('d/m/Y H:i', strtotime($item['created_at'])) : date('d/m/Y H:i') ?></td>
                            <td>
                                <?php
                                $statusClass = '';
                                switch($item['status']) {
                                    case 'Pending':
                                        $statusClass = 'status-pending';
                                        break;
                                    case 'Diproses':
                                        $statusClass = 'status-processed';
                                        break;
                                    case 'Selesai':
                                        $statusClass = 'status-completed';
                                        break;
                                    case 'Dibatalkan':
                                        $statusClass = 'status-cancelled';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($item['status']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Sistem Penjualan ATK dan Percetakan. Semua hak dilindungi.</p>
    </footer>
</body>
</html>