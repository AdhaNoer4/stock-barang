<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
require_once('../../../config.php');

if (isset($_POST['submit'])) {
    $id_barang = htmlspecialchars($_POST['id_barang']);
    $jumlah = htmlspecialchars($_POST['jumlah']);
    $keterangan = htmlspecialchars($_POST['keterangan'] ?? '');
    $tanggal = htmlspecialchars($_POST['tanggal']);
    $id_toko = isset($_POST['id_toko']) ? intval($_POST['id_toko']) : null;
    if (!$id_toko) {
        $_SESSION['gagal'] = "Toko harus dipilih!";
        header("Location: stock_masuk.php");
        exit;
    }
    $id_user = $_SESSION['id_user'];

    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pesan_kesalahan = [];
        // Validasi inputan
        if (empty($id_barang)) {
            $pesan_kesalahan[] = "$icon_validasi Nama Barang wajib diisi!";
        }
        if (empty($jumlah)) {
            $pesan_kesalahan[] = "$icon_validasi Jumlah Masuk wajib diisi!";
        }
        if (empty($keterangan)) {
            $pesan_kesalahan[] = "$icon_validasi Keterangan wajib diisi!";
        }
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // insert ke tabel masuk
            $insert = mysqli_query($conn, "INSERT INTO masuk (id_barang, tanggal, keterangan, jumlah, id_user, id_toko) VALUES ('$id_barang', '$tanggal' , '$keterangan', '$jumlah', '$id_user', '$id_toko')");

            // update ke tabel stock
            $update = mysqli_query($conn, "UPDATE stock SET stock = stock + '$jumlah', id_toko = '$id_toko'  WHERE id_barang = '$id_barang'");

            // insert ke tabel riwayat_stok
            $insert_riwayat = mysqli_query($conn, "INSERT INTO riwayat_stok (id_barang, jumlah, tanggal, id_user, id_toko, jenis) VALUES ('$id_barang', '$jumlah', '$tanggal', '$id_user', '$id_toko', 'masuk')");

            if ($insert && $update) {
                $_SESSION['berhasil'] = "Stock berhasil ditambahkan!";
                header('Location: stock_masuk.php');
                exit;

                if ($insert_riwayat) {
                    $_SESSION['berhasil'] = "Riwayat berhasil ditambahkan!";
                    header('Location: stock_masuk.php');
                    exit;
                } else {
                    $_SESSION['gagal'] = "Riwayat gagal ditambahkan!";
                }
            } else {
                $_SESSION['gagal'] = "Stock gagal ditambahkan!";
            }
        }
    }
}
