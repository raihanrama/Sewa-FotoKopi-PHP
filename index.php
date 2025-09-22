<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang | ATK & Percetakan</title>
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

        nav a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        nav a i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        /* Kartu Layanan */
        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            padding: 3rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            width: 300px;
            transition: var(--transition);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            height: 160px;
            overflow: hidden;
        }

        .card-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .service-card:hover .card-header img {
            transform: scale(1.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-body h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .card-body p {
            margin-bottom: 1.5rem;
            color: #555;
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
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        .btn-alt {
            background-color: var(--accent-color);
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
        .delay-3 { animation-delay: 0.6s; }

        /* Responsive */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
            
            nav ul {
                flex-direction: column;
            }
            
            .services {
                padding: 2rem 1rem;
            }
        }
    </style>
    <!-- Font Awesome untuk ikon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>
<body>
    <header class="animated">
        <h1>Sistem Penjualan ATK dan Percetakan</h1>
        <p>Solusi terpadu untuk kebutuhan alat tulis kantor dan jasa percetakan dokumen dengan kualitas terbaik dan harga terjangkau</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="pembelian.php"><i class="fas fa-shopping-cart"></i> Pembelian ATK</a></li>
            <li><a href="percetakan.php"><i class="fas fa-print"></i> Percetakan Dokumen</a></li>
            <li><a href="kontak.php"><i class="fas fa-envelope"></i> Kontak</a></li>
        </ul>
    </nav>

    <section class="services">
        <div class="service-card animated delay-1">
            <div class="card-header">
                <img src="/api/placeholder/300/160" alt="Alat Tulis Kantor">
            </div>
            <div class="card-body">
                <h3>Pembelian ATK</h3>
                <p>Tersedia berbagai kebutuhan alat tulis kantor dengan kualitas terbaik untuk kebutuhan bisnis, sekolah, atau pribadi Anda.</p>
                <a href="pembelian.php" class="btn"><i class="fas fa-shopping-cart"></i> Belanja Sekarang</a>
            </div>
        </div>

        <div class="service-card animated delay-2">
            <div class="card-header">
                <img src="/api/placeholder/300/160" alt="Percetakan Dokumen">
            </div>
            <div class="card-body">
                <h3>Percetakan Dokumen</h3>
                <p>Layanan cetak dokumen profesional dengan berbagai pilihan kertas, ukuran, dan finishing untuk hasil cetak berkualitas.</p>
                <a href="percetakan.php" class="btn btn-alt"><i class="fas fa-print"></i> Cetak Dokumen</a>
            </div>
        </div>

        <div class="service-card animated delay-3">
            <div class="card-header">
                <img src="/api/placeholder/300/160" alt="Layanan Pelanggan">
            </div>
            <div class="card-body">
                <h3>Layanan Pelanggan</h3>
                <p>Butuh bantuan? Tim kami siap membantu Anda untuk konsultasi produk, pesanan, atau pertanyaan lainnya.</p>
                <a href="kontak.php" class="btn"><i class="fas fa-headset"></i> Hubungi Kami</a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Sistem Penjualan ATK dan Percetakan. Semua hak dilindungi.</p>
    </footer>
</body>
</html>