<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Katering tidak boleh mengelola data ini.'); window.location.href='distribusi.php';</script>";
    exit();
}
include 'koneksi.php';

if (isset($_POST['tombol_simpan'])) {
    $nama_sekolah = mysqli_real_escape_string($koneksi, $_POST['nama_sekolah']);
    $jumlah_siswa = $_POST['jumlah_siswa'];
    if (!empty($nama_sekolah) && !empty($jumlah_siswa)) {
        $query_tambah = "INSERT INTO sekolah (nama_sekolah, jumlah_siswa) VALUES ('$nama_sekolah', '$jumlah_siswa')";
        if (mysqli_query($koneksi, $query_tambah)) {
            header("Location: sekolah.php?status=sukses_tambah");
            exit();
        }
    }
}

if (isset($_POST['tombol_update'])) {
    $id_sekolah   = $_POST['id_sekolah'];
    $nama_sekolah = mysqli_real_escape_string($koneksi, $_POST['nama_sekolah']);
    $jumlah_siswa = $_POST['jumlah_siswa'];
    $query_update = "UPDATE sekolah SET nama_sekolah='$nama_sekolah', jumlah_siswa='$jumlah_siswa' WHERE id_sekolah='$id_sekolah'";
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: sekolah.php?status=sukses_update");
        exit();
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_sekolah = $_GET['id'];
    $query_hapus = "DELETE FROM sekolah WHERE id_sekolah='$id_sekolah'";
    if (mysqli_query($koneksi, $query_hapus)) {
        header("Location: sekolah.php?status=sukses_hapus");
        exit();
    }
}

$edit_mode = false; $edit_nama = ""; $edit_jumlah = ""; $edit_id = "";
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $edit_mode = true;
    $id_sekolah = $_GET['id'];
    $query_ambil_satu = "SELECT * FROM sekolah WHERE id_sekolah='$id_sekolah'";
    $hasil_satu = mysqli_query($koneksi, $query_ambil_satu);
    if ($row_edit = mysqli_fetch_assoc($hasil_satu)) {
        $edit_id     = $row_edit['id_sekolah'];
        $edit_nama   = $row_edit['nama_sekolah'];
        $edit_jumlah = $row_edit['jumlah_siswa'];
    }
}

$query_tampil = "SELECT * FROM sekolah ORDER BY id_sekolah DESC";
$data_sekolah = mysqli_query($koneksi, $query_tampil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Sekolah - Aplikasi MBG</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f5f9; color: #334155; }
        .header-banner { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; border-bottom: 5px solid #28a745; }
        .header-banner h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
        .container { max-width: 1000px; margin: 25px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .nav a { text-decoration: none; padding: 10px 18px; background: #e2e8f0; color: #475569; border-radius: 6px; font-weight: bold; margin-right: 8px; transition: all 0.3s; display: inline-block; }
        .nav a:hover { background: #cbd5e1; }
        .nav a.active { background: #1e3c72; color: white; }
        form { margin: 30px 0; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        form h3 { margin-top: 0; color: #1e3c72; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 10px; box-sizing: border-box; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 14px; transition: all 0.3s; }
        input:focus { border-color: #28a745; outline: none; box-shadow: 0 0 0 3px rgba(40,167,69,0.1); }
        .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px; transition: all 0.3s; }
        .btn-success { background: linear-gradient(to right, #28a745, #1e7e34); color: white; box-shadow: 0 3px 8px rgba(40,167,69,0.2); }
        .btn-success:hover { transform: translateY(-1px); }
        .btn-warning { background-color: #ffc107; color: #1e293b; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-secondary { background-color: #64748b; color: white; }
        .btn:hover { opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        th, td { padding: 14px; text-align: left; }
        th { background-color: #1e3c72; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tr { border-bottom: 1px solid #e2e8f0; background-color: #ffffff; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .alert { background-color: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; border-left: 4px solid #22c55e; }
    </style>
</head>
<body>
<div class="header-banner">
    <h1>PANEL MANAGEMENT MBG PUSAT</h1>
</div>
<div class="container">
    <div class="nav" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <div>
            <a href="sekolah.php" class="active">Data Sekolah</a>
            <a href="menu_makanan.php">Menu Makanan</a>
            <a href="distribusi.php">Distribusi Logistik</a>
        </div>
        <div style="font-size: 14px; color: #475569;">
            👤 User: <strong style="color: #1e3c72;"><?= $_SESSION['username']; ?></strong> | 
            <a href="logout.php" style="color: #dc3545; font-weight: bold; text-decoration: none;" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
        </div>
    </div>
    <h2>Manajemen Data Sekolah Mitra</h2>
    <?php if (isset($_GET['status'])) : ?>
        <div class="alert">Operasi database sukses dijalankan!</div>
    <?php endif; ?>
    <form action="sekolah.php" method="POST">
        <h3><?= $edit_mode ? "📝 Edit Data Sekolah" : "➕ Tambah Sekolah Baru"; ?></h3>
        <?php if ($edit_mode) : ?><input type="hidden" name="id_sekolah" value="<?= $edit_id; ?>"><?php endif; ?>
        <div class="form-group">
            <label>Nama Sekolah</label>
            <input type="text" name="nama_sekolah" value="<?= $edit_nama; ?>" placeholder="Contoh: SDN 01 Merdeka" required>
        </div>
        <div class="form-group">
            <label>Jumlah Siswa (Porsi)</label>
            <input type="number" name="jumlah_siswa" value="<?= $edit_jumlah; ?>" placeholder="Contoh: 150" min="1" required>
        </div>
        <?php if ($edit_mode) : ?>
            <button type="submit" name="tombol_update" class="btn btn-warning">Update Data</button>
            <a href="sekolah.php" class="btn btn-secondary">Batal</a>
        <?php else : ?>
            <button type="submit" name="tombol_simpan" class="btn btn-success">Simpan Data</button>
        <?php endif; ?>
    </form>
    <h3>Daftar Sekolah Penerima Manfaat</h3>
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th>Nama Sekolah</th>
                <th width="25%">Jumlah Siswa / Porsi</th>
                <th width="22%" style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data_sekolah)) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><strong><?= htmlspecialchars($row['nama_sekolah']); ?></strong></td>
                <td><span style="color: #475569; font-weight: 600;"><?= number_format($row['jumlah_siswa']); ?></span> anak</td>
                <td style="text-align: center;">
                    <a href="sekolah.php?aksi=edit&id=<?= $row['id_sekolah']; ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <a href="sekolah.php?aksi=hapus&id=<?= $row['id_sekolah']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Hapus sekolah ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>