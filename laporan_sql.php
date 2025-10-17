<?php
// ==========================================================
// PRAKTIKUM 3 - LAPORAN SQL : SORTING DAN AGGREGATE
// ==========================================================

// Tampilkan error agar mudah dicek di browser
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi Database
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'db_mahasiswa';

// Koneksi ke database
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die("<h2 style='color:red;text-align:center;'>Koneksi database gagal: " . mysqli_connect_error() . "</h2>");
}

// Fungsi bantu untuk menjalankan query SELECT
function run_select($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if ($res === false) {
        return ['error' => mysqli_error($conn)];
    }
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return ['rows' => $rows];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Laporan SQL - Sorting & Aggregate</title>
<style>
  body{font-family:Segoe UI,Arial;background:#f6f8fb;margin:0;padding:24px;}
  .wrap{max-width:1100px;margin:auto;}
  h1{text-align:center;color:#1e40af;margin-bottom:6px;}
  h3{color:#0f172a;}
  table{width:100%;border-collapse:collapse;margin-top:10px;}
  th,td{padding:8px;border:1px solid #dbeafe;text-align:center;}
  th{background:#007bff;color:white;}
  .card{background:white;padding:16px;margin:10px 0;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,0.1);}
  .nav{text-align:center;margin-bottom:10px;}
  .btn{padding:8px 14px;background:#007bff;color:white;border-radius:6px;text-decoration:none;margin:4px;display:inline-block;}
  .btn.print{background:#475569;}
  .btn.home{background:#10b981;}
  .btn.home:hover{background:#059669;}
  @media print {.nav,.btn{display:none;}}
</style>
</head>
<body>
<div class="wrap">
  <h1>Laporan SQL — Sorting dan Aggregate</h1>

  <div class="nav">
    <a href="index.html" class="btn home">Home</a>
    <a href="input_nilai.php" class="btn">Input Nilai</a>
    <a href="#" onclick="window.print()" class="btn print">Cetak / Simpan PDF</a>
  </div>

  <!-- ORDER BY -->
  <div class="card">
    <h3>1. ORDER BY</h3>
    <?php
      $out = run_select($conn, "SELECT * FROM mahasiswa ORDER BY nama ASC");
      if(isset($out['error'])) echo "<p style='color:red;'>{$out['error']}</p>";
      else if(count($out['rows'])==0) echo "<p>Tidak ada data mahasiswa.</p>";
      else{
        echo "<table><tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Angkatan</th></tr>";
        foreach($out['rows'] as $r){
          echo "<tr><td>{$r['nim']}</td><td>{$r['nama']}</td><td>{$r['jurusan']}</td><td>{$r['angkatan']}</td></tr>";
        }
        echo "</table>";
      }
    ?>
  </div>

  <!-- AGGREGATE -->
  <div class="card">
    <h3>2. Fungsi Aggregate</h3>
    <?php
      $agg = [
        'Rata-rata (AVG)' => "SELECT AVG(nilai) AS hasil FROM nilai",
        'Jumlah Data (COUNT)' => "SELECT COUNT(*) AS hasil FROM nilai",
        'Total Nilai (SUM)' => "SELECT SUM(nilai) AS hasil FROM nilai",
        'Nilai Tertinggi (MAX)' => "SELECT MAX(nilai) AS hasil FROM nilai",
        'Nilai Terendah (MIN)' => "SELECT MIN(nilai) AS hasil FROM nilai"
      ];
      echo "<table><tr><th>Fungsi</th><th>Hasil</th></tr>";
      foreach($agg as $judul=>$sql){
        $res = run_select($conn,$sql);
        if(isset($res['error'])) echo "<tr><td>$judul</td><td style='color:red;'>Error</td></tr>";
        else{
          $val = $res['rows'][0]['hasil'] ?? '-';
          $val = is_numeric($val) ? number_format($val,2) : $val;
          echo "<tr><td>$judul</td><td>$val</td></tr>";
        }
      }
      echo "</table>";
    ?>
  </div>

  <!-- GROUP BY -->
  <div class="card">
    <h3>3. GROUP BY — Rata-rata Nilai per Mahasiswa</h3>
    <?php
      $q = "SELECT n.nim, m.nama, AVG(n.nilai) AS rata_rata 
            FROM nilai n 
            JOIN mahasiswa m ON n.nim = m.nim 
            GROUP BY n.nim, m.nama 
            ORDER BY rata_rata DESC";
      $res = run_select($conn, $q);
      if(isset($res['error'])) echo "<p style='color:red;'>{$res['error']}</p>";
      else if(count($res['rows'])==0) echo "<p>Tidak ada data nilai mahasiswa.</p>";
      else{
        echo "<table><tr><th>NIM</th><th>Nama</th><th>Rata-rata Nilai</th></tr>";
        foreach($res['rows'] as $r){
          echo "<tr><td>{$r['nim']}</td><td>{$r['nama']}</td><td>".number_format($r['rata_rata'],2)."</td></tr>";
        }
        echo "</table>";
      }
    ?>
  </div>

  <!-- HAVING -->
  <div class="card">
    <h3>4. HAVING — Mahasiswa dengan Rata-rata > 75</h3>
    <?php
      $q = "SELECT m.nama, AVG(n.nilai) AS rata_rata 
            FROM nilai n 
            JOIN mahasiswa m ON n.nim = m.nim 
            GROUP BY m.nama 
            HAVING AVG(n.nilai) > 75 
            ORDER BY rata_rata DESC";
      $res = run_select($conn, $q);
      if(isset($res['error'])) echo "<p style='color:red;'>{$res['error']}</p>";
      else if(count($res['rows'])==0) echo "<p>Tidak ada mahasiswa dengan rata-rata di atas 75.</p>";
      else{
        echo "<table><tr><th>Nama</th><th>Rata-rata Nilai</th></tr>";
        foreach($res['rows'] as $r){
          echo "<tr><td>{$r['nama']}</td><td>".number_format($r['rata_rata'],2)."</td></tr>";
        }
        echo "</table>";
      }
    ?>
  </div>

  <div class="nav">
    <a href="index.html" class="btn home">Home</a>
    <a href="input_nilai.php" class="btn">Input Nilai</a>
    <a href="#" onclick="window.print()" class="btn print">Cetak / Simpan PDF</a>
  </div>
</div>
</body>
</html>

<?php
// Tutup koneksi di akhir halaman
mysqli_close($conn);
?>
