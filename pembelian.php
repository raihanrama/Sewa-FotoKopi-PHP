<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fotokopi";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch products from database
$stmt = $conn->prepare("SELECT * FROM produk");
$stmt->execute();
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process order form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $no_wa = $_POST['no_wa'];
    $alamat = $_POST['alamat'];
    $total_harga = 0;
    
    $_SESSION['no_wa'] = $no_wa; // Save WhatsApp number to session
    
    foreach ($_POST['produk'] as $id => $jumlah) {
        if ($jumlah > 0) {
            $stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            $subtotal = $item['harga'] * $jumlah;
            $total_harga += $subtotal;
            
            // Save order to database
            $query = $conn->prepare("INSERT INTO pesanan (nama_pembeli, no_wa, alamat, produk_id, jumlah, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $query->execute([$nama, $no_wa, $alamat, $id, $jumlah, $subtotal]);
        }
    }
    
    // Return success message for AJAX request
    echo json_encode(['success' => true]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko ATK Online</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        /* Header */
        .site-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            box-shadow: var(--shadow);
        }

        .site-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .site-header p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Navigasi */
        .main-nav {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .main-nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .main-nav li {
            margin: 0;
        }

        .main-nav a {
            display: flex;
            align-items: center;
            color: var(--dark-color);
            text-decoration: none;
            padding: 1.2rem 2rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .main-nav a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        .main-nav a i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        /* Product Cards */
        .product-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .product-img {
            height: 200px;
            object-fit: contain;
            padding: 1rem;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: bold;
        }

        .card-footer {
            background-color: transparent;
            border-top: none;
        }

        .btn {
            border-radius: 30px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-danger {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .cart-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn-cart {
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: bold;
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-cart:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Modal styles */
        .modal-header.bg-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        }

        .modal-header.bg-success {
            background: var(--secondary-color) !important;
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
            margin-bottom: 0;
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
        .delay-4 { animation-delay: 0.8s; }

        /* Responsive */
        @media (max-width: 768px) {
            .site-header h1 {
                font-size: 2rem;
            }
            
            .main-nav ul {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header animated">
        <h1>Toko ATK Online</h1>
        <p>Pilih produk ATK berkualitas untuk kebutuhan Anda dengan harga terbaik dan layanan pengiriman cepat</p>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="produk.php"><i class="fas fa-shopping-bag"></i> Produk</a></li>
            <li><a href="percetakan.php"><i class="fas fa-print"></i> Percetakan</a></li>
            <li><a href="kontak.php"><i class="fas fa-envelope"></i> Kontak</a></li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fas fa-shopping-cart"></i> Keranjang
                    <span class="badge bg-danger ms-2" id="cartCount">0</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Products Section -->
    <div class="container py-5">
        <h2 class="mb-4 text-center animated">Produk ATK Tersedia</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="productContainer">
            <?php if(count($produk) > 0): ?>
                <?php $delay = 1; ?>
                <?php foreach($produk as $item): ?>
                    <div class="col animated delay-<?= min($delay++, 4) ?>">
                        <div class="product-card">
                            <div class="text-center p-3">
                                <!-- Display default image if no image is available -->
                                <img src="<?= isset($item['gambar']) && !empty($item['gambar']) ? $item['gambar'] : 'https://via.placeholder.com/150?text=ATK' ?>" class="product-img" alt="<?= $item['nama'] ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= $item['nama'] ?></h5>
                                <p class="card-text"><?= isset($item['deskripsi']) ? $item['deskripsi'] : 'Produk ATK berkualitas' ?></p>
                                <p class="fw-bold text-primary">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                                <p class="text-muted">Stok: <?= $item['stok'] ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <button class="btn btn-primary w-100 add-to-cart-btn" 
                                        data-id="<?= $item['id'] ?>" 
                                        data-name="<?= $item['nama'] ?>" 
                                        data-price="<?= $item['harga'] ?>"
                                        data-img="<?= isset($item['gambar']) && !empty($item['gambar']) ? $item['gambar'] : 'https://via.placeholder.com/150?text=ATK' ?>"
                                        <?= $item['stok'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                    <h4>Tidak ada produk tersedia</h4>
                    <p>Silakan cek kembali nanti.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cartModalLabel">Keranjang Belanja</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="emptyCart" class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h4>Keranjang belanja Anda kosong</h4>
                        <p>Silahkan tambahkan produk ke keranjang</p>
                    </div>
                    <div id="cartItems" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cartItemsList">
                                    <!-- Cart items will be added here dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td id="cartTotal" class="fw-bold">Rp 0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lanjut Belanja</button>
                    <button type="button" class="btn btn-primary" id="checkoutBtn" data-bs-toggle="modal" data-bs-target="#checkoutModal" disabled>Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="checkoutModalLabel">Checkout Pesanan</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="checkoutForm" method="POST">
                    <div class="modal-body">
                        <h5 class="mb-3">Data Pembeli</h5>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_wa" class="form-label">Nomor WhatsApp</label>
                            <input type="text" class="form-control" id="no_wa" name="no_wa" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        <div id="orderSummary">
                            <h5 class="mt-4 mb-3">Ringkasan Pesanan</h5>
                            <div id="checkoutItems">
                                <!-- Order summary will be displayed here -->
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <span class="fw-bold">Total Pembayaran:</span>
                                <span class="fw-bold text-primary" id="checkoutTotal">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Pesan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Pesanan Berhasil</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>Terima Kasih!</h4>
                    <p>Pesanan Anda telah berhasil diproses. Kami akan segera menghubungi Anda melalui WhatsApp.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">Kembali ke Toko</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Toko ATK Online. Semua hak dilindungi.</p>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize cart array
        let cart = [];
        
        // Update cart display
        function updateCart() {
            const cartItemsList = document.getElementById('cartItemsList');
            const cartTotal = document.getElementById('cartTotal');
            const cartCount = document.getElementById('cartCount');
            const emptyCart = document.getElementById('emptyCart');
            const cartItems = document.getElementById('cartItems');
            const checkoutItems = document.getElementById('checkoutItems');
            const checkoutTotal = document.getElementById('checkoutTotal');
            
            // Clear current items
            cartItemsList.innerHTML = '';
            checkoutItems.innerHTML = '';
            
            // Calculate total
            let total = 0;
            let itemCount = 0;
            
            // Show/hide empty cart message
            if (cart.length === 0) {
                emptyCart.classList.remove('d-none');
                cartItems.classList.add('d-none');
                document.getElementById('checkoutBtn').disabled = true;
            } else {
                emptyCart.classList.add('d-none');
                cartItems.classList.remove('d-none');
                document.getElementById('checkoutBtn').disabled = false;
                
                // Add each item to the cart
                cart.forEach((item, index) => {
                    const subtotal = item.price * item.quantity;
                    total += subtotal;
                    itemCount += item.quantity;
                    
                    // Add to cart modal
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${item.img}" alt="${item.name}" width="50" height="50" class="me-3">
                                <div>
                                    <h6 class="mb-0">${item.name}</h6>
                                    <input type="hidden" name="produk[${item.id}]" value="${item.quantity}">
                                </div>
                            </div>
                        </td>
                        <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                        <td>
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${index}, ${item.quantity - 1})">-</button>
                                <input type="number" class="form-control text-center" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, parseInt(this.value))">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${index}, ${item.quantity + 1})">+</button>
                            </div>
                        </td>
                        <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                        <td><button class="btn btn-sm btn-danger" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button></td>
                    `;
                    cartItemsList.appendChild(tr);
                    
                    // Add to checkout summary
                    const summaryItem = document.createElement('div');
                    summaryItem.className = 'd-flex justify-content-between mb-2';
                    summaryItem.innerHTML = `
                        <span>${item.name} × ${item.quantity}</span>
                        <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                    `;
                    checkoutItems.appendChild(summaryItem);
                });
            }
            
            // Update totals
            cartTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            checkoutTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            cartCount.textContent = itemCount;
        }
        
        // Add item to cart
        function addToCart(id, name, price, img) {
            // Check if item already exists in cart
            const existingItemIndex = cart.findIndex(item => item.id === id);
            
            if (existingItemIndex !== -1) {
                // Update quantity if item exists
                cart[existingItemIndex].quantity += 1;
            } else {
                // Add new item if it doesn't exist
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    img: img
                });
            }
            
            // Update cart display
            updateCart();
            
            // Show toast notification
            const toastEl = document.createElement('div');
            toastEl.className = 'toast position-fixed bottom-0 end-0 m-3';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Produk Ditambahkan</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${name} telah ditambahkan ke keranjang belanja.
                </div>
            `;
            document.body.appendChild(toastEl);
            
            const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
            toast.show();
            
            // Remove toast after it's hidden
            toastEl.addEventListener('hidden.bs.toast', function () {
                this.remove();
            });
        }
        
        // Update item quantity
        function updateQuantity(index, quantity) {
            if (quantity <= 0) {
                removeItem(index);
            } else {
                cart[index].quantity = quantity;
                updateCart();
            }
        }
        
        // Remove item from cart
        function removeItem(index) {
            cart.splice(index, 1);
            updateCart();
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart buttons
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const price = parseFloat(this.getAttribute('data-price'));
                    const img = this.getAttribute('data-img');
                    addToCart(id, name, price, img);
                });
            });
            
            // Checkout form submission
            const checkoutForm = document.getElementById('checkoutForm');
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Create form data
                const formData = new FormData(this);
                
                // Add cart items to form data
                cart.forEach(item => {
                    formData.append(`produk[${item.id}]`, item.quantity);
                });
                
                // Submit form via AJAX
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide checkout modal
                        const checkoutModal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
                        checkoutModal.hide();
                        
                        // Show success modal
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        
                        // Clear cart
                        cart = [];
                        updateCart();
                    } else {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
            });
        });
    </script>
</body>
</html>