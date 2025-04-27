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

    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE idbarang = $idbarang");

    if ($hapus) {
        $_SESSION['berhasil'] = "Barang berhasil dihapus!";
    } else {
        $_SESSION['validasi'] = "Gagal menghapus barang.";
    }
}

header('Location: barang.php');
exit;
?>