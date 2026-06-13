<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'driver') {
    header("Location: index.php");
    exit();
}
include 'koneksi.php';

$id_driver = $_SESSION['id_driver'];

// Proses Konfirmasi Penerimaan oleh Driver di Sekolah
if (isset($_GET['aksi']) && $_GET['aksi'] == 'terima') {
    $id_distribusi = $_GET['id'];
    mysqli_query($koneksi, "UPDATE distribusi SET status='Diterima' WHERE id_distribusi='$id_distribusi' AND id_driver='$id_driver'");
    header("Location: dashboard_driver.php?status=selesai");
    exit();
}

// Ambil manifes pengiriman khusus untuk driver yang sedang bertugas
$query = "SELECT d.*, s.nama_sekolah, s.jumlah_siswa, m.nama_menu, sp.nama_sppg 
          FROM distribusi d
          LEFT JOIN sekolah s ON d.id_sekolah = s.id_sekolah
          LEFT JOIN menu_makanan m ON d.id_menu = m.id_menu
          LEFT JOIN sppg sp ON d.id_sppg = sp.id_sppg
          WHERE d.id_driver = '$id_driver'
          ORDER BY d.tanggal DESC";
$data_tugas = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Driver Lapangan</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f5f9; }
        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 15px; border-bottom: 5px solid #28a745; display: flex; justify-content: space-between; align-items: center;}
        .container { max-width: 600px; margin: 15px auto; padding: 10px; }
        .card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 15px; border-left: 5px solid #1e3c72; }
        .btn-terima { display: block; text-align: center; background: #22c55e; color: white; padding: 10px; text-decoration: none; font-weight: bold; border-radius: 6px; margin-top: 10px; font-size: 14px; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; color: white; }
        .bg-proses { background-color: #eab308; color: #1e293b; }
        .bg-kirim { background-color: #0ea5e9; }
        .bg-terima { background-color: #22c55e; }
    </style>
</head>
<body>
<div class="header">
    <div style="font-size:14px;">🏃‍♂️ Driver: <strong><?= $_SESSION['username']; ?></strong></div>
    <a href="logout.php" style="color: #ffc107; font-weight: bold; text-decoration: none; font-size: 13px;">LOGOUT</a>
</div>
<div class="container">
    <h3 style="color:#1e3c72; padding-left: 5px;">Manifes Pengantaran Anda</h3>
    
    <?php if(mysqli_num_rows($data_tugas) == 0) echo "<p style='text-align:center; color:#64748b;'>Belum ada manifes tugas hari ini.</p>"; ?>

    <?php while($row = mysqli_fetch_assoc($data_tugas)) : 
        $badge = ($row['status'] == 'Diproses') ? 'bg-proses' : (($row['status'] == 'Dikirim') ? 'bg-kirim' : 'bg-terima');
    ?>
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 12px; color: #64748b;">📅 <?= date('d/m/Y', strtotime($row['tanggal'])); ?></span>
            <span class="badge <?= $badge; ?>"><?= $row['status']; ?></span>
        </div>
        <div style="font-size: 16px; font-weight: bold; color: #1e3c72;"><?= $row['nama_sekolah']; ?></div>
        <div style="font-size: 13px; margin: 4px 0;">📦 Muatan: <strong><?= $row['nama_menu']; ?> (<?= $row['jumlah_siswa']; ?> Porsi)</strong></div>
        <div style="font-size: 13px; color:#475569;">🏢 Asal Dapur: <?= $row['nama_sppg']; ?></div>
        
        <?php if($row['status'] == 'Dikirim') : ?>
            <a href="dashboard_driver.php?aksi=terima&id=<?= $row['id_distribusi']; ?>" class="btn-terima" onclick="return confirm('Nyatakan bahwa makanan telah resmi diserahterimakan ke pihak sekolah?')">✅ Selesai Diantar</a>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>
</body>
</html>