<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') { 
    header("Location: index.php"); 
    exit(); 
}
include 'koneksi.php';

if (isset($_POST['tombol_simpan'])) {
    $nama_driver = mysqli_real_escape_string($koneksi, $_POST['nama_driver']);
    $no_hp       = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_sppg     = $_POST['id_sppg'];
    mysqli_query($koneksi, "INSERT INTO driver (nama_driver, no_hp, id_sppg) VALUES ('$nama_driver', '$no_hp', '$id_sppg')");
    header("Location: manage_driver.php"); 
    exit();
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM driver WHERE id_driver='$id'");
    header("Location: manage_driver.php"); 
    exit();
}

$list_sppg = mysqli_query($koneksi, "SELECT * FROM sppg ORDER BY nama_sppg ASC");
// Sudah disesuaikan menjadi ASC (Berurutan dari angka 1)
$data_driver = mysqli_query($koneksi, "SELECT d.*, s.nama_sppg FROM driver d LEFT JOIN sppg s ON d.id_sppg = s.id_sppg ORDER BY d.id_driver ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Data Driver</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f5f9; color: #334155; }
        .header-banner { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; border-bottom: 5px solid #28a745; }
        .header-banner h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .container { max-width: 950px; margin: 25px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
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
        input[type="text"], select { width: 100%; padding: 10px; box-sizing: border-box; border: 2px solid #e2e8f0; border-radius: 6px; margin-bottom: 12px; font-size: 14px; }
        .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; color: white; font-size: 13px; }
        .btn-success { background: linear-gradient(to right, #28a745, #1e7e34); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #1e3c72; color: white; text-transform: uppercase; font-size: 11px; }
    </style>
</head>
<body>
<div class="header-banner">
    <h1>MANAJEMEN TIM DRIVER / KURIR LOGISTIK</h1>
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
        <a href="manage_sppg.php" class="btn-sub">🏢 Kelola Cabang SPPG</a>
        <a href="manage_driver.php" class="btn-sub active">🏃‍♂️ Kelola Data Driver</a>
        <a href="distribusi.php" class="btn-sub" style="background: #64748b; color: white;">⬅️ Kembali ke Manifes</a>
    </div>

    <form action="" method="POST">
        <h3>➕ Daftarkan Driver Pengantar Baru</h3>
        <label style="display:block; margin-bottom:6px; font-weight:600; font-size:13px;">Nama Lengkap Driver</label>
        <input type="text" name="nama_driver" placeholder="Contoh: Rian Hidayat" required>
        <label style="display:block; margin-bottom:6px; font-weight:600; font-size:13px;">Nomor WhatsApp Aktif</label>
        <input type="text" name="no_hp" placeholder="Contoh: 0812xxxxxxxx" required>
        <label style="display:block; margin-bottom:6px; font-weight:600; font-size:13px;">Penempatan Pos Cabang SPPG</label>
        <select name="id_sppg" required>
            <option value="">-- Pilih Penempatan SPPG --</option>
            <?php while($sp = mysqli_fetch_assoc($list_sppg)) : ?>
                <option value="<?= $sp['id_sppg']; ?>"><?= $sp['nama_sppg']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="tombol_simpan" class="btn btn-success">Simpan Data Driver</button>
    </form>

    <h3>Armada Kurir Aktif Lapangan</h3>
    <table>
        <thead><tr><th>No</th><th>Nama Driver</th><th>Kontak No HP</th><th>Homebase SPPG</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php $n=1; while($row = mysqli_fetch_assoc($data_driver)) : ?>
            <tr>
                <td><?= $n++; ?></td>
                <td><strong><?= htmlspecialchars($row['nama_driver']); ?></strong></td>
                <td>🟢 <?= htmlspecialchars($row['no_hp']); ?></td>
                <td><span style="color:#1e3c72; font-weight:600;"><?= htmlspecialchars($row['nama_sppg'] ?? 'Belum Ditentukan'); ?></span></td>
                <td><a href="manage_driver.php?aksi=hapus&id=<?= $row['id_driver']; ?>" style="color:#dc3545; font-weight:bold; text-decoration:none;" onclick="return confirm('Hapus?')">Hapus</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>