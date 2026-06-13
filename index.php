<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; 

    // 1. Cek keberadaan username di semua tabel terlebih dahulu
    $cek_user_sppg   = mysqli_query($koneksi, "SELECT * FROM sppg WHERE username='$username'");
    $cek_user_driver = mysqli_query($koneksi, "SELECT * FROM driver WHERE username='$username'");
    $cek_user_admin  = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username'");

    // Jika username tidak ditemukan di ketiga tabel tersebut
    if (mysqli_num_rows($cek_user_sppg) == 0 && mysqli_num_rows($cek_user_driver) == 0 && mysqli_num_rows($cek_user_admin) == 0) {
        $error_type = "username_tidak_ada";
    } else {
        // 2. Jika username ADA, baru kita validasi password-nya masing-masing
        
        // Pengecekan ke tabel SPPG
        $cek_sppg = mysqli_query($koneksi, "SELECT * FROM sppg WHERE username='$username' AND password='$password'");
        if ($cek_sppg && mysqli_num_rows($cek_sppg) > 0) {
            $data = mysqli_fetch_assoc($cek_sppg);
            $_SESSION['login']    = true;
            $_SESSION['username'] = $data['nama_sppg'];
            $_SESSION['id_sppg']   = $data['id_sppg'];
            $_SESSION['role']     = 'sppg';
            header("Location: dashboard_sppg.php");
            exit();
        }

        // Pengecekan ke tabel Driver
        $cek_driver = mysqli_query($koneksi, "SELECT * FROM driver WHERE username='$username' AND password='$password'");
        if ($cek_driver && mysqli_num_rows($cek_driver) > 0) {
            $data = mysqli_fetch_assoc($cek_driver);
            $_SESSION['login']    = true;
            $_SESSION['username'] = $data['nama_driver'];
            $_SESSION['id_driver'] = $data['id_driver'];
            $_SESSION['role']     = 'driver';
            header("Location: dashboard_driver.php");
            exit();
        }

        // Pengecekan ke tabel Admin utama
        $cek_admin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
        if ($cek_admin && mysqli_num_rows($cek_admin) > 0) {
            $data = mysqli_fetch_assoc($cek_admin);
            $_SESSION['login']    = true;
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = 'admin';
            header("Location: distribusi.php"); 
            exit();
        }

        // Jika lolos kondisi di atas (usernamenya ada tapi password salah)
        $error_type = "password_salah";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Gateway - Aplikasi MBG</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }
        
        body { 
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card { 
            background: white; 
            padding: 40px 35px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.08); 
            border-top: 6px solid #1e3c72; 
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        /* Desain Logo Header MBG */
        .brand-header {
            margin-bottom: 30px;
        }

        .brand-logo { 
            font-size: 36px; 
            font-weight: 900; 
            letter-spacing: 2px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 6px;
            margin-bottom: 0;
        }

        /* Input Form Group */
        .form-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input { 
            width: 100%; 
            padding: 14px 16px; 
            border: 2px solid #e2e8f0; 
            border-radius: 10px; 
            font-size: 15px;
            color: #334155;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #2a5298;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
        }

        /* Tombol Masuk Desain Modern */
        button { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); 
            border: none; 
            color: white; 
            font-weight: 700; 
            font-size: 15px;
            letter-spacing: 0.5px;
            border-radius: 10px; 
            cursor: pointer; 
            box-shadow: 0 4px 12px rgba(40, 167, 105, 0.2);
            transition: all 0.3s ease;
            margin-top: 5px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 105, 0.3);
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }

        button:active {
            transform: translateY(0);
        }
        
        /* Notifikasi Alert Modis */
        .error-msg { 
            padding: 12px 15px; 
            border-radius: 10px; 
            text-align: center; 
            margin-bottom: 25px; 
            font-size: 13px; 
            font-weight: 600; 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: shake 0.4s ease-in-out;
        }

        .bg-danger-light { 
            background-color: #fee2e2; 
            color: #dc3545; 
            border: 1px solid #fca5a5; 
        }

        .bg-warning-light { 
            background-color: #fef9c3; 
            color: #854d0e; 
            border: 1px solid #fde047; 
        }

        /* Efek Getar Saat Salah Input */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="brand-header">
            <h1 class="brand-logo">GATEWAY MBG</h1>
            <p class="brand-subtitle">Makanan Bergizi Gratis</p>
        </div>
        
        <?php if (isset($error_type) && $error_type == "username_tidak_ada") : ?>
            <div class="error-msg bg-danger-light">❌ Username tidak terdaftar!</div>
        <?php elseif (isset($error_type) && $error_type == "password_salah") : ?>
            <div class="error-msg bg-warning-light">⚠️ Username ditemukan, tapi password salah!</div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan nama pengguna" autocomplete="off" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan kata sandi" required>
            </div>
            
            <button type="submit" name="login">MASUK KE SISTEM</button>
        </form>
    </div>
</div>

</body>
</html>