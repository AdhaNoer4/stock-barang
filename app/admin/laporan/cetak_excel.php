<?php
require_once '../../../config.php';

$tanggal = $_POST['tanggal'] ?? date('Y-m-d');

// Ambil data toko
$toko_result = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko ORDER BY id_toko");
$toko_list = mysqli_fetch_all($toko_result, MYSQLI_ASSOC);

// Bangun query utama
$query = "
SELECT 
    b.id_barang, b.kode_barang, b.nama_barang, b.harga_pokok, b.harga_jual, b.laba, b.minimal_stock,
    IFNULL(ts.total_stock, 0) AS total_stock,
    IFNULL(rs.penambahan_stok, 0) AS penambahan_stok,";

// Tambahkan kolom stok per toko
foreach ($toko_list as $toko) {
    $query .= "
    IFNULL(stk_{$toko['id_toko']}.stok_toko, 0) AS stok_toko_{$toko['id_toko']},";
}

$query = rtrim($query, ','); // Hapus koma terakhir

$query .= "
FROM barang b

-- Total stock semua toko
LEFT JOIN (
    SELECT id_barang, SUM(stock) AS total_stock
    FROM stock
    GROUP BY id_barang
) ts ON ts.id_barang = b.id_barang

-- Penambahan stok per tanggal
LEFT JOIN (
    SELECT id_barang, SUM(jumlah) AS penambahan_stok
    FROM riwayat_stok
    WHERE jenis = 'masuk' AND DATE(tanggal) = '$tanggal'
    GROUP BY id_barang
) rs ON rs.id_barang = b.id_barang
";

// Stok per toko
foreach ($toko_list as $toko) {
    $query .= "
LEFT JOIN (
    SELECT id_barang, SUM(stock) AS stok_toko
    FROM stock
    WHERE id_toko = {$toko['id_toko']}
    GROUP BY id_barang
) stk_{$toko['id_toko']} ON stk_{$toko['id_toko']}.id_barang = b.id_barang";
}

$query .= "
ORDER BY b.nama_barang
";

// Jalankan query
$result = mysqli_query($conn, $query);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Output sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_stok_$tanggal.xls");

// Tabel header
echo "<table border='1'>";
echo "<tr><th>No</th><th>Kode</th><th>Nama</th><th>Harga Pokok</th><th>Harga Jual</th><th>Laba</th>";
foreach ($toko_list as $toko) {
    echo "<th>" . htmlspecialchars($toko['nama_toko']) . "</th>";
}
echo "<th>Total</th><th>Minimal</th><th>Penambahan</th></tr>";

// Tabel isi
$no = 1;
foreach ($rows as $row) {
    echo "<tr><td>{$no}</td>";
    echo "<td>{$row['kode_barang']}</td>";
    echo "<td>{$row['nama_barang']}</td>";
    echo "<td>" . number_format($row['harga_pokok'], 0, ',', '.') . "</td>";
    echo "<td>" . number_format($row['harga_jual'], 0, ',', '.') . "</td>";
    echo "<td>" . number_format($row['laba'], 0, ',', '.') . "</td>";
    foreach ($toko_list as $toko) {
        echo "<td>" . ($row["stok_toko_" . $toko['id_toko']] ?? 0) . "</td>";
    }
    echo "<td>{$row['total_stock']}</td>";
    echo "<td>{$row['minimal_stock']}</td>";
    echo "<td>{$row['penambahan_stok']}</td>";
    echo "</tr>";
    $no++;
}
echo "</table>";
?>
