<?php

session_start();
require_once("../../../config.php");

$id = $_GET['id'];

$result = mysqli_query($conn, "DELETE FROM user WHERE id_user='$id'");

$_SESSION['berhasil'] = 'Data berhasil dihapus';
header('Location: user.php');
exit;
?>

<?php include('../layouts/footer.php'); ?>


