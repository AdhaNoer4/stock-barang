<?php
session_start();
require_once('../../../config.php');

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_barang'])) {
    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $harga_pokok = (int) $_POST['harga_pokok'];
    $harga_jual = (int) $_POST['harga_jual'];
    $laba = $harga_jual - $harga_pokok;
    $minimal_stock = (int) $_POST['minimal_stock'];

    // Cek kode barang agar tidak duplikat
    $cek = mysqli_query($conn, "SELECT * FROM barang WHERE kode_barang = '$kode_barang'");
    if (mysqli_num_rows($cek) === 0) {

        // Tambahkan ke tabel barang
        mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, harga_pokok, harga_jual, laba, minimal_stock)
            VALUES ('$kode_barang', '$nama_barang', $harga_pokok, $harga_jual, $laba, $minimal_stock)");

        $id_barang = mysqli_insert_id($conn);

        // Ambil semua toko
        $toko_result = mysqli_query($conn, "SELECT id_toko FROM toko");
        while ($toko = mysqli_fetch_assoc($toko_result)) {
            $id_toko = $toko['id_toko'];

            // Cek apakah sudah ada entry stok untuk kombinasi ini
            $cek_stock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = $id_barang AND id_toko = $id_toko");
            if (mysqli_num_rows($cek_stock) === 0) {
                mysqli_query($conn, "INSERT INTO stock (id_barang, id_toko, stock) VALUES ($id_barang, $id_toko, 0)");
            }
        }

        // Redirect untuk hindari submit ulang
        header("Location: barang.php?pesan=berhasil");
        exit;
    } else {
        echo "<script>alert('Kode barang sudah ada!');</script>";
    }
}

?>

<?php include('../layouts/header.php'); ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Barang Baru</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                        </div>
                        <div class="mb-3">
                            <label for="harga_pokok" class="form-label">Harga Pokok</label>
                            <input type="number" class="form-control" id="harga_pokok" name="harga_pokok" required>
                        </div>
                        <div class="mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required>
                        </div>
                        <div class="mb-3">
                            <label for="minimal_stock" class="form-label">Minimal Stok</label>
                            <input type="number" class="form-control" id="minimal_stock" name="minimal_stock" required>
                        </div>
                        <button type="submit" name="simpan_barang" class="btn btn-success">Simpan Barang</button>
                        <a href="barang.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Import Excel Data Stok Barang</h5>
                </div>
                <div class="card-body">
                    <form action="proses_import_stock.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="file_excel" accept=".xls,.xlsx" required>
                        <button class="btn btn-success mt-2" type="submit" name="import">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include('../layouts/footer.php'); ?>