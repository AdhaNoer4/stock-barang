<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
use Mpdf\Mpdf;

$tanggal_input = $_POST['tanggal'] ?? date('Y-m-d');
$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
$mpdf->SetTitle('Laporan Stok Barang - ' . $tanggal_input);

// Ambil semua data barang
$sql_barang = "SELECT * FROM barang ORDER BY id_barang";
$result_barang = mysqli_query($conn, $sql_barang);

// Siapkan array data
$data = [];
while ($row = mysqli_fetch_assoc($result_barang)) {
    $id_barang = $row['id_barang'];
    $data[$id_barang] = [
        'kode_barang' => $row['kode_barang'],
        'nama_barang' => $row['nama_barang'],
        'harga_pokok' => $row['harga_pokok'],
        'harga_jual' => $row['harga_jual'],
        'laba' => $row['harga_jual'] - $row['harga_pokok'],
        'minimal_stock' => $row['minimal_stock'],
        'stock_toko_1' => 0,
        'stock_toko_2' => 0,
        'stock_toko_3' => 0,
        'perubahan_stock' => 0,
        'tanggal' => $tanggal_input,
    ];
}

// Ambil data stok semua toko
$sql_stok = "SELECT id_barang, id_toko, stock FROM stock";
$result_stok = mysqli_query($conn, $sql_stok);
while ($row = mysqli_fetch_assoc($result_stok)) {
    $id_barang = $row['id_barang'];
    $id_toko = $row['id_toko'];
    $stok = $row['stock'];

    if (isset($data[$id_barang])) {
        $data[$id_barang]["stock_toko_$id_toko"] = $stok;
    }
}

// Ambil perubahan stock dari riwayat_stok pada tanggal tertentu
$sql_riwayat = "SELECT id_barang, SUM(jumlah) AS penambahan
                FROM riwayat_stok
                WHERE jenis = 'masuk' AND tanggal = ?
                GROUP BY id_barang";
$stmt = $conn->prepare($sql_riwayat);
$stmt->bind_param("s", $tanggal_input);
$stmt->execute();
$result_riwayat = $stmt->get_result();
while ($row = $result_riwayat->fetch_assoc()) {
    $id_barang = $row['id_barang'];
    if (isset($data[$id_barang])) {
        $data[$id_barang]['perubahan_stock'] = $row['penambahan'];
    }
}

// Bangun HTML
$html = '
<style>
    body { font-family: sans-serif; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 8px; font-size: 12px; text-align: center; }
</style>
<h3 style="text-align:center;">LAPORAN STOK BARANG - ' . date('d M Y', strtotime($tanggal_input)) . '</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Harga Pokok</th>
            <th>Harga Jual</th>
            <th>Laba</th>
            <th>Stock Toko 1</th>
            <th>Stock Toko 2</th>
            <th>Stock Toko 3</th>
            <th>Stock Total</th>
            <th>Minimal Stock</th>
            <th>Penambahan Stock</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($data as $item) {
    $total_stock = $item['stock_toko_1'] + $item['stock_toko_2'] + $item['stock_toko_3'];
    $html .= "<tr>
        <td>{$no}</td>
        <td>{$item['tanggal']}</td>
        <td>{$item['kode_barang']}</td>
        <td>{$item['nama_barang']}</td>
        <td>Rp " . number_format($item['harga_pokok'], 0, ',', '.') . "</td>
        <td>Rp " . number_format($item['harga_jual'], 0, ',', '.') . "</td>
        <td>Rp " . number_format($item['laba'], 0, ',', '.') . "</td>
        <td>{$item['stock_toko_1']}</td>
        <td>{$item['stock_toko_2']}</td>
        <td>{$item['stock_toko_3']}</td>
        <td>{$total_stock}</td>
        <td>{$item['minimal_stock']}</td>
        <td>{$item['perubahan_stock']}</td>
    </tr>";
    $no++;
}

$html .= '</tbody></table>';

// Output ke PDF
$mpdf->WriteHTML($html);
$mpdf->Output('laporan_stok_' . $tanggal_input . '.pdf', 'I');
