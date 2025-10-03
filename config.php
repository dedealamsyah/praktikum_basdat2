<?php
// Konfigurasi database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';   // ganti jika ada password MySQL
$db_name = 'db_mahasiswa';

// Koneksi database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Jika gagal koneksi
if ($mysqli->connect_errno) {
    die('Koneksi gagal: ' . $mysqli->connect_error);
}
?>