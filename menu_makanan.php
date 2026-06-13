<?php
session_start();
// Proteksi 1: Cek apakah sudah login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

// Proteksi 2: SPPG dilarang masuk ke halaman Menu (Hanya Admin Pusat)
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Hanya Admin Pusat yang boleh mengelola Menu Makanan.'); window.location.href='distribusi.php';</script>";
    exit();
}

include 'koneksi.php';

// ==========================================
// 1. LOGIKA PROSES TAMBAH DATA (CREATE)
// ==========================================
if (isset($_POST['tombol_simpan'])) {
    $nama_menu = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $hari      = mysqli_real_escape_string($koneksi, $_POST['hari']);

    if (!empty($nama_menu) && !empty($hari)) {
        $query_tambah = "INSERT INTO menu_makanan (nama_menu, hari) VALUES ('$nama_menu', '$hari')";
        if (mysqli_query($koneksi, $query_tambah)) {
            header("Location: menu_makanan.php?status=sukses_tambah");
            exit();
        }
    }
}

// ==========================================
// 2. LOGIKA PROSES EDIT DATA (UPDATE)
// ==========================================
if (isset($_POST['tombol_update'])) {
    $id_menu   = $_POST['id_menu'];
    $nama_menu = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $hari      = mysqli_real_escape_string($koneksi, $_POST['hari']);

    $query_update = "UPDATE menu_makanan SET nama_menu='$nama_menu', hari='$hari' WHERE id_menu='$id_menu'";
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: menu_makanan.php?status=sukses_update");
        exit();
    }
}

// ==========================================
// 3. LOGIKA PROSES HAPUS DATA (DELETE)
// ==========================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_menu = $_GET['id'];
    $query_hapus = "DELETE FROM menu_makanan WHERE id_menu='$id_menu'";
    if (mysqli_query($koneksi, $query_hapus)) {
        header("Location: menu_makanan.php?status=sukses_hapus");
        exit();
    }
}

// ==========================================
// 4. LOGIKA AMBIL DATA UNTUK TOMBOL EDIT
// ==========================================
$edit_mode = false; $edit_nama = ""; $edit_hari = ""; $edit_id = "";
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $edit_mode = true;
    $id_menu = $_GET['id'];
    $query_ambil_satu = "SELECT * FROM menu_makanan WHERE id_menu='$id_menu'";
    $hasil_satu = mysqli_query($koneksi, $query_ambil_satu);
    if ($row_edit = mysqli_fetch_assoc($hasil_satu)) {
        $edit_id   = $row_edit['id_menu'];
        $edit_nama = $row_edit['nama_menu'];
        $edit_hari = $row_edit['hari'];
    }
}

