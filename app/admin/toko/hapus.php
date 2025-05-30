<?php
session_start();
require_once('../../../config.php');

if (!isset($_SESSION["login"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

$id = $_GET['id'] ?? 0;

$cek = mysqli_query($conn, "SELECT * FROM toko WHERE id_toko = $id");
if (mysqli_num_rows($cek) === 0) {
    $_SESSION['gagal'] = "Toko tidak ditemukan.";
    header("Location: toko.php");
    exit;
}

// Hapus stok terkait juga (agar tidak orphan)
mysqli_query($conn, "DELETE FROM stock WHERE id_toko = $id");
mysqli_query($conn, "DELETE FROM user WHERE id_toko = $id");
$hapus = mysqli_query($conn, "DELETE FROM toko WHERE id_toko = $id");

if ($hapus) {
    $_SESSION['berhasil'] = "Toko berhasil dihapus.";
} else {
    $_SESSION['gagal'] = "Gagal menghapus toko.";
}
header("Location: toko.php");
exit;
