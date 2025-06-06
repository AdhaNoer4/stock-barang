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
    $keterangan = htmlspecialchars($_POST['keterangan']);
    $tanggal = htmlspecialchars($_POST['tanggal']);
    $id_toko = $_SESSION['id_toko'];
    $id_user = $_SESSION['id_user'];

    $icon_validasi = "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

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
            // Insert ke tabel masuk
            $insert = mysqli_query($conn, "INSERT INTO masuk (id_barang, tanggal, keterangan, jumlah, id_user, id_toko) VALUES ('$id_barang', '$tanggal', '$keterangan', '$jumlah', '$id_user', '$id_toko')");

            // Cek apakah stock sudah ada untuk barang dan toko ini
            $cekStock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = '$id_barang' AND id_toko = '$id_toko'");
            if (mysqli_num_rows($cekStock) > 0) {
                // Jika ada, update
                $update = mysqli_query($conn, "UPDATE stock SET stock = stock + '$jumlah' WHERE id_barang = '$id_barang' AND id_toko = '$id_toko'");
            } else {
                // Jika tidak ada, insert baru
                $update = mysqli_query($conn, "INSERT INTO stock (id_barang, stock, id_toko) VALUES ('$id_barang', '$jumlah', '$id_toko')");
            }

            // Insert ke tabel riwayat_stok
            $insert_riwayat = mysqli_query($conn, "INSERT INTO riwayat_stok (id_barang, jumlah, tanggal, id_user, id_toko, jenis) VALUES ('$id_barang', '$jumlah', '$tanggal', '$id_user', '$id_toko', 'masuk')");

            if ($insert && $update && $insert_riwayat) {
                $_SESSION['berhasil'] = "Stock berhasil ditambahkan!";
                header('Location: stock_masuk.php');
                exit;
            } else {
                $_SESSION['gagal'] = "Terjadi kesalahan saat menambahkan data!";
            }
        }
    }
}
?>
