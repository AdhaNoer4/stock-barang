<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Tambah Barang";
include('../layouts/header.php');
require_once('../../../config.php');

$queryToko = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko");

if (isset($_POST['submit'])) {
    $kodebarang = htmlspecialchars($_POST['kode_barang']);
    $namabarang = htmlspecialchars($_POST['nama_barang']);
    $hargapokok = intval($_POST['harga_pokok']);
    $hargajual = intval($_POST['harga_jual']);
    $minimalstock = intval($_POST['minimal_stock']);
    $id_toko = intval($_POST['id_toko']);
    $laba = $hargajual - $hargapokok;

    $icon_validasi = "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M5 12l5 5l10 -10' /></svg>";

    $pesan_kesalahan = [];

    if (empty($id_toko)) {
        $pesan_kesalahan[] = "$icon_validasi Toko wajib dipilih!";
    }
    if (empty($kodebarang)) {
        $pesan_kesalahan[] = "$icon_validasi Kode barang wajib diisi!";
    }
    if (empty($namabarang)) {
        $pesan_kesalahan[] = "$icon_validasi Nama barang wajib diisi!";
    }
    if (!isset($_POST['harga_pokok']) || $_POST['harga_pokok'] === "") {
        $pesan_kesalahan[] = "$icon_validasi Harga pokok barang wajib diisi!";
    }
    if (!isset($_POST['harga_jual']) || $_POST['harga_jual'] === "") {
        $pesan_kesalahan[] = "$icon_validasi Harga jual barang wajib diisi!";
    }
    if (!isset($_POST['minimal_stock']) || $_POST['minimal_stock'] === "") {
        $pesan_kesalahan[] = "$icon_validasi Minimal stock barang wajib diisi!";
    }

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        mysqli_begin_transaction($conn);
        try {
            // Insert ke tabel stock
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

            // Update id_barang di tabel stock
            $stmtUpdateStock = mysqli_prepare($conn, "UPDATE stock SET id_barang = ? WHERE id_stock = ?");
            mysqli_stmt_bind_param($stmtUpdateStock, "ii", $id_barang_baru, $id_stock);
            mysqli_stmt_execute($stmtUpdateStock);

            // Tutup statement
            mysqli_stmt_close($stmtStock);
            mysqli_stmt_close($stmtBarang);
            mysqli_stmt_close($stmtUpdateStock);

            mysqli_commit($conn);
            $_SESSION['berhasil'] = "Barang berhasil ditambahkan!";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['gagal'] = "Barang gagal ditambahkan! " . $e->getMessage();
        }

        header('Location: barang.php');
        exit;
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="tambah_barang.php" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="kode_barang">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" required
                                    value="<?= isset($_POST['kode_barang']) ? htmlspecialchars($_POST['kode_barang']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="nama_barang">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required
                                    value="<?= isset($_POST['nama_barang']) ? htmlspecialchars($_POST['nama_barang']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="harga_pokok">Harga Pokok</label>
                                <input type="number" name="harga_pokok" class="form-control" required
                                    value="<?= isset($_POST['harga_pokok']) ? htmlspecialchars($_POST['harga_pokok']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="harga_jual">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" required
                                    value="<?= isset($_POST['harga_jual']) ? htmlspecialchars($_POST['harga_jual']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="minimal_stock">Minimal Stock</label>
                                <input type="number" name="minimal_stock" class="form-control" required
                                    value="<?= isset($_POST['minimal_stock']) ? htmlspecialchars($_POST['minimal_stock']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="id_toko">Pilih Toko</label>
                                <select name="id_toko" class="form-control" required>
                                    <option value="">-- Pilih Toko --</option>
                                    <?php while ($toko = mysqli_fetch_assoc($queryToko)) : ?>
                                        <option value="<?= $toko['id_toko']; ?>"
                                            <?= (isset($_POST['id_toko']) && $_POST['id_toko'] == $toko['id_toko']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($toko['nama_toko']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
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

<?php include('../layouts/footer.php'); ?>