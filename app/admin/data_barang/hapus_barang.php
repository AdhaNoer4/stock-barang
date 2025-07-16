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

if (isset($_GET['id_stock']) && isset($_GET['id_barang'])) {
    $idstock = intval($_GET['id_stock']);
    $idbarang = intval($_GET['id_barang']);

    // Cek apakah masih ada stok lain untuk barang ini selain yang akan dihapus
    $cek = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = $idbarang AND id_stock != $idstock");

    if (mysqli_num_rows($cek) > 0) {
        // Masih ada stok lain → hanya hapus stok ini
        $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_stock = $idstock");
    } else {
        // Tidak ada stok lain → hapus stok ini & data barang
        $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_stock = $idstock");
        $hapus_barang = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $idbarang");
    }

    if ($hapus && mysqli_affected_rows($conn) > 0) {
        $_SESSION['berhasil'] = "Barang berhasil dihapus dari stok toko!";
    } else {
        $_SESSION['validasi'] = "Gagal menghapus stok barang untuk toko ini.";
    }
}

header('Location: barang.php');
exit;
?>
