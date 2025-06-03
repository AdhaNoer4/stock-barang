<?php
session_start();
require_once('../../../config.php');
require_once('../../../vendor/autoload.php');

use Mpdf\Mpdf;

if (!isset($_SESSION["login"]) || $_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

$id_toko = $_SESSION['id_toko'];

if (!isset($_POST['tanggal'])) {
    die("Tanggal tidak tersedia.");
}

$tanggal = $_POST['tanggal'];

// Query data stok berdasarkan tanggal dan toko
$query = "
    SELECT 
        r.tanggal, 
        b.kode_barang, 
        b.nama_barang, 
        b.harga_pokok, 
        b.harga_jual, 
        b.laba, 
        b.minimal_stock,
        b.id_barang,
        r.jumlah,
        r.jenis,
        s.stock
    FROM riwayat_stok r
    JOIN barang b ON r.id_barang = b.id_barang
    LEFT JOIN stock s ON s.id_barang = b.id_barang AND s.id_toko = $id_toko
    WHERE s.id_toko = $id_toko
      AND DATE(r.tanggal) = '$tanggal'
";

$result = mysqli_query($conn, $query);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Buat isi HTML-nya
$html = '
<h3 style="text-align:center;">Laporan Stok Tanggal ' . date('d-m-Y', strtotime($tanggal)) . '</h3>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Harga Pokok</th>
            <th>Harga Jual</th>
            <th>Laba</th>
            <th>Stock</th>
            <th>Minimal Stock</th>
            <th>Penambahan Stock</th>
        </tr>
    </thead>
    <tbody>';

if (empty($rows)) {
    $html .= '<tr><td colspan="10" align="center">Data tidak ditemukan</td></tr>';
} else {
    $no = 1;
    foreach ($rows as $data) {
        $html .= '<tr>
            <td>' . $no++ . '</td>
            <td>' . date('d-m-Y', strtotime($data['tanggal'])) . '</td>
            <td>' . $data['kode_barang'] . '</td>
            <td>' . $data['nama_barang'] . '</td>
            <td>' . number_format($data['harga_pokok']) . '</td>
            <td>' . number_format($data['harga_jual']) . '</td>
            <td>' . number_format($data['laba']) . '</td>
            <td>' . ($data['stock'] ?? 0) . '</td>
            <td>' . $data['minimal_stock'] . '</td>
            <td>' . ($data['jenis'] == 'masuk' ? '+' . $data['jumlah'] : '0') . '</td>
        </tr>';
    }
}

$html .= '</tbody></table>';

// Buat PDF-nya
$mpdf = new Mpdf(['orientation' => 'L']);
$mpdf->WriteHTML($html);
$mpdf->Output("Laporan_Stok_$tanggal.pdf", "D"); 
?>
