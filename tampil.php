<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_mahasiswa';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) { die('Koneksi gagal: ' . $mysqli->connect_error); }

$result = $mysqli->query('SELECT * FROM mahasiswa');
$resNilai = $mysqli->query('SELECT n.id,n.nim,m.nama,n.mata_kuliah,n.nilai,d.nama_dosen,r.nama_ruang
  FROM nilai n LEFT JOIN mahasiswa m ON n.nim=m.nim
  LEFT JOIN dosen d ON n.kd_dosen=d.kd_dosen
  LEFT JOIN ruang_kelas r ON n.kd_ruang=r.kd_ruang');
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Data Tersimpan</title></head>
<body>
<h1>Data Mahasiswa</h1>
<table border="1" cellpadding="5"><tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Angkatan</th><th>Umur</th></tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr><td><?= $row['nim'] ?></td><td><?= $row['nama'] ?></td><td><?= $row['jurusan'] ?></td><td><?= $row['angkatan'] ?></td><td><?= $row['umur'] ?></td></tr>
<?php endwhile; ?>
</table>

<h1>Data Nilai</h1>
<table border="1" cellpadding="5"><tr><th>ID</th><th>NIM</th><th>Nama</th><th>Mata Kuliah</th><th>Nilai</th><th>Dosen</th><th>Ruang</th></tr>
<?php while($r=$resNilai->fetch_assoc()): ?>
<tr><td><?= $r['id'] ?></td><td><?= $r['nim'] ?></td><td><?= $r['nama'] ?></td><td><?= $r['mata_kuliah'] ?></td><td><?= $r['nilai'] ?></td><td><?= $r['nama_dosen'] ?></td><td><?= $r['nama_ruang'] ?></td></tr>
<?php endwhile; ?>
</table>
</body></html>