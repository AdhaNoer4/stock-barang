<?php
session_start();
require_once '../../../vendor/autoload.php';
require_once '../../../config.php';

use Mpdf\Mpdf;

if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$id_toko = $_SESSION['id_toko'] ?? 1;

if (!isset($_POST['tanggal'])) {
    die("Tanggal tidak tersedia.");
}

$tanggal_input = $_POST['tanggal'];
$mpdf = new Mpdf(['orientation' => 'L']);
$mpdf->SetTitle('Laporan Stok Barang - ' . $tanggal_input);

// Query utama
$query = "
    SELECT 
        r.tanggal, 
        b.kode_barang, 
        b.nama_barang, 
        b.harga_pokok, 
        b.harga_jual, 
        (b.harga_jual - b.harga_pokok) AS laba,
        b.minimal_stock,
        b.id_barang,
        r.jumlah,
        r.jenis,
        s.stock
    FROM riwayat_stok r
    LEFT JOIN barang b ON r.id_barang = b.id_barang
    LEFT JOIN (
        SELECT id_barang, MAX(stock) AS stock
        FROM stock
        WHERE id_toko = $id_toko
        GROUP BY id_barang
    ) s ON s.id_barang = b.id_barang
    WHERE r.id_toko = $id_toko 
      AND DATE(r.tanggal) = '$tanggal_input'
";
$result = mysqli_query($conn, $query);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Mulai HTML
$html = '
<h3 style="text-align:center;">Laporan Stok Tanggal ' . date('d-m-Y', strtotime($tanggal_input)) . '</h3>
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
    $html .= '<tr><td colspan="10" style="text-align:center;">Data tidak ditemukan</td></tr>';
} else {
    $no = 1;
    foreach ($rows as $data) {
        $penambahan = $data['jenis'] === 'masuk' ? ('+' . $data['jumlah']) : '0';

        $html .= "<tr>
            <td>{$no}</td>
            <td>" . date('d-m-Y', strtotime($data['tanggal'])) . "</td>
            <td>{$data['kode_barang']}</td>
            <td>{$data['nama_barang']}</td>
            <td>Rp " . number_format($data['harga_pokok'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($data['harga_jual'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($data['laba'], 0, ',', '.') . "</td>
            <td>" . number_format($data['stock'] ?? 0, 0, ',', '.') . "</td>
            <td>{$data['minimal_stock']}</td>
            <td>{$penambahan}</td>
        </tr>";
        $no++;
    }
}

$html .= '</tbody></table>';

// Output PDF
$mpdf->WriteHTML($html);
$mpdf->Output("Laporan_Stok_$tanggal_input.pdf", "D");
?>
