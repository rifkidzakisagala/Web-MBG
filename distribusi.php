<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}
include 'koneksi.php';

if (isset($_POST['tombol_simpan'])) {
    $id_sekolah = $_POST['id_sekolah'];
    $id_menu    = $_POST['id_menu'];
    $id_driver  = $_POST['id_driver'];
    $id_sppg    = $_POST['id_sppg'];
    $tanggal    = $_POST['tanggal'];
    $status     = $_POST['status'];
    
    if (!empty($id_sekolah) && !empty($id_menu) && !empty($id_driver) && !empty($id_sppg)) {
        $query_tambah = "INSERT INTO distribusi (id_sekolah, id_menu, id_driver, id_sppg, tanggal, status) 
                         VALUES ('$id_sekolah', '$id_menu', '$id_driver', '$id_sppg', '$tanggal', '$status')";
        if (mysqli_query($koneksi, $query_tambah)) {
            header("Location: distribusi.php?status=sukses_tambah");
            exit();
        }
    }
}

if (isset($_POST['tombol_update'])) {
    $id_distribusi = $_POST['id_distribusi'];
    $id_sekolah    = $_POST['id_sekolah'];
    $id_menu       = $_POST['id_menu'];
    $id_driver     = $_POST['id_driver'];
    $id_sppg       = $_POST['id_sppg'];
    $tanggal       = $_POST['tanggal'];
    $status        = $_POST['status'];
    
    $query_update = "UPDATE distribusi SET id_sekolah='$id_sekolah', id_menu='$id_menu', id_driver='$id_driver', 
                     id_sppg='$id_sppg', tanggal='$tanggal', status='$status' WHERE id_distribusi='$id_distribusi'";
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: distribusi.php?status=sukses_update");
        exit();
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_distribusi = $_GET['id'];
    $query_hapus = "DELETE FROM distribusi WHERE id_distribusi='$id_distribusi'";
    if (mysqli_query($koneksi, $query_hapus)) {
        header("Location: distribusi.php?status=sukses_hapus");
        exit();
    }
}

$edit_mode = false; $edit_id_sekolah = ""; $edit_id_menu = ""; $edit_id_driver = ""; $edit_id_sppg = ""; $edit_tanggal = ""; $edit_status = ""; $edit_id = "";
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $edit_mode = true;
    $id_distribusi = $_GET['id'];
    $query_ambil_satu = "SELECT * FROM distribusi WHERE id_distribusi='$id_distribusi'";
    $hasil_satu = mysqli_query($koneksi, $query_ambil_satu);
    if ($row_edit = mysqli_fetch_assoc($hasil_satu)) {
        $edit_id         = $row_edit['id_distribusi'];
        $edit_id_sekolah = $row_edit['id_sekolah'];
        $edit_id_menu    = $row_edit['id_menu'];
        $edit_id_driver  = $row_edit['id_driver'];
        $edit_id_sppg    = $row_edit['id_sppg'];
        $edit_tanggal    = $row_edit['tanggal'];
        $edit_status     = $row_edit['status'];
    }
}

$list_sekolah = mysqli_query($koneksi, "SELECT id_sekolah, nama_sekolah FROM sekolah ORDER BY nama_sekolah ASC");
$list_menu    = mysqli_query($koneksi, "SELECT id_menu, nama_menu, hari FROM menu_makanan ORDER BY hari ASC");
$list_driver  = mysqli_query($koneksi, "SELECT id_driver, nama_driver FROM driver ORDER BY nama_driver ASC");
$list_sppg    = mysqli_query($koneksi, "SELECT id_sppg, nama_sppg FROM sppg ORDER BY nama_sppg ASC");

$query_tampil = "SELECT d.*, s.nama_sekolah, s.jumlah_siswa, m.nama_menu, dr.nama_driver, sp.nama_sppg 
                 FROM distribusi d
                 LEFT JOIN sekolah s ON d.id_sekolah = s.id_sekolah
                 LEFT JOIN menu_makanan m ON d.id_menu = m.id_menu
                 LEFT JOIN driver dr ON d.id_driver = dr.id_driver
                 LEFT JOIN sppg sp ON d.id_sppg = sp.id_sppg
                 ORDER BY d.tanggal DESC, d.id_distribusi DESC";
