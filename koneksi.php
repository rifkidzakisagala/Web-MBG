<?php
$host     = "localhost";
$user     = "root";
$password = ""; // Kosongkan jika menggunakan XAMPP bawaan
$database = "mbg"; // Sudah diubah sesuai database kamu

$koneksi = mysqli_connect($host, $user, $password, $database);

// Periksa apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>