<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_mahasiswa';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) { die('Koneksi gagal: ' . $mysqli->connect_error); }

$action = $_POST['action'] ?? null;
if ($action === 'add_mahasiswa') {
  $stmt = $mysqli->prepare('INSERT INTO mahasiswa (nim,nama,jurusan,angkatan,umur) VALUES (?,?,?,?,?)');
  $stmt->bind_param('sssis', $_POST['nim'], $_POST['nama'], $_POST['jurusan'], $_POST['angkatan'], $_POST['umur']);
  $stmt->execute();
}
if ($action === 'add_nilai') {
  $stmt = $mysqli->prepare('INSERT INTO nilai (nim,mata_kuliah,nilai,kd_dosen,kd_ruang) VALUES (?,?,?,?,?)');
  $stmt->bind_param('ssiss', $_POST['nim_n'], $_POST['mata_kuliah'], $_POST['nilai'], $_POST['kd_dosen'], $_POST['kd_ruang']);
  $stmt->execute();
}
header('Location: tampil.php');
?>