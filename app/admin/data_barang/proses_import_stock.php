<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}
require_once('../../../config.php'); // koneksi
require '../../../vendor/autoload.php'; // jika pakai Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    if ($file) {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $baris_mulai = 2; // baris 1 = judul kolom
        foreach ($sheet as $index => $row) {
            if ($index < $baris_mulai) continue;

            // Misal kolom Excel: A = kode_barang, B = nama_barang, C = jumlah, D = id_toko
            $kode_barang = $row['A'];
            $nama_barang = $row['B'];
            $harga_pokok = floatval($row['C']);
            $harga_jual = floatval($row['D']);
            $laba = $harga_jual - $harga_pokok;
            $minimal_stock = 0; // jika ada kolom minimal stock

            if (empty($kode_barang) || empty($nama_barang) || $harga_pokok <= 0 || $harga_jual <= 0) {
                continue; // skip baris, jangan exit semua
            }

            // // Cari ID barang
            // $barang = mysqli_query($conn, "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'");
            // if (mysqli_num_rows($barang) == 0) {
            //     // Tambah barang baru jika belum ada
            //     mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, harga_pokok, harga_jual, laba, minimal_stock)
            // VALUES ('$kode_barang', '$nama_barang', $harga_pokok, $harga_jual, $laba, $minimal_stock)");
            //     $id_barang = mysqli_insert_id($conn);
            // } else {
            //     $id_barang = mysqli_fetch_assoc($barang)['id_barang'];
            // }

            // Cek kode barang agar tidak duplikat
            $cek = mysqli_query($conn, "SELECT * FROM barang WHERE kode_barang = '$kode_barang'");
            if (mysqli_num_rows($cek) === 0) {

                // Tambahkan ke tabel barang
                mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, harga_pokok, harga_jual, laba, minimal_stock)
            VALUES ('$kode_barang', '$nama_barang', $harga_pokok, $harga_jual, $laba, $minimal_stock)");

                $id_barang = mysqli_insert_id($conn);

                // Ambil semua toko
                $toko_result = mysqli_query($conn, "SELECT id_toko FROM toko");
                while ($toko = mysqli_fetch_assoc($toko_result)) {
                    $id_toko = $toko['id_toko'];

                    // Cek apakah sudah ada entry stok untuk kombinasi ini
                    $cek_stock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = $id_barang AND id_toko = $id_toko");
                    if (mysqli_num_rows($cek_stock) === 0) {
                        mysqli_query($conn, "INSERT INTO stock (id_barang, id_toko, stock) VALUES ($id_barang, $id_toko, 0)");
                    }
                }

                // Redirect untuk hindari submit ulang
                header("Location: barang.php?pesan=berhasil");
                exit;
            } else {
                $_SESSION['berhasil'] = "Import data stock berhasil!";
            }
        }

        $_SESSION['berhasil'] = "Import data stock berhasil!";
        header('Location: barang.php');
    }
}
