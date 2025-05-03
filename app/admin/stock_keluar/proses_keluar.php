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
    $idbarang = htmlspecialchars($_POST['idbarang']);
    $qty = htmlspecialchars($_POST['qty']);
    $penerima = htmlspecialchars($_POST['penerima']);

    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validasi inputan
        if (empty($idbarang)) {
            $pesan_kesalahan[] = "$icon_validasi Nama Barang wajib diisi!";
        }
        if (empty($qty)) {
            $pesan_kesalahan[] = "$icon_validasi Jumlah Masuk wajib diisi!";
        }
        if (empty($penerima)) {
            $pesan_kesalahan[] = "$icon_validasi penerima wajib diisi!";
        }
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // insert ke tabel stock_masuk
            $insert = mysqli_query($conn, "INSERT INTO keluar (idbarang, tanggal, penerima, qty) VALUES ('$idbarang', NOW() , '$penerima', '$qty')");

            // Cek stok tersedia
            $cek = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idbarang'");
            $data = mysqli_fetch_assoc($cek);
            if ($data['stock'] < $qty) {
                $_SESSION['gagal'] = "Stok tidak mencukupi!";
                header('Location: stock_keluar.php');
                exit;
            }
            // update ke tabel stock
            $update = mysqli_query($conn, "UPDATE stock SET stock = stock - '$qty' WHERE idbarang = '$idbarang'");

            // insert ke tabel riwayat_stok
            $insert_riwayat = mysqli_query($conn, "INSERT INTO riwayat_stok (idbarang, id_user, aksi, jumlah, tanggal) VALUES ('$idbarang', '$_SESSION[id_user]', 'keluar', '$qty', NOW())");

            if ($insert && $update) {
                $_SESSION['berhasil'] = "Stock berhasil dikeluarkan!";
                header('Location: stock_keluar.php');
                exit;

                if ($insert_riwayat) {
                    $_SESSION['berhasil'] = "Riwayat berhasil ditambahkan!";
                    header('Location: stock_masuk.php');
                    exit;
                } else {
                    $_SESSION['gagal'] = "Riwayat gagal ditambahkan!";
                }
            } else {
                $_SESSION['gagal'] = "Stock gagal dikeluarkan!";
            }
        }
    }
}
