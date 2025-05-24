<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
require_once('../../../config.php');
include('../layouts/header.php');

if (isset($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];

    $query = "
        SELECT 
            r.tanggal, 
            b.kode_barang, 
            b.nama_barang, 
            b.harga_pokok, 
            b.harga_jual, 
            b.laba, 
            b.minimal_stock,
            b.id_barang,
            b.id_toko,
            b.id_stock,
            r.jumlah,
            r.jenis,
            r.id_toko AS toko_riwayat
        FROM riwayat_stok r
        JOIN barang b ON r.id_barang = b.id_barang
        WHERE DATE(r.tanggal) = '$tanggal'
    ";

    $result = mysqli_query($conn, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $stok_toko = [];
    foreach ($rows as $row) {
        $id_barang = $row['id_barang'];
        if (!isset($stok_toko[$id_barang])) {
            $stok_toko[$id_barang] = [];
        }
        $stok_toko[$id_barang][$row['toko_riwayat']] = ($stok_toko[$id_barang][$row['toko_riwayat']] ?? 0) + ($row['jenis'] === 'masuk' ? $row['jumlah'] : -$row['jumlah']);
    }
}
?>
<!-- Begin Page Content -->
<div class="container mt-4">
    <h4 class="mb-4">Laporan Stock</h4>
    <form method="GET" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="tanggal" class="form-label">Pilih Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= isset($_GET['tanggal']) ? $_GET['tanggal'] : '' ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
            </div>
        </div>
    </form>
    <form method="POST" action="../../../cetak_pdf.php" target="_blank">
        <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
        <button type="submit" class="btn btn-danger mt-3">Download PDF</button>
    </form>

    <form class="mb-5" method="POST" action="export_excel.php" target="_blank">
        <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
        <button type="submit" class="btn btn-success mt-3">Download Excel</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Pokok</th>
                <th>Harga Jual</th>
                <th>Laba</th>
                <th>Stock Toko 1</th>
                <th>Stock Toko 2</th>
                <th>Stock Toko 3</th>
                <th>Stock Total</th>
                <th>Minimal Stock</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if (empty($rows)) {
                echo '<tr><td colspan="13" class="text-center">Data tidak ditemukan </td></tr>';
                return;
            }
            foreach ($rows as $data):
                $idb = $data['id_barang'];
                $total_stock = array_sum($stok_toko[$idb]);
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($data['tanggal'])) ?></td>
                    <td><?= $data['kode_barang'] ?></td>
                    <td><?= $data['nama_barang'] ?></td>
                    <td><?= number_format($data['harga_pokok']) ?></td>
                    <td><?= number_format($data['harga_jual']) ?></td>
                    <td><?= number_format($data['laba']) ?></td>
                    <td><?= $stok_toko[$idb][1] ?? 0 ?></td>
                    <td><?= $stok_toko[$idb][2] ?? 0 ?></td>
                    <td><?= $stok_toko[$idb][3] ?? 0 ?></td>
                    <td><?= $total_stock ?></td>
                    <td><?= $data['minimal_stock'] ?></td>
                    <td><?= $data['jenis'] == 'masuk' ? '+' : '-' ?><?= $data['jumlah'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- /.container-fluid -->

<?php include('../layouts/footer.php') ?>