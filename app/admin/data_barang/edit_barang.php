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

$query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = $idbarang");
$query_stock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang = $idbarang");
$stock = mysqli_fetch_assoc($query_stock);
$barang = mysqli_fetch_assoc($query);

if (isset($_POST['submit'])) {
    $kodebarang = htmlspecialchars($_POST['kode_barang']);
    $namabarang = htmlspecialchars($_POST['nama_barang']);
    $hargapokok = intval($_POST['harga_pokok']);
    $hargajual = intval($_POST['harga_jual']);
    $stocktotal = intval($_POST['stock_total']);
    $minimalstock = intval($_POST['minimal_stock']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        
        if (empty($namabarang)) {
            $pesan_kesalahan[] = "Nama barang wajib diisi!";
        }
        if (!isset($hargapokok)) {
            $pesan_kesalahan[] = "Harga pokok barang wajib diisi!";
        }
        if (!isset($hargajual)) {
            $pesan_kesalahan[] = "Harga jual barang wajib diisi!";
        }
        if (!isset($stocktotal)) {
            $pesan_kesalahan[] = "Stock total barang wajib diisi!";
        }
        if (!isset($minimalstock)) {
            $pesan_kesalahan[] = "Minimal stock barang wajib diisi!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $update_stock = mysqli_query($conn, "UPDATE stock SET stock = $stocktotal WHERE id_barang = $idbarang");

            $update = mysqli_query($conn, "UPDATE barang SET 
            kode_barang = '$kodebarang', 
            nama_barang = '$namabarang', 
            harga_pokok = $hargapokok, 
            harga_jual = $hargajual, 
            minimal_stock = $minimalstock 
            WHERE id_barang = $idbarang");

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
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="kode_barang">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" value="<?= $barang['kode_barang']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="nama_barang">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="<?= $barang['nama_barang']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="harga_pokok">Harga Pokok</label>
                                <input type="number" name="harga_pokok" class="form-control" value="<?= $barang['harga_pokok']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="harga_jual">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" value="<?= $barang['harga_jual']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="stock_total">Stock Total</label>
                                <input type="number" name="stock_total" class="form-control" value="<?= $stock['stock']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="minimal_stock">Minimal Stock</label>
                                <input type="number" name="minimal_stock" class="form-control" value="<?= $barang['minimal_stock']; ?>">
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