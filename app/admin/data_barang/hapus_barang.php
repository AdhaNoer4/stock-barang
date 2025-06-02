<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

require_once('../../../config.php');

if (isset($_GET['id'])) {
    $idbarang = intval($_GET['id']);
    $id_toko = $_SESSION['id_toko'];

    // Hapus stock barang dari toko ini saja
    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_barang = $idbarang AND id_toko = $id_toko");

    if ($hapus && mysqli_affected_rows($conn) > 0) {
        $_SESSION['berhasil'] = "Barang berhasil dihapus dari stok toko!";
    } else {
        $_SESSION['validasi'] = "Gagal menghapus stok barang untuk toko ini.";
    }
}


header('Location: barang.php');
exit;
?>