<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../../config.php'); // koneksi DB
require '../../../vendor/autoload.php'; // load library PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    if ($file) {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // A, B, C, D

        $baris_mulai = 2; // baris 1 = judul kolom Excel
        foreach ($sheet as $index => $row) {
            if ($index < $baris_mulai) continue;

            // Kolom Excel: A = kode_barang, B = nama_barang, C = harga_pokok, D = harga_jual
            $kode_barang = trim($row['A']);
            $nama_barang = trim($row['B']);
            $harga_pokok = floatval($row['C']);
            $harga_jual = floatval($row['D']);
            $laba = $harga_jual - $harga_pokok;
            $minimal_stock = 0;

            // Validasi sederhana
            if (empty($kode_barang) || empty($nama_barang) || $harga_pokok <= 0 || $harga_jual <= 0) {
                continue; // Skip baris tidak valid
            }

            // Cek apakah kode barang sudah ada
            $cek = mysqli_query($conn, "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'");
            if (mysqli_num_rows($cek) === 0) {
                // Tambah barang baru
                mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, harga_pokok, harga_jual, laba, minimal_stock)
                    VALUES ('$kode_barang', '$nama_barang', $harga_pokok, $harga_jual, $laba, $minimal_stock)");

                $id_barang = mysqli_insert_id($conn);

                // Tambah stock untuk semua toko
                $toko_result = mysqli_query($conn, "SELECT id_toko FROM toko");
                while ($toko = mysqli_fetch_assoc($toko_result)) {
                    $id_toko = $toko['id_toko'];

                    // Cek jika kombinasi id_barang dan id_toko belum ada
                    $cek_stock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = $id_barang AND id_toko = $id_toko");
                    if (mysqli_num_rows($cek_stock) === 0) {
                        mysqli_query($conn, "INSERT INTO stock (id_barang, id_toko, stock) VALUES ($id_barang, $id_toko, 0)");
                    }
                }
            }
            // Jika sudah ada, abaikan atau bisa diupdate tergantung kebutuhan
        }

        $_SESSION['berhasil'] = "Import data stock berhasil!";
        header('Location: barang.php');
        exit;
    } else {
        $_SESSION['gagal'] = "File Excel tidak ditemukan.";
        header('Location: barang.php');
        exit;
    }
}
