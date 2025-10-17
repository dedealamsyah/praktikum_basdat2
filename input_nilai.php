<?php
// =====================================
// PRAKTIKUM 3 - INPUT NILAI MAHASISWA
// =====================================

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'db_mahasiswa';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die("<h2 style='color:red;text-align:center;'>Koneksi database gagal: " . mysqli_connect_error() . "</h2>");
}

// Simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_nilai'])) {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $mata_kuliah = mysqli_real_escape_string($conn, $_POST['mata_kuliah']);
    $nilai = (int) $_POST['nilai'];

    $query = "INSERT INTO nilai (nim, mata_kuliah, nilai) VALUES ('$nim', '$mata_kuliah', $nilai)";
    if (mysqli_query($conn, $query)) {
        $msg = "✅ Data nilai berhasil disimpan!";
    } else {
        $msg = "❌ Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Input Nilai Mahasiswa</title>
  <style>
    body{font-family:Segoe UI,Arial;background:#f4f7fb;margin:0;padding:24px;}
    .wrap{max-width:500px;margin:auto;background:white;padding:24px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
    h1{text-align:center;color:#1e3a8a;}
    label{font-weight:bold;}
    input{width:100%;padding:8px;margin:6px 0;border:1px solid #cbd5e1;border-radius:6px;}
    button{padding:10px 16px;background:#007bff;color:white;border:none;border-radius:6px;cursor:pointer;}
    button:hover{background:#0056b3;}
    .msg{text-align:center;margin-bottom:10px;font-weight:bold;}
    .nav{text-align:center;margin-top:15px;}
    a{text-decoration:none;color:#007bff;}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Input Nilai Mahasiswa</h1>
    <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>
    <form method="post">
      <label for="nim">NIM:</label>
      <input type="text" id="nim" name="nim" required placeholder="Contoh: 2025001">

      <label for="mata_kuliah">Mata Kuliah:</label>
      <input type="text" id="mata_kuliah" name="mata_kuliah" required placeholder="Contoh: Basis Data Lanjut">

      <label for="nilai">Nilai:</label>
      <input type="number" id="nilai" name="nilai" min="0" max="100" required placeholder="0-100">

      <button type="submit" name="tambah_nilai">Simpan Nilai</button>
    </form>
    <div class="nav">
      <p><a href="laporan_sql.php">➡ Lihat Laporan SQL</a></p>
    </div>
  </div>
</body>
</html>
<?php mysqli_close($conn); ?>
