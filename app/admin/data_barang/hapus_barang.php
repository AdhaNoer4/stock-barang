<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

require_once('../../../config.php');

if (isset($_GET['id'])) {
    $idstock = intval($_GET['id']);
    $idbarang = intval($_GET['id_barang']);
    $id_toko = $_SESSION['id_toko'];

    // Hapus stock barang dari toko ini saja
    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_stock = $idstock ");
    $hapus_barang = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $idbarang");
    if ($hapus && mysqli_affected_rows($conn) > 0) {
        $_SESSION['berhasil'] = "Barang berhasil dihapus dari stok toko!";
    } else {
        $_SESSION['validasi'] = "Gagal menghapus stok barang untuk toko ini.";
    }
}


header('Location: barang.php');
exit;
?>