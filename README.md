# 🖨️ Stationery & Print Service System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

*Sistem manajemen modern untuk pembelian alat tulis kantor dan layanan print/fotokopi*


</div>

---

## 📋 Deskripsi

Sistem Pembelian Alat Tulis (ATK) & Print Service adalah aplikasi web berbasis PHP yang dirancang untuk memudahkan pengelolaan bisnis alat tulis kantor dan layanan percetakan. Sistem ini menyediakan platform terintegrasi untuk penjualan produk alat tulis serta layanan print/fotokopi dengan fitur upload dokumen.

## ✨ Fitur

### 🛒 **Sistem Pembelian Alat Tulis**
- **Katalog Produk** - Browse dan pencarian produk ATK yang tersedia
- **Keranjang Belanja** - Sistem cart yang user-friendly dan mudah dipahami
- **Manajemen Stok** - Tracking inventory real-time
- **Checkout Terintegrasi** - Proses pemesanan yang lancar

### 🖨️ **Layanan Print & Fotokopi**
- **Upload Dokumen** - Support multiple format file (PDF, DOC, DOCX)
- **Preview Dokumen** - Tampilan preview sebelum print dan nama filenya 
- **Konfigurasi Print** - Pengaturan ukuran kertas, orientasi, jumlah cetak
- **Estimasi Biaya** - Kalkulasi otomatis total biaya jasa print

### 👤 **Manajemen Pengguna**
- **Sistem Login** - Autentikasi aman untuk customer dan admin
- **Dashboard Admin** - Panel kontrol untuk pengelolaan sistem
- **Riwayat Transaksi** - Tracking semua aktivitas pembelian dan print
- **Profil Pengguna** - Manajemen data personal

### 📊 **Fitur Administrasi**
- **Manajemen Produk** - CRUD produk alat tulis
- **Laporan Penjualan** - Analytics dan reporting
- **Manajemen Order** - Tracking status pesanan
- **Konfigurasi Sistem** - Pengaturan aplikasi

## 🛠️ Teknologi

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS dengan desain responsive
- **File Upload**: PHP File Handling dengan validasi keamanan

## 🚀 Instalasi

### Prasyarat
```bash
- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx Web Server
- Composer (opsional)
```

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/raihanrama/Sewa-FotoKopi-PHP.git
   cd Sewa-FotoKopi-PHP
   ```

2. **Setup Database**
   ```sql
   CREATE DATABASE stationery_print_db;
   ```
   Import file database SQL yang disediakan

3. **Konfigurasi Database**
   Edit file `database.php`:
   ```php
   <?php
   $host = 'localhost';
   $username = 'your_username';
   $password = 'your_password';
   $database = 'stationery_print_db';
   ?>
   ```

4. **Akses Aplikasi**
   Buka browser dan akses: `http://localhost/stationery-print-system`

## 📖 Penggunaan

### Untuk Customer
1. **Registrasi/Login** - Buat akun atau masuk ke sistem
2. **Browse Produk** - Jelajahi katalog alat tulis
3. **Tambah ke Keranjang** - Pilih produk yang diinginkan
4. **Upload Dokumen** - Upload file untuk layanan print
5. **Checkout** - Proses pembayaran dan konfirmasi pesanan

### Untuk Admin
1. **Dashboard Admin** - Akses panel administrasi
2. **Kelola Produk** - Tambah, edit, hapus produk
3. **Monitor Pesanan** - Tracking status semua pesanan
4. **Generate Laporan** - Buat laporan penjualan dan statistik

## 📁 Struktur Proyek

```
stationery-print-system/
├── admin/                 # Panel administrasi
├── uploads/              # Folder upload dokumen
├── database.php          # Konfigurasi database
├── index.php            # Halaman utama
├── pembayaran.php       # Sistem pembayaran
├── pembelian.php        # Proses pembelian
├── percetakan.php       # Layanan print
├── proses_pembayaran.php # Handler pembayaran
├── style.css           # Styling aplikasi
├── upload.php          # Handler upload file
└── README.md           # Dokumentasi
```

## 🔒 Keamanan

- **Input Validation** - Validasi semua input pengguna
- **File Upload Security** - Pembatasan tipe dan ukuran file
- **SQL Injection Protection** - Prepared statements
- **Session Management** - Pengelolaan sesi yang aman
- **Access Control** - Sistem otorisasi berbasis role

## 🎨 Screenshots

### Dashboard Utama
*[Tambahkan screenshot dashboard utama]*

### Katalog Produk
*[Tambahkan screenshot katalog produk]*

### Upload & Print Service
*[Tambahkan screenshot fitur upload]*

## 📈 Roadmap

- [ ] **Payment Gateway Integration** - Integrasi dengan payment gateway
- [ ] **Mobile App** - Aplikasi mobile Android/iOS
- [ ] **API Development** - RESTful API untuk integrasi
- [ ] **Multi-language Support** - Dukungan bahasa Indonesia dan Inggris
- [ ] **Advanced Analytics** - Dashboard analytics yang lebih detail

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Proyek ini dilisensikan under MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 👨‍💻 Developer

Dikembangkan dengan ❤️ oleh **[Raihanrama](https://github.com/raihanrama)**

---

<div align="center">

**[⭐ Star this repository jika bermanfaat!](https://github.com/username/stationery-print-system)**

Made with ❤️ in Indonesia 🇮🇩

</div>
