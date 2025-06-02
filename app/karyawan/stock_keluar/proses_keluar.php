<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
require_once('../../../config.php');

if (isset($_POST['submit'])) {
    $id_barang = htmlspecialchars($_POST['id_barang']);
    $jumlah = htmlspecialchars($_POST['jumlah']);
    $penerima = htmlspecialchars($_POST['penerima']);
    $tanggal = htmlspecialchars($_POST['tanggal']);
    $id_toko = $_SESSION['id_toko'];
    $id_user = $_SESSION['id_user'];

    $icon_validasi = "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    $pesan_kesalahan = [];

    // Validasi input
    if (empty($id_barang)) {
        $pesan_kesalahan[] = "$icon_validasi Nama Barang wajib diisi!";
    }
    if (empty($jumlah)) {
        $pesan_kesalahan[] = "$icon_validasi Jumlah keluar wajib diisi!";
    }
    if (empty($penerima)) {
        $pesan_kesalahan[] = "$icon_validasi Penerima wajib diisi!";
    }
    if (empty($tanggal)) {
        $pesan_kesalahan[] = "$icon_validasi Tanggal wajib diisi!";
    }

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        // Cek stok sekarang
        $query = mysqli_query($conn, "SELECT stock FROM stock WHERE id_barang = '$id_barang' AND id_toko = '$id_toko'");
        $data = mysqli_fetch_assoc($query);

        if (!$data) {
            $_SESSION['gagal'] = "Stok untuk barang ini belum tersedia di toko Anda.";
            header('Location: stock_keluar.php');
            exit;
        }

        $stok_sekarang = $data['stock'];

        if ($jumlah <= 0) {
            $_SESSION['gagal'] = "Jumlah keluar harus lebih dari 0!";
            header('Location: stock_keluar.php');
            exit;
        }

        if ($jumlah > $stok_sekarang) {
            $_SESSION['gagal'] = "Jumlah Stok Tidak Mencukupi. Silakan tambah stok terlebih dahulu!";
            header('Location: stock_keluar.php');
            exit;
        }

        // Insert ke tabel keluar
        $insert = mysqli_query($conn, "INSERT INTO keluar (id_barang, tanggal, penerima, jumlah, id_user, id_toko) VALUES ('$id_barang', '$tanggal', '$penerima', '$jumlah', '$id_user', '$id_toko')");

        // Update tabel stock
        $update = mysqli_query($conn, "UPDATE stock SET stock = stock - '$jumlah' WHERE id_barang = '$id_barang' AND id_toko = '$id_toko'");

        // Insert ke riwayat stok
        $insert_riwayat = mysqli_query($conn, "INSERT INTO riwayat_stok (id_barang, jumlah, tanggal, id_user, id_toko, jenis) VALUES ('$id_barang', '$jumlah', '$tanggal', '$id_user', '$id_toko', 'keluar')");

        if ($insert && $update && $insert_riwayat) {
            $_SESSION['berhasil'] = "Stock berhasil dikeluarkan!";
        } else {
            $_SESSION['gagal'] = "Gagal mengeluarkan stock!";
        }

        header('Location: stock_keluar.php');
        exit;
    }
}
