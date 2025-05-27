<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Barang";
include('../layouts/header.php');
require_once('../../../config.php');

if (isset($_POST['submit'])) {

    $kodebarang = htmlspecialchars($_POST['kode_barang']);
    $namabarang = htmlspecialchars($_POST['nama_barang']);
    $hargapokok = intval($_POST['harga_pokok']);
    $hargajual = intval($_POST['harga_jual']);
    $minimalstock = intval($_POST['minimal_stock']);
    $laba = $hargajual - $hargapokok;
    $id_toko = $_SESSION['id_toko'];

    $icon_validasi = "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (empty($kodebarang)) {
            $pesan_kesalahan[] = "$icon_validasi Kode barang wajib diisi!";
        }
        if (empty($namabarang)) {
            $pesan_kesalahan[] = "$icon_validasi Nama barang wajib diisi!";
        }
        if (empty($hargapokok) && $hargapokok !== 0) {
            $pesan_kesalahan[] = "$icon_validasi harga pokok barang wajib diisi!";
        }
        if (empty($hargajual) && $hargajual !== 0) {
            $pesan_kesalahan[] = "$icon_validasi harga jual barang wajib diisi!";
        }
        if (empty($minimalstock) && $minimalstock !== 0) {
            $pesan_kesalahan[] = "$icon_validasi minimal stock barang wajib diisi!";
        }



        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            mysqli_begin_transaction($conn);
            try {
                // insert ke tabel stock
                $stmtStock = mysqli_prepare($conn, "INSERT INTO stock (stock, id_toko) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmtStock, "ii", $minimalstock, $id_toko);
                mysqli_stmt_execute($stmtStock);
                $id_stock = mysqli_insert_id($conn);


                // Insert ke tabel barang
                $stmtBarang = mysqli_prepare($conn, "INSERT INTO barang 
                (kode_barang, nama_barang, harga_pokok, harga_jual, laba, minimal_stock)
                VALUES (?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtBarang, "ssiiii", $kodebarang, $namabarang, $hargapokok, $hargajual, $laba, $minimalstock);
                mysqli_stmt_execute($stmtBarang);
                $id_barang_baru = mysqli_insert_id($conn);

                // update id_barang di table stock
                $stmtUpdateStock = mysqli_prepare($conn, "UPDATE stock SET id_barang = ? WHERE id_stock = ?");
                mysqli_stmt_bind_param($stmtUpdateStock, "ii", $id_barang_baru, $id_stock);
                mysqli_stmt_execute($stmtUpdateStock);
                mysqli_stmt_close($stmtUpdateStock);
                mysqli_stmt_close($stmtBarang);

                mysqli_commit($conn);
                $_SESSION['berhasil'] = "Barang berhasil ditambahkan!";
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $_SESSION['gagal'] = "Barang gagal ditambahkan!" . $e->getMessage();;
            }
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
                                <label for="kode_barang">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" value="<?php if (isset($_POST['kode_barang'])) echo $_POST['kode_barang'] ?>">
                                <div class="mb-3">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" value="<?php if (isset($_POST['nama_barang'])) echo $_POST['nama_barang'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="harga_pokok">Harga Pokok</label>
                                    <input type="number" name="harga_pokok" class="form-control" value="<?php if (isset($_POST['harga_pokok'])) echo $_POST['harga_pokok'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="harga_jual">Harga Jual</label>
                                    <input type="number" name="harga_jual" class="form-control" value="<?php if (isset($_POST['harga_jual'])) echo $_POST['harga_jual'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="minimal_stock">Minimal Stock</label>
                                    <input type="number" name="minimal_stock" class="form-control" value="<?php if (isset($_POST['minimal_stock'])) echo $_POST['minimal_stock'] ?>">
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