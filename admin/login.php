<?php
session_start();
require '../database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $query->execute([$username]);
    $admin = $query->fetch();

    // Menggunakan perbandingan biasa (TIDAK AMAN untuk produksi)
    if ($admin && $password == $admin['password']) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            width: 400px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 10;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .login-header {
            background: linear-gradient(to right, #4776e6, #8e54e9);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .login-header h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .admin-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: white;
            padding: 5px;
            margin: 0 auto 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .admin-avatar i {
            font-size: 60px;
            color: #4776e6;
        }
        
        .login-form {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group .input-with-icon {
            position: relative;
        }
        
        .form-group .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #888;
            transition: all 0.3s ease;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-group input:focus {
            border-color: #4776e6;
            box-shadow: 0 0 0 3px rgba(71, 118, 230, 0.1);
        }
        
        .form-group input:focus + i {
            color: #4776e6;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input {
            margin-right: 8px;
        }
        
        .forgot-password {
            color: #4776e6;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #4776e6, #8e54e9);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }
        
        .login-btn:hover {
            background: linear-gradient(to right, #3a66d6, #7646d9);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
        }
        
        .login-footer {
            text-align: center;
            padding: 0 30px 20px;
            font-size: 14px;
            color: #666;
        }
        
        .login-footer a {
            color: #4776e6;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background-color: rgba(255, 87, 87, 0.1);
            border-left: 4px solid #ff5757;
            color: #e41c1c;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 10px;
            font-size: 16px;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .floating {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="particles" id="particles-js"></div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="admin-avatar floating">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2>Administrator</h2>
            <p>Silakan login untuk akses panel admin</p>
        </div>
        
        <div class="login-form">
            <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required autocomplete="off">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                    <a href="#" class="forgot-password">Lupa Password?</a>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>&copy; 2025 Admin Panel. Hubungi <a href="#">IT Support</a> jika ada masalah</p>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS("particles-js", {
                "particles": {
                    "number": {
                        "value": 80,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#ffffff"
                    },
                    "shape": {
                        "type": "circle",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        },
                        "polygon": {
                            "nb_sides": 5
                        }
                    },
                    "opacity": {
                        "value": 0.5,
                        "random": false,
                        "anim": {
                            "enable": false,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 3,
                        "random": true,
                        "anim": {
                            "enable": false,
                            "speed": 40,
                            "size_min": 0.1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#ffffff",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 2,
                        "direction": "none",
                        "random": false,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                        "attract": {
                            "enable": false,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "grab"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    },
                    "modes": {
                        "grab": {
                            "distance": 140,
                            "line_linked": {
                                "opacity": 1
                            }
                        },
                        "bubble": {
                            "distance": 400,
                            "size": 40,
                            "duration": 2,
                            "opacity": 8,
                            "speed": 3
                        },
                        "repulse": {
                            "distance": 200,
                            "duration": 0.4
                        },
                        "push": {
                            "particles_nb": 4
                        },
                        "remove": {
                            "particles_nb": 2
                        }
                    }
                },
                "retina_detect": true
            });
            
            // Toggle password visibility
            const passwordField = document.getElementById('password');
            const passwordIcon = document.querySelector('.form-group:nth-child(2) .input-with-icon i');
            
            passwordIcon.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-lock');
                this.classList.toggle('fa-unlock');
            });
        });
    </script>
</body>
</html>