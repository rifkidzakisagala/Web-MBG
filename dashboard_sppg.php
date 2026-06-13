<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'sppg') {
    header("Location: index.php");
    exit();
}
include 'koneksi.php';

$id_sppg = $_SESSION['id_sppg'];

// Proses Konfirmasi Pengiriman oleh SPPG
if (isset($_GET['aksi']) && $_GET['aksi'] == 'kirim') {
    $id_distribusi = $_GET['id'];
    mysqli_query($koneksi, "UPDATE distribusi SET status='Dikirim' WHERE id_distribusi='$id_distribusi' AND id_sppg='$id_sppg'");
    header("Location: dashboard_sppg.php?status=berhasil");
    exit();
}

// Ambil data distribusi khusus untuk cabang SPPG yang sedang login
$query = "SELECT d.*, s.nama_sekolah, s.jumlah_siswa, m.nama_menu, dr.nama_driver 
          FROM distribusi d
          LEFT JOIN sekolah s ON d.id_sekolah = s.id_sekolah
          LEFT JOIN menu_makanan m ON d.id_menu = m.id_menu
          LEFT JOIN driver dr ON d.id_driver = dr.id_driver
          WHERE d.id_sppg = '$id_sppg'
          ORDER BY d.tanggal DESC";
$data_logistik = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cabang SPPG - MBG</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            background-color: #f1f5f9; 
        }

        /* Header Premium Senada Dengan Aplikasi Utama */
        .header { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            color: white; 
            padding: 20px 30px; 
            border-bottom: 5px solid #28a745; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-logout {
            color: #ffc107; 
            font-weight: 700; 
            text-decoration: none;
            font-size: 14px;
            padding: 8px 16px;
            border: 1.5px solid #ffc107;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #ffc107;
            color: #1e3c72;
        }

        /* Container Utama */
        .container { 
            max-width: 1200px; 
            margin: 40px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(30, 60, 114, 0.05); 
        }

        .container h3 {
            color: #1e3c72;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
        }

        /* Desain Tabel Manifes Distribusi */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }

        th, td { 
            padding: 16px 14px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0; 
            font-size: 14px;
        }

        th { 
            background-color: #1e3c72; 
            color: white; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Desain FIX Tombol Konfirmasi Kirim (Anti-Penyok) */
        .btn-kirim { 
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); 
            color: white; 
            padding: 8px 16px; 
            text-decoration: none; 
            font-weight: bold; 
            border-radius: 6px; 
            font-size: 12px; 
            white-space: nowrap; /* KUNCI UTAMA: Teks dipaksa lurus dan tidak patah ke bawah */
            box-shadow: 0 3px 6px rgba(14, 165, 233, 0.2);
            transition: all 0.2s ease;
        }

        .btn-kirim:hover {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
        }

        /* Ruang Aman Kolam Aksi */
        td:last-child {
            min-width: 160px;
            vertical-align: middle;
        }

        /* Desain Badges Status */
        .badge { 
            display: inline-block;
            padding: 6px 14px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 700; 
            color: white; 
            text-align: center;
        }
        
        .bg-proses { background-color: #eab308; color: #1e293b; }
        .bg-kirim { background-color: #0ea5e9; }
        .bg-terima { background-color: #22c55e; }
    </style>
</head>
<body>

<div class="header">
    <h2>🏢 OPERASIONAL DAPUR: <?= htmlspecialchars($_SESSION['username']); ?></h2>
    <a href="logout.php" class="btn-logout">LOGOUT</a>
</div>

<div class="container">
    <h3>Perintah Distribusi Dapur Masuk</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 120px;">Tanggal</th>
                <th>Sekolah Tujuan (Porsi)</th>
                <th>Paket Menu</th>
                <th>Driver Penanggungjawab</th>
                <th style="text-align: center; width: 120px;">Status</th>
                <th style="text-align: center; width: 180px;">Aksi Konfirmasi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($data_logistik) == 0) {
                echo "<tr><td colspan='6' style='text-align:center; color:#64748b; padding: 30px;'>Belum ada data perintah distribusi logistik untuk dapur ini.</td></tr>";
            }
            
            while($row = mysqli_fetch_assoc($data_logistik)) : 
                $badge = ($row['status'] == 'Diproses') ? 'bg-proses' : (($row['status'] == 'Dikirim') ? 'bg-kirim' : 'bg-terima');
            ?>
            <tr>
                <td><strong><?= date('d/m/Y', strtotime($row['tanggal'])); ?></strong></td>
                <td>
                    <span style="color: #1e3c72; font-weight: bold; font-size: 15px;"><?= htmlspecialchars($row['nama_sekolah']); ?></span><br>
                    <small style="color: #64748b; font-weight: 600;">👥 Muatan: <?= $row['jumlah_siswa']; ?> Porsi</small>
                </td>
                <td style="color: #334155; line-height: 1.4;"><?= htmlspecialchars($row['nama_menu']); ?></td>
                <td style="font-weight: 500; color: #475569;">🏃‍♂️ <?= htmlspecialchars($row['nama_driver']); ?></td>
                <td style="text-align: center;"><span class="badge <?= $badge; ?>"><?= $row['status']; ?></span></td>
                <td style="text-align: center;">
                    <?php if($row['status'] == 'Diproses') : ?>
                        <a href="dashboard_sppg.php?aksi=kirim&id=<?= $row['id_distribusi']; ?>" class="btn-kirim" onclick="return confirm('Konfirmasi bahwa logistik makanan sudah siap dan diserahkan ke driver?')">
                            🚚 Kirim Logistik
                        </a>
                    <?php else: ?>
                        <span style="color:#22c55e; font-size:13px; font-weight: 700;">✅ Dikonfirmasi</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>