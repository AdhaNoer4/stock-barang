<?php
session_start();
ob_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} elseif ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../../config.php');

if (isset($_POST['submit'])) {
    $id_barang = htmlspecialchars($_POST['id_barang']);
    $jumlah = (int) htmlspecialchars($_POST['jumlah']);
    $penerima = htmlspecialchars($_POST['penerima'] ?? '');
    $tanggal = htmlspecialchars($_POST['tanggal']);
    $id_toko = isset($_POST['id_toko']) ? intval($_POST['id_toko']) : null;
    $id_user = $_SESSION['id_user'];

    $icon_validasi = "<svg ...>...</svg>"; // SVG seperti sebelumnya

    $pesan_kesalahan = [];
    if (empty($id_barang)) $pesan_kesalahan[] = "$icon_validasi Nama Barang wajib diisi!";
    if (empty($jumlah) || $jumlah <= 0) $pesan_kesalahan[] = "$icon_validasi Jumlah keluar wajib diisi dan lebih dari 0!";
    if (empty($penerima)) $pesan_kesalahan[] = "$icon_validasi Penerima wajib diisi!";
    if (!$id_toko) $pesan_kesalahan[] = "$icon_validasi Toko harus dipilih!";

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        header("Location: stock_keluar.php");
        exit;
    }

    // Cek stok cukup
    $cek_stok = mysqli_query($conn, "SELECT stock FROM stock WHERE id_barang = '$id_barang' AND id_toko = '$id_toko'");
    $data_stok = mysqli_fetch_assoc($cek_stok);

    if (!$data_stok || $data_stok['stock'] < $jumlah) {
        $_SESSION['gagal'] = "Stok tidak mencukupi!";
        header("Location: stock_keluar.php");
        exit;
    }

    // Insert ke tabel keluar
    $insert = mysqli_query($conn, "INSERT INTO keluar (id_barang, tanggal, penerima, jumlah, id_user, id_toko) VALUES ('$id_barang', '$tanggal', '$penerima', '$jumlah', '$id_user', '$id_toko')");

    // Update stok (kurangi stok)
    $update = mysqli_query($conn, "UPDATE stock SET stock = stock - '$jumlah' WHERE id_barang = '$id_barang' AND id_toko = '$id_toko'");

    // Insert ke riwayat stok
    $insert_riwayat = mysqli_query($conn, "INSERT INTO riwayat_stok (id_barang, jumlah, tanggal, id_user, id_toko, jenis) VALUES ('$id_barang', '$jumlah', '$tanggal', '$id_user', '$id_toko', 'keluar')");

    if ($insert && $update && $insert_riwayat) {
        $_SESSION['berhasil'] = "Stock berhasil dikeluarkan!";
    } else {
        $_SESSION['gagal'] = "Terjadi kesalahan saat mengeluarkan stok!";
    }

    header("Location: stock_keluar.php");
    exit;
}
