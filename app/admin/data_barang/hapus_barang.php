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
    $select_stock = mysqli_query($conn, "SELECT * FROM stock");
    $barang=mysqli_query($conn,"SELECT * FROM barang");
    // Cek apakah ada stok lain untuk id_barang ini
    $cek = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = $idbarang AND id_stock != $idstock");
    if (mysqli_num_rows($cek) > 0) {
        // Masih ada stok lain untuk barang ini
        $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_stock = $idstock");
    } else {
        // Tidak ada stok lain, hapus stok dan barangnya
        $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_stock = $idstock");
        $hapus_barang = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $idbarang");
    }

    // Hapus stock barang dari toko ini saja
    if ($hapus && mysqli_affected_rows($conn) > 0) {
        $_SESSION['berhasil'] = "Barang berhasil dihapus dari stok toko!";
    } else {
        $_SESSION['validasi'] = "Gagal menghapus stok barang untuk toko ini.";
    }
}


header('Location: barang.php');
exit;
?>