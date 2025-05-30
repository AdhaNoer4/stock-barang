<?php
session_start();
require_once('../../../config.php');

if (!isset($_SESSION["login"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko']);
    $alamat = trim($_POST['alamat']);
    $telepon = trim($_POST['telepon']);

    // Cek apakah nama toko sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM toko WHERE nama_toko = '$nama_toko'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['gagal'] = "Nama toko sudah digunakan.";
        header("Location: tambah.php");
        exit;
    }

    // Simpan toko
    $insert = mysqli_query($conn, "INSERT INTO toko (nama_toko, alamat, telepon) VALUES ('$nama_toko', '$alamat', '$telepon')");
    if ($insert) {
        $id_toko_baru = mysqli_insert_id($conn);

        // Tambahkan stok kosong untuk semua barang di toko baru
        $barang = mysqli_query($conn, "SELECT id_barang FROM barang");
        while ($b = mysqli_fetch_assoc($barang)) {
            $id_barang = $b['id_barang'];
            mysqli_query($conn, "INSERT INTO stock (id_barang, id_toko, stock) VALUES ($id_barang, $id_toko_baru, 0)");
        }

        $_SESSION['berhasil'] = "Toko berhasil ditambahkan.";
        header("Location: toko.php");
        exit;
    } else {
        $_SESSION['gagal'] = "Gagal menambahkan toko.";
        header("Location: tambah.php");
        exit;
    }
}
?>

<?php include('../layouts/header.php'); ?>

<div class="container mt-4">
    <h4>Tambah Toko Baru</h4>

    <?php if (isset($_SESSION['gagal'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['gagal']; unset($_SESSION['gagal']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nama_toko" class="form-label">Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="telepon" class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="toko.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?php include('../layouts/footer.php'); ?>
