<?php
// ===========================================
// PRAKTIKUM 3 - SORTING DAN AGGREGATE (VERSI TERBARU)
// ===========================================

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'db_mahasiswa';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die("<h2 style='color:red;text-align:center;'>Koneksi database gagal: " . mysqli_connect_error() . "</h2>");
}

// ========== SIMPAN DATA NILAI BARU ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_nilai'])) {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $mata_kuliah = mysqli_real_escape_string($conn, $_POST['mata_kuliah']);
    $nilai = (int) $_POST['nilai'];

    $query = "INSERT INTO nilai (nim, mata_kuliah, nilai) VALUES ('$nim', '$mata_kuliah', $nilai)";
    if (mysqli_query($conn, $query)) {
        $msg = "✅ Data nilai berhasil ditambahkan!";
    } else {
        $msg = "❌ Gagal menambahkan data: " . mysqli_error($conn);
    }
}

// ========== AMBIL FILTER ==========
$filter_mk = isset($_GET['mata_kuliah']) ? mysqli_real_escape_string($conn, $_GET['mata_kuliah']) : '';
$filter_angkatan = isset($_GET['angkatan']) ? (int) $_GET['angkatan'] : '';

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
<title>Praktikum 3 - Sorting & Aggregate</title>
<style>
  body{font-family:Segoe UI,Arial,sans-serif;background:#f4f7fb;margin:0;padding:24px;}
  .wrap{max-width:1100px;margin:auto;}
  h1{text-align:center;color:#1a365d;margin-bottom:10px;}
  .msg{text-align:center;margin-bottom:15px;font-weight:bold;}
  .form-card, .card{background:white;padding:16px;margin:12px 0;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,0.1);}
  input,select{padding:8px;margin:5px;width:100%;max-width:300px;}
  button{padding:8px 16px;background:#007bff;color:white;border:none;border-radius:6px;cursor:pointer;}
  button:hover{background:#0056b3;}
  table{width:100%;border-collapse:collapse;margin-top:10px;}
  th,td{padding:8px;border:1px solid #dfe6ef;text-align:center;}
  th{background:#007bff;color:white;}
  tr:nth-child(even){background:#f8fafc;}
  .filter-form{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:10px;}
  .btn-print{background:#6b7280;}
  @media print{
    .form-card,.filter-form,.btn-print{display:none;}
    body{background:#fff;}
  }
</style>
</head>
<body>
<div class="wrap">
  <h1>Modul Praktikum 3 — Sorting & Aggregate</h1>
  <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

  <!-- ===== FORM INPUT NILAI BARU ===== -->
  <div class="form-card">
    <h3>Tambah Nilai Mahasiswa</h3>
    <form method="post">
      <label>NIM:</label><br>
      <input type="text" name="nim" placeholder="contoh: 2025001" required><br>
      <label>Mata Kuliah:</label><br>
      <input type="text" name="mata_kuliah" placeholder="contoh: Basis Data Lanjut" required><br>
      <label>Nilai:</label><br>
      <input type="number" name="nilai" min="0" max="100" required><br><br>
      <button type="submit" name="tambah_nilai">Simpan Nilai</button>
    </form>
  </div>

  <!-- ===== FILTER DATA ===== -->
  <div class="form-card">
    <h3>Filter Data</h3>
    <form method="get" class="filter-form">
      <div>
        <label for="mata_kuliah">Mata Kuliah:</label><br>
        <input type="text" name="mata_kuliah" id="mata_kuliah" value="<?= htmlspecialchars($filter_mk) ?>" placeholder="(opsional)">
      </div>
      <div>
        <label for="angkatan">Angkatan:</label><br>
        <input type="number" name="angkatan" id="angkatan" value="<?= htmlspecialchars($filter_angkatan) ?>" placeholder="(opsional)">
      </div>
      <div>
        <button type="submit">Terapkan Filter</button>
        <a href="praktikum_sorting_aggregate.php" style="margin-left:10px;text-decoration:none;"><button type="button" class="btn-print">Reset</button></a>
      </div>
    </form>
  </div>

  <!-- ===== ORDER BY ===== -->
  <div class="card">
    <h3>1️⃣ ORDER BY — Urutan Nama Mahasiswa</h3>
    <?php
      $q = "SELECT * FROM mahasiswa";
      if ($filter_angkatan) $q .= " WHERE angkatan = $filter_angkatan";
      $q .= " ORDER BY nama ASC";
      $out = run_select($conn, $q);
      if(isset($out['error'])) echo "<p style='color:red;'>".$out['error']."</p>";
      else if (count($out['rows'])>0){
        echo "<table><tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Angkatan</th></tr>";
        foreach($out['rows'] as $r){
          echo "<tr><td>{$r['nim']}</td><td>{$r['nama']}</td><td>{$r['jurusan']}</td><td>{$r['angkatan']}</td></tr>";
        }
        echo "</table>";
      } else echo "<p>Tidak ada data mahasiswa.</p>";
    ?>
  </div>

  <!-- ===== AGGREGATE ===== -->
  <div class="card">
    <h3>2️⃣ Fungsi Aggregate</h3>
    <?php
      $where = "";
      if ($filter_mk) $where = "WHERE mata_kuliah LIKE '%$filter_mk%'";
      $agg = [
        'Rata-rata (AVG)' => "SELECT AVG(nilai) AS hasil FROM nilai $where",
        'Jumlah Data (COUNT)' => "SELECT COUNT(*) AS hasil FROM nilai $where",
        'Total Nilai (SUM)' => "SELECT SUM(nilai) AS hasil FROM nilai $where",
        'Nilai Tertinggi (MAX)' => "SELECT MAX(nilai) AS hasil FROM nilai $where",
        'Nilai Terendah (MIN)' => "SELECT MIN(nilai) AS hasil FROM nilai $where"
      ];
      echo "<table><tr><th>Fungsi</th><th>Hasil</th></tr>";
      foreach($agg as $judul=>$sql){
        $res = run_select($conn,$sql);
        if(isset($res['error'])) echo "<tr><td>$judul</td><td style='color:red;'>Error</td></tr>";
        else{
          $val = isset($res['rows'][0]['hasil']) ? $res['rows'][0]['hasil'] : null;
          $val = $val!==null ? number_format($val,2) : '-';
          echo "<tr><td>$judul</td><td>$val</td></tr>";
        }
      }
      echo "</table>";
    ?>
  </div>

  <!-- ===== GROUP BY ===== -->
  <div class="card">
    <h3>3️⃣ GROUP BY — Rata-rata Nilai per Mahasiswa</h3>
    <?php
      $q = "SELECT n.nim, m.nama, AVG(n.nilai) AS rata_rata 
            FROM nilai n 
            JOIN mahasiswa m ON n.nim = m.nim";
      if ($filter_mk) $q .= " WHERE n.mata_kuliah LIKE '%$filter_mk%'";
      $q .= " GROUP BY n.nim, m.nama ORDER BY rata_rata DESC";
      $res = run_select($conn, $q);
      if(isset($res['error'])) echo "<p style='color:red;'>".$res['error']."</p>";
      else if (count($res['rows'])>0){
        echo "<table><tr><th>NIM</th><th>Nama</th><th>Rata-rata</th></tr>";
        foreach($res['rows'] as $r){
          echo "<tr><td>{$r['nim']}</td><td>{$r['nama']}</td><td>".number_format($r['rata_rata'],2)."</td></tr>";
        }
        echo "</table>";
      } else echo "<p>Tidak ada data nilai.</p>";
    ?>
  </div>

  <!-- ===== HAVING ===== -->
  <div class="card">
    <h3>4️⃣ HAVING — Mahasiswa dengan Rata-rata > 75</h3>
    <?php
      $q = "SELECT m.nama, AVG(n.nilai) AS rata_rata 
            FROM nilai n 
            JOIN mahasiswa m ON n.nim = m.nim";
      if ($filter_mk) $q .= " WHERE n.mata_kuliah LIKE '%$filter_mk%'";
      $q .= " GROUP BY m.nama HAVING AVG(n.nilai) > 75 ORDER BY rata_rata DESC";
      $res = run_select($conn, $q);
      if(isset($res['error'])) echo "<p style='color:red;'>".$res['error']."</p>";
      else if (count($res['rows'])>0){
        echo "<table><tr><th>Nama</th><th>Rata-rata Nilai</th></tr>";
        foreach($res['rows'] as $r){
          echo "<tr><td>{$r['nama']}</td><td>".number_format($r['rata_rata'],2)."</td></tr>";
        }
        echo "</table>";
      } else echo "<p>Tidak ada mahasiswa dengan rata-rata > 75.</p>";
    ?>
  </div>

  <div style="text-align:center;margin:20px;">
    <button onclick="window.print()">🖨 Cetak Halaman</button>
  </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
