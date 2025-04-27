<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Barang";
include('../layouts/header.php');
require_once('../../../config.php');

if (isset($_POST['submit'])) {

    $namabarang = htmlspecialchars($_POST['namabarang']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $stock = intval($_POST['stock']);

    $icon_validasi = "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($namabarang)) {
            $pesan_kesalahan[] = "$icon_validasi Nama barang wajib diisi!";
        }
        if (empty($stock) && $stock !== 0) {
            $pesan_kesalahan[] = "$icon_validasi stock barang wajib diisi!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $query = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock) VALUES ('$namabarang', '$deskripsi', $stock)");

            $_SESSION['berhasil'] = "Barang berhasil ditambahkan!";
            header('Location: barang.php');
            exit;
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="tambah_barang.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="namabarang">Nama Barang</label>
                                <input type="text" name="namabarang" class="form-control" value="<?php if (isset($_POST['namabarang'])) echo $_POST['namabarang'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control"><?php if (isset($_POST['deskripsi'])) echo $_POST['deskripsi'] ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="stock">Stock</label>
                                <input type="number" name="stock" class="form-control" value="<?php if (isset($_POST['stock'])) echo $_POST['stock'] ?>">
                            </div>
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layouts/footer.php') ?>