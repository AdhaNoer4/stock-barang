<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../../config.php');
require '../../../vendor/autoload.php'; // pastikan PhpSpreadsheet tersedia

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom Excel
$sheet->setCellValue('A1', 'Kode Barang');
$sheet->setCellValue('B1', 'Nama Barang');
$sheet->setCellValue('C1', 'Harga Pokok');
$sheet->setCellValue('D1', 'Harga Jual');

// Ambil data dari database
$result = mysqli_query($conn, "SELECT kode_barang, nama_barang, harga_pokok, harga_jual FROM barang");

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A{$rowNum}", $row['kode_barang']);
    $sheet->setCellValue("B{$rowNum}", $row['nama_barang']);
    $sheet->setCellValue("C{$rowNum}", $row['harga_pokok']);
    $sheet->setCellValue("D{$rowNum}", $row['harga_jual']);
    $rowNum++;
}

// Set nama file dan kirim header untuk download
$filename = "data_barang_" . date("Ymd_His") . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Ekspor ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
