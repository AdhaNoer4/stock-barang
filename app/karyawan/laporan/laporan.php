<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
require_once('../../../config.php');
include('../layouts/header.php');

$id_toko = $_SESSION['id_toko'];

if (isset($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
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
    r.jumlah,
    r.jenis,
    s.stock,
    s.id_toko
FROM riwayat_stok r
LEFT JOIN barang b ON r.id_barang = b.id_barang
LEFT JOIN stock s ON s.id_barang = b.id_barang AND s.id_toko = $id_toko
WHERE r.id_toko = $id_toko AND DATE(r.tanggal) = '$tanggal'

";

    // Filter jenis jika dipilih
    if ($jenis !== '') {
        $query .= " AND r.jenis = '$jenis'";
    }

    $result = mysqli_query($conn, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
                <label for="jenis" class="form-label">Jenis</label>
                <select name="jenis" id="jenis" class="form-select">
                    <option value="">Semua</option>
                    <option value="masuk" <?= isset($_GET['jenis']) && $_GET['jenis'] === 'masuk' ? 'selected' : '' ?>>Masuk</option>Add commentMore actions
                    <option value="keluar" <?= isset($_GET['jenis']) && $_GET['jenis'] === 'keluar' ? 'selected' : '' ?>>Keluar</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
            </div>
        </div>
    </form>
    <form method="POST" action="cetak_pdf.php" target="_blank">
        <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
        <button type="submit" class="btn btn-danger mt-3">Download PDF</button>
    </form>

    <form class="mb-5" method="POST" action="cetak_excel.php" target="_blank">
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
                <th>Stock</th>
                <th>Minimal Stock</th>
                <th>Penambahan Stock</th>
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

            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($data['tanggal'])) ?></td>
                    <td><?= $data['kode_barang'] ?></td>
                    <td><?= $data['nama_barang'] ?></td>
                    <td><?= number_format($data['harga_pokok']) ?></td>
                    <td><?= number_format($data['harga_jual']) ?></td>
                    <td><?= number_format($data['laba']) ?></td>
                    <td><?= $data['stock'] ?? 0 ?></td>
                    <td><?= $data['minimal_stock'] ?></td>
                    <td><?= $data['jenis'] == 'masuk' ? '+' . $data['jumlah'] : '-' . $data['jumlah'] ?></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- /.container-fluid -->

<?php include('../layouts/footer.php') ?>