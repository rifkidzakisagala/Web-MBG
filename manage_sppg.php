<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') { 
    header("Location: index.php"); 
    exit(); 
}
include 'koneksi.php';

if (isset($_POST['tombol_simpan'])) {
    $nama_sppg = mysqli_real_escape_string($koneksi, $_POST['nama_sppg']);
    $wilayah   = mysqli_real_escape_string($koneksi, $_POST['wilayah']);
    mysqli_query($koneksi, "INSERT INTO sppg (nama_sppg, wilayah) VALUES ('$nama_sppg', '$wilayah')");
    header("Location: manage_sppg.php"); 
    exit();
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM sppg WHERE id_sppg='$id'");
    header("Location: manage_sppg.php"); 
    exit();
}

// Sudah disesuaikan menjadi ASC (Berurutan dari angka 1)
$data_sppg = mysqli_query($koneksi, "SELECT * FROM sppg ORDER BY id_sppg ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Cabang SPPG</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f5f9; color: #334155; }
        .header-banner { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; border-bottom: 5px solid #28a745; }
        .header-banner h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .container { max-width: 900px; margin: 25px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Navigasi Utama Seragam */
        .nav a { text-decoration: none; padding: 10px 18px; background: #e2e8f0; color: #475569; border-radius: 6px; font-weight: bold; margin-right: 5px; transition: all 0.3s; display: inline-block; font-size: 13px; }
        .nav a:hover { background: #cbd5e1; }
        .nav a.active { background: #1e3c72; color: white; }
        
        /* Submenu khusus penunjuk halaman aktif */
        .submenu-container { background: #fff; padding: 15px; border-radius: 8px; border: 2px dashed #28a745; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; }
        .submenu-title { font-weight: bold; font-size: 14px; color: #1e3c72; }
        .btn-sub { text-decoration: none; padding: 8px 16px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.2s; display: inline-block; }
        .btn-sub.active { background: #28a745; color: white; border-color: #28a745; }
        .btn-sub:hover { background: #cbd5e1; color: #334155; }

        form { margin: 25px 0; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        form h3 { margin-top: 0; color: #1e3c72; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
        input[type="text"] { width: 100%; padding: 10px; box-sizing: border-box; border: 2px solid #e2e8f0; border-radius: 6px; margin-bottom: 12px; font-size: 14px; }
        .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; color: white; font-size: 13px; }
        .btn-success { background: linear-gradient(to right, #28a745, #1e7e34); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #1e3c72; color: white; text-transform: uppercase; font-size: 11px; }
    </style>
</head>
<body>
<div class="header-banner">
    <h1>KELOLA CABANG DISTRIBUSI SPPG</h1>
</div>
<div class="container">
    <div class="nav" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <div>
            <a href="sekolah.php">Data Sekolah</a>
            <a href="menu_makanan.php">Menu Makanan</a>
            <a href="distribusi.php" class="active">Distribusi Logistik</a>
        </div>
        <div style="font-size: 13px; color: #475569;">
            👤 Operator: <strong style="color: #1e3c72;"><?= $_SESSION['username']; ?></strong> | 
            <a href="logout.php" style="color: #dc3545; font-weight: bold; text-decoration: none;">Logout</a>
        </div>
    </div>

    <div class="submenu-container">
        <div class="submenu-title">⚙️ Navigasi Master Logistik:</div>
        <a href="manage_sppg.php" class="btn-sub active">🏢 Kelola Cabang SPPG</a>
        <a href="manage_driver.php" class="btn-sub">🏃‍♂️ Kelola Data Driver</a>
        <a href="distribusi.php" class="btn-sub" style="background: #64748b; color: white;">⬅️ Kembali ke Manifes</a>
    </div>

    <form action="" method="POST">
        <h3>➕ Tambah Cabang SPPG Baru</h3>
        <label style="display:block; margin-bottom:6px; font-weight:600; font-size:13px;">Nama Cabang SPPG</label>
        <input type="text" name="nama_sppg" placeholder="Contoh: SPPG Cabang Siumbut-umbut" required>
        <label style="display:block; margin-bottom:6px; font-weight:600; font-size:13px;">Wilayah Operasional</label>
        <input type="text" name="wilayah" placeholder="Contoh: Kec. Kisaran Timur" required>
        <button type="submit" name="tombol_simpan" class="btn btn-success">Simpan Cabang</button>
    </form>

    <h3>Daftar Satuan Pelayanan Terdaftar</h3>
    <table>
        <thead><tr><th>ID Cabang</th><th>Nama Cabang SPPG</th><th>Cakupan Wilayah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($data_sppg)) : ?>
            <tr>
                <td><strong><?= $row['id_sppg']; ?></strong></td>
                <td><span style="color: #1e3c72; font-weight: 700;"><?= htmlspecialchars($row['nama_sppg']); ?></span></td>
                <td><?= htmlspecialchars($row['wilayah']); ?></td>
                <td><a href="manage_sppg.php?aksi=hapus&id=<?= $row['id_sppg']; ?>" style="color:#dc3545; font-weight:bold; text-decoration:none;" onclick="return confirm('Hapus?')">Hapus</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>