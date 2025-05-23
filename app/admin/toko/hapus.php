<?php

session_start();
require_once("../../../config.php");

$id = $_GET['id'];

$result = mysqli_query($conn, "DELETE FROM toko WHERE id_toko='$id'");

$_SESSION['berhasil'] = 'Data berhasil dihapus';
header('Location: toko.php');
exit;
?>

<?php include('../layouts/footer.php'); ?>


