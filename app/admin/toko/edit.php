<?php
session_start();
require_once('../../../config.php');

if (!isset($_SESSION["login"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

$id = $_GET['id'] ?? 0;
$result = mysqli_query($conn, "SELECT * FROM toko WHERE id_toko = $id");
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['gagal'] = "Toko tidak ditemukan.";
    header("Location: toko.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko']);
    $alamat = trim($_POST['alamat']);
    $telepon = trim($_POST['telepon']);

    $update = mysqli_query($conn, "UPDATE toko SET 
        nama_toko = '$nama_toko',
        alamat = '$alamat',
        telepon = '$telepon'
        WHERE id_toko = $id");

    if ($update) {
        $_SESSION['berhasil'] = "Toko berhasil diperbarui.";
    } else {
        $_SESSION['gagal'] = "Gagal memperbarui toko.";
    }

    header("Location: toko.php");
    exit;
}
?>

<?php include('../layouts/header.php'); ?>

<div class="container mt-4">
    <h4>Edit Toko</h4>
    <form method="POST">
        <div class="mb-3">
            <label for="nama_toko">Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($data['nama_toko']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="alamat">Alamat</label>
            <textarea name="alamat" class="form-control"><?= htmlspecialchars($data['alamat']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="telepon">Telepon</label>
            <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($data['telepon']) ?>">
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="toko.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?php include('../layouts/footer.php'); ?>
