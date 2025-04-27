<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = "Edit Barang";
include('../layouts/header.php');
require_once('../../../config.php');

if (!isset($_GET['id'])) {
    header('Location: barang.php');
    exit;
}

$idbarang = intval($_GET['id']);

$query = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = $idbarang");
$barang = mysqli_fetch_assoc($query);

if (isset($_POST['submit'])) {
    $namabarang = htmlspecialchars($_POST['namabarang']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $stock = intval($_POST['stock']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($namabarang)) {
            $pesan_kesalahan[] = "Nama barang wajib diisi!";
        }
        if (!isset($stock)) {
            $pesan_kesalahan[] = "Stok barang wajib diisi!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $update = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi', stock=$stock WHERE idbarang=$idbarang");

            if ($update) {
                $_SESSION['berhasil'] = "Barang berhasil diupdate!";
                header('Location: barang.php');
                exit;
            } else {
                $_SESSION['validasi'] = "Gagal update barang.";
            }
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="namabarang">Nama Barang</label>
                                <input type="text" name="namabarang" class="form-control" value="<?= $barang['namabarang']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control"><?= $barang['deskripsi']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="stock">Stock</label>
                                <input type="number" name="stock" class="form-control" value="<?= $barang['stock']; ?>">
                            </div>

                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary" name="submit">Update</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layouts/footer.php') ?>