// ==========================================
// 5. AMBIL SEMUA DATA (ORDER BY ID ASC Agar Berurutan)
// ==========================================
$query_tampil = "SELECT * FROM menu_makanan ORDER BY id_menu ASC";
$data_menu = mysqli_query($koneksi, $query_tampil);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu - Aplikasi MBG</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f5f9; color: #334155; }
        
        /* HEADER BANNER - SEKARANG DENGAN AKSEN HIJAU */
        .header-banner { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            color: white; 
            padding: 20px 30px; 
            border-bottom: 5px solid #28a745; /* <--- Warna Merah diganti HIJAU Konsisten */
        }
        .header-banner h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
        
        .container { max-width: 1000px; margin: 25px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Navigasi Seragam */
        .nav a { text-decoration: none; padding: 10px 18px; background: #e2e8f0; color: #475569; border-radius: 6px; font-weight: bold; margin-right: 8px; transition: all 0.3s; display: inline-block; font-size: 14px; }
        .nav a:hover { background: #cbd5e1; }
        .nav a.active { background: #1e3c72; color: white; }
        
        form { margin: 30px 0; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        form h3 { margin-top: 0; color: #1e3c72; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; }
        
        input[type="text"], select { width: 100%; padding: 10px; box-sizing: border-box; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 14px; transition: all 0.3s; }
        input:focus, select:focus { border-color: #28a745; outline: none; box-shadow: 0 0 0 3px rgba(40,167,69,0.1); }
        
        .btn { padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px; transition: all 0.3s; }
        /* Tombol Simpan HIJAU */
        .btn-success { background: linear-gradient(to right, #28a745, #1e7e34); color: white; box-shadow: 0 3px 8px rgba(40,167,69,0.2); }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(40,167,69,0.3); }
        
        .btn-warning { background-color: #ffc107; color: #1e293b; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-secondary { background-color: #64748b; color: white; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; }
        th, td { padding: 14px; text-align: left; }
        th { background-color: #1e3c72; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tr { border-bottom: 1px solid #e2e8f0; background-color: #ffffff; }
        tr:nth-child(even) { background-color: #f8fafc; }
        
        .alert { background-color: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; border-left: 4px solid #22c55e; }
        
        .day-badge { background-color: #1e3c72; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

<div class="header-banner">
    <h1>MANAJEMEN PAKET MENU GIZI MBG</h1>
</div>

<div class="container">
    <div class="nav" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <div>
            <a href="sekolah.php">Data Sekolah</a>
            <a href="menu_makanan.php" class="active">Menu Makanan</a>
            <a href="distribusi.php">Distribusi Logistik</a>
        </div>
        <div style="font-size: 14px; color: #475569;">
            👤 User: <strong style="color: #1e3c72;"><?= $_SESSION['username']; ?></strong> | 
            <a href="logout.php" style="color: #dc3545; font-weight: bold; text-decoration: none;" onclick="return confirm('Yakin ingin logout?')">Keluar</a>
        </div>
    </div>

    <h2>Pengaturan Jadwal Variasi Menu</h2>
    
    <?php if (isset($_GET['status'])) : ?>
        <div class="alert">✅ Operasi Menu Makanan berhasil diperbarui ke sistem!</div>
    <?php endif; ?>

    <form action="menu_makanan.php" method="POST">
        <h3><?= $edit_mode ? "📝 Edit Rencana Menu" : "➕ Tambah Komponen Menu Baru"; ?></h3>
        
        <?php if ($edit_mode) : ?>
            <input type="hidden" name="id_menu" value="<?= $edit_id; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Hari Distribusi</label>
            <select name="hari" required>
                <option value="">-- Pilih Hari --</option>
                <?php 
                $hari_array = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                foreach ($hari_array as $h) {
                    $selected = ($edit_hari == $h) ? 'selected' : '';
                    echo "<option value='$h' $selected>$h</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Rincian Komponen Menu Gizi Seimbang</label>
            <input type="text" name="nama_menu" value="<?= $edit_nama; ?>" placeholder="Contoh: Nasi Putih, Sop Ayam, Tempe Goreng, Buah Pisang, Susu" required>
        </div>

        <?php if ($edit_mode) : ?>
            <button type="submit" name="tombol_update" class="btn btn-warning">Update Menu</button>
            <a href="menu_makanan.php" class="btn btn-secondary">Batal</a>
        <?php else : ?>
            <button type="submit" name="tombol_simpan" class="btn btn-success">Simpan Variasi Menu</button>
        <?php endif; ?>
    </form>

    <h3>Daftar Variasi Komponen Gizi Aktif</h3>
    <table>
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th width="15%">Hari</th>
                <th>Rincian Paket Menu Makanan</th>
                <th width="20%" style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($data_menu)) : ?>
            <tr>
                <td><strong><?= $row['id_menu']; ?></strong></td>
                <td><span class="day-badge"><?= $row['hari']; ?></span></td>
                <td><?= htmlspecialchars($row['nama_menu']); ?></td>
                <td style="text-align: center;">
                    <a href="menu_makanan.php?aksi=edit&id=<?= $row['id_menu']; ?>" class="btn btn-warning" style="padding: 4px 8px; font-size: 12px;">Edit</a>
                    <a href="menu_makanan.php?aksi=hapus&id=<?= $row['id_menu']; ?>" class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;" onclick="return confirm('Hapus menu hari ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>