<?php
session_start();
require_once 'config/database.php';
// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    
    
    
    // Cek kredensial (contoh sederhana, ganti dengan query database yang aman)
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        // Verifikasi password (gunakan password_verify() jika password di-hash)
        if ($password === $user['password']) { // Ganti dengan password_verify() untuk keamanan
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect ke dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $_SESSION['notif'] = [
                'type' => 'danger',
                'message' => 'Password tidak sesuai!'
            ];
        }
    } else {
        $_SESSION['notif'] = [
                'type' => 'warning',
                'message' => 'Username tidak ditemukan!'
            ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/image/dinsos_logo.png">
  <title>Login - Sistem Informasi Dinsos Kota Tarakan</title>
    
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/bootstrap-table/dist/bootstrap-table.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            /* Background gambar + overlay gelap */
            background:
                linear-gradient(
                    rgba(0, 0, 0, 0.3),
                    rgba(0, 0, 0, 0.3)
                ),
                url('assets/image/final\ gambar\ bg.png') center/cover no-repeat fixed;
        }

        
        .login-container {
            display: flex;
            width: 900px;
            height: 550px;

            /* transparan halus */
            background: rgba(255, 255, 255, 0.5);

            border-radius: 20px;
            overflow: hidden;

            /* efek kaca */
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);

            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
        }

        input{
            background: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
        }

        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-left h1 {
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .login-left p {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .features {
            list-style: none;
        }
        
        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .features i {
            margin-right: 10px;
            background: rgba(255,255,255,0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-right {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-logo {
            text-align: center;
            /* margin-bottom: 40px; */
        }
        
        .login-logo img {
            height: 70px;
            /* margin-bottom: 15px; */
        }
        
        .login-logo h2 {
            color: #1e3c72;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .login-logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .input-with-icon input:focus {
            border-color: #1e3c72;
            outline: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }
        
        .copyright {
            text-align: center;
            margin-top: 30px;
            color: #464646;
            font-size: 12px;
        }

        /* notif */
        .notif-wrapper {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1055;
            width: auto;
            max-width: 90%;
        }

        .notif-wrapper .alert {
            min-width: 300px;
            text-align: center;
        }
                
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 90%;
                height: auto;
            }
            
            .login-left {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- notif  -->

    <?php if (isset($_SESSION['notif'])): ?>
        <div class="notif-wrapper">
            <div class="alert alert-<?= $_SESSION['notif']['type']; ?> alert-dismissible fade show auto-close shadow"
                role="alert">
                <?= $_SESSION['notif']['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php
        unset($_SESSION['notif']);
        endif;
        ?>

    <div class="login-container">
        <!-- Bagian Kiri: Informasi Sistem -->
        <div class="login-left">
            <h1>Sistem Informasi Dinsos Tarakan</h1>
            <p>Sistem terintegrasi untuk pengelolaan laporan dan kegiatan Dinas Sosial & Pemberdayaan Masyarakat Kota Tarakan</p>
            
            <ul class="features">
                <li><i class="bi bi-check fs-2"></i> Manajemen Laporan</li>
                <li><i class="bi bi-check fs-2"></i> Monitoring Kegiatan Real-time</li>
                <li><i class="bi bi-check fs-2"></i> Rekapitulasi Data</li>
                <li><i class="bi bi-check fs-2"></i> Akses Multi-User</li>
                <li><i class="bi bi-check fs-2"></i> Kemudahan Pengelolaan Data</li>
            </ul>
        </div>
        
        <!-- Bagian Kanan: Form Login -->
        <div class="login-right">
            <div class="login-logo">
                <!-- Ganti dengan logo asli Kota Tarakan -->
                <div style=" width: 70px; height: 70px; border-radius: 10px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                    <img src="assets/image/dinsos_logo.png" alt="">
                </div>
                <h2>DINSOS - PM</h2>
                <p>Kota Tarakan</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="bi bi-person-fill fs-5"></i>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="bi bi-lock-fill fs-5"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Dinas Sosial & Pemberdayaan Masyarakat Kota Tarakan</p>
                
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi sederhana untuk form
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });

        

// notif 
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        let alert = document.querySelector('.auto-close');
        if (alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000); // durasi 3 detik
});

    </script>
</body>
</html>