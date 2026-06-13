# 🏢 Aplikasi Manajemen Distribusi MBG (Makanan Bergizi Gratis)

Aplikasi berbasis web ini dirancang untuk mengelola dan memantau proses distribusi program Makanan Bergizi Gratis (MBG) secara sistematis dan real-time. Sistem ini mendukung arsitektur kontrol Multi-Role untuk memastikan integrasi data yang aman antara pihak manajemen pusat, dapur produksi, dan armada pengiriman di lapangan.

## 👥 Hak Akses & Fitur Utama (Multi-Role)
Aplikasi ini membagi hak akses pengguna ke dalam 3 entitas utama:
1. **Admin / Operator Pusat (`admin`):** Memiliki kontrol penuh atas pengelolaan master data sekolah, manajemen paket menu gizi, pengelolaan data driver, serta pembuatan perintah manifes distribusi logistik utama.
2. **Petugas Dapur Cabang (`sppg`):** Berfungsi memantau perintah memasak yang masuk khusus untuk cabangnya dan memberikan konfirmasi siap kirim jika logistik makanan telah selesai diproduksi.
3. **Kurir Lapangan (`driver`):** Membantu driver memantau daftar sekolah tujuan pengantaran, detail jumlah porsi muatan, dan memberikan konfirmasi penyelesaian distribusi setelah makanan sampai di lokasi.

## 🛠️ Teknologi yang Digunakan
* **Bahasa Pemrograman:** PHP
* **Database:** MySQL / MariaDB (Ekstensi `mysqli`)
* **Desain UI:** Custom CSS (Responsive & Modern Gateway Design)
* **Server Lokal:** XAMPP (Apache v3.x)