$data_distribusi = mysqli_query($koneksi, $query_tampil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistik & Distribusi Terintegrasi MBG</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f5f9; color: #334155; }
        .header-banner { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; border-bottom: 5px solid #28a745; }
        .header-banner h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .container { max-width: 1200px; margin: 25px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Navigasi Utama Atas */
        .nav a { text-decoration: none; padding: 10px 18px; background: #e2e8f0; color: #475569; border-radius: 6px; font-weight: bold; margin-right: 5px; transition: all 0.3s; display: inline-block; font-size: 13px; }
        .nav a:hover { background: #cbd5e1; }
        .nav a.active { background: #1e3c72; color: white; }
        
        /* Submenu khusus SPPG & Driver di dalam halaman */
        .submenu-container { background: #fff; padding: 15px; border-radius: 8px; border: 2px dashed #cbd5e1; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; }
        .submenu-title { font-weight: bold; font-size: 14px; color: #1e3c72; }
        .btn-sub { text-decoration: none; padding: 8px 16px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.2s; display: inline-block; }
        .btn-sub:hover { background: #28a745; color: white; border-color: #28a745; transform: translateY(-1px); }

        form { margin: 30px 0; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        form h3 { margin-top: 0; color: #1e3c72; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
        .grid-form { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 12px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; }
        select, input[type="date"] { width: 100%; padding: 10px; box-sizing: border-box; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 14px; }
        select:focus, input:focus { border-color: #28a745; outline: none; }
        .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; font-size: 13px; }
        .btn-success { background: linear-gradient(to right, #28a745, #1e7e34); color: white; }
        .btn-warning { background-color: #ffc107; color: #1e293b; }
        .btn-danger { background-color: #dc3545; color: white; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; color: white; display: inline-block; }
        .bg-proses { background-color: #eab308; color: #1e293b; }
        .bg-kirim { background-color: #0ea5e9; }
        .bg-terima { background-color: #22c55e; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #1e3c72; color: white; text-transform: uppercase; font-size: 11px; }
        .alert { background-color: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="header-banner">
    <h1>MONITORING LOGISTIK MBG NASIONAL</h1>
</div>
<div class="container">
    <div class="nav" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <div>
            <?php if ($_SESSION['role'] == 'admin') : ?>
                <a href="sekolah.php">Data Sekolah</a>
                <a href="menu_makanan.php">Menu Makanan</a>
            <?php endif; ?>
            <a href="distribusi.php" class="active">Distribusi Logistik</a>
        </div>
        <div style="font-size: 13px; color: #475569;">
            👤 Operator: <strong style="color: #1e3c72;"><?= $_SESSION['username']; ?></strong> | 
            <a href="logout.php" style="color: #dc3545; font-weight: bold; text-decoration: none;" onclick="return confirm('Logout?')">Logout</a>
        </div>
    </div>

    <?php if ($_SESSION['role'] == 'admin') : ?>
        <div class="submenu-container">
            <div class="submenu-title">⚙️ Pengaturan Data Master Logistik:</div>
            <a href="manage_sppg.php" class="btn-sub">🏢 Kelola Cabang SPPG</a>
            <a href="manage_driver.php" class="btn-sub">🏃‍♂️ Kelola Data Driver</a>
        </div>
    <?php endif; ?>

    <h2>Alur Distribusi Terintegrasi Terkini</h2>
    <?php if (isset($_GET['status'])) : ?><div class="alert">Aksi database sukses dijalankan!</div><?php endif; ?>

    <form action="distribusi.php" method="POST">
        <h3><?= $edit_mode ? "📝 Edit Manifes Pengiriman" : "➕ Daftarkan Distribusi Logistik Baru"; ?></h3>
        <?php if ($edit_mode) : ?><input type="hidden" name="id_distribusi" value="<?= $edit_id; ?>"><?php endif; ?>
        
        <div class="grid-form">
            <div class="form-group">
                <label>Sekolah Tujuan</label>
                <select name="id_sekolah" required>
                    <option value="">-- Pilih Sekolah --</option>
                    <?php while($s = mysqli_fetch_assoc($list_sekolah)) : ?>
                        <option value="<?= $s['id_sekolah']; ?>" <?= ($edit_id_sekolah == $s['id_sekolah']) ? 'selected' : ''; ?>><?= $s['nama_sekolah']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Paket Menu Gizi</label>
                <select name="id_menu" required>
                    <option value="">-- Pilih Menu --</option>
                    <?php while($m = mysqli_fetch_assoc($list_menu)) : ?>
                        <option value="<?= $m['id_menu']; ?>" <?= ($edit_id_menu == $m['id_menu']) ? 'selected' : ''; ?>>[<?= $m['hari']; ?>] <?= $m['nama_menu']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Cabang SPPG Pengirim</label>
                <select name="id_sppg" required>
                    <option value="">-- Pilih Cabang SPPG --</option>
                    <?php while($sp = mysqli_fetch_assoc($list_sppg)) : ?>
                        <option value="<?= $sp['id_sppg']; ?>" <?= ($edit_id_sppg == $sp['id_sppg']) ? 'selected' : ''; ?>><?= $sp['nama_sppg']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="grid-form">
            <div class="form-group">
                <label>Driver Pengantar</label>
                <select name="id_driver" required>
                    <option value="">-- Pilih Driver --</option>
                    <?php while($d = mysqli_fetch_assoc($list_driver)) : ?>
                        <option value="<?= $d['id_driver']; ?>" <?= ($edit_id_driver == $d['id_driver']) ? 'selected' : ''; ?>><?= $d['nama_driver']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal Pengiriman</label>
                <input type="date" name="tanggal" value="<?= $edit_tanggal; ?>" required>
            </div>
            <div class="form-group">
                <label>Status Logistik</label>
                <select name="status" required>
                    <option value="Diproses" <?= ($edit_status == 'Diproses') ? 'selected' : ''; ?>>🟡 Diproses Dapur</option>
                    <option value="Dikirim" <?= ($edit_status == 'Dikirim') ? 'selected' : ''; ?>>🔵 Sedang Dikirim</option>
                    <option value="Diterima" <?= ($edit_status == 'Diterima') ? 'selected' : ''; ?>>🟢 Sudah Diterima</option>
                </select>
            </div>
        </div>

        <button type="submit" name="<?= $edit_mode ? 'tombol_update' : 'tombol_simpan'; ?>" class="btn btn-success">
            <?= $edit_mode ? 'Update Manifes' : 'Daftarkan Pengiriman'; ?>
        </button>
    </form>

    <h3>Tabel Monitoring Integrasi Sistem</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Asal SPPG</th>
                <th>Sekolah Penerima (Porsi)</th>
                <th>Menu Makanan</th>
                <th>Driver / Kurir</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data_distribusi)) : 
                $badge = "bg-proses";
                if ($row['status'] == 'Dikirim') $badge = "bg-kirim";
                if ($row['status'] == 'Diterima') $badge = "bg-terima";
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><strong style="color: #0ea5e9;"><?= $row['nama_sppg'] ?? 'Global'; ?></strong></td>
                <td><strong><?= htmlspecialchars($row['nama_sekolah']); ?></strong> (<?= $row['jumlah_siswa']; ?>)</td>
                <td><?= htmlspecialchars($row['nama_menu']); ?></td>
                <td>🏃‍♂️ <?= htmlspecialchars($row['nama_driver'] ?? 'Belum Ditunjuk'); ?></td>
                <td style="text-align: center;"><span class="badge <?= $badge; ?>"><?= $row['status']; ?></span></td>
                <td style="text-align: center;">
                    <a href="distribusi.php?aksi=edit&id=<?= $row['id_distribusi']; ?>" class="btn btn-warning" style="padding:4px 8px; font-size:11px;">Edit</a>
                    <?php if ($_SESSION['role'] == 'admin') : ?>
                        <a href="distribusi.php?aksi=hapus&id=<?= $row['id_distribusi']; ?>" class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="return confirm('Hapus?')">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>