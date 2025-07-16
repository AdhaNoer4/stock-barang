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
require_once('../../../config.php');
include('../layouts/header.php');

// Ambil daftar toko
$toko_query = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko ORDER BY id_toko");
$toko_list = mysqli_fetch_all($toko_query, MYSQLI_ASSOC);

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

// Mulai susun query
$query = "
    SELECT 
        b.kode_barang, 
        b.nama_barang, 
        b.harga_pokok, 
        b.harga_jual, 
        b.laba, 
        b.minimal_stock,
        b.id_barang,
";

// Tambahkan kolom stok per toko dari subquery
foreach ($toko_list as $toko) {
    $id_toko = $toko['id_toko'];
    $query .= "IFNULL(ss.stok_toko_$id_toko, 0) AS stok_toko_$id_toko, ";
}

$query .= "
    IFNULL(ss.total_stock, 0) AS total_stock,
    IFNULL(rs.penambahan_stok, 0) AS penambahan_stok
FROM barang b
LEFT JOIN (
    SELECT 
        id_barang,
";

// Subquery stok per toko
foreach ($toko_list as $toko) {
    $id_toko = $toko['id_toko'];
    $query .= "SUM(CASE WHEN id_toko = $id_toko THEN stock ELSE 0 END) AS stok_toko_$id_toko, ";
}

$query .= "SUM(stock) AS total_stock
    FROM stock
    GROUP BY id_barang
) ss ON ss.id_barang = b.id_barang

LEFT JOIN (
    SELECT 
        id_barang, 
        SUM(jumlah) AS penambahan_stok
    FROM riwayat_stok
    WHERE jenis = 'masuk' AND DATE(tanggal) = '$tanggal'
    GROUP BY id_barang
) rs ON rs.id_barang = b.id_barang

ORDER BY b.kode_barang
";

// Eksekusi dan ambil data
$result = mysqli_query($conn, $query);
$data_stok = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="container mt-4">
    <h4 class="mb-4">Laporan Stok per Tanggal</h4>
    <form method="GET" class="mb-2">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="tanggal" class="form-label">Pilih Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
            </div>
        </div>
    </form>

    <form method="POST" action="cetak_pdf.php" target="_blank">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        <button type="submit" class="btn btn-danger mt-3">Download PDF</button>
    </form>

    <form method="POST" action="cetak_excel.php" target="_blank">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        <button type="submit" class="btn btn-success mt-2">Download Excel</button>
    </form>

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Pokok</th>
                <th>Harga Jual</th>
                <th>Laba</th>
                <?php foreach ($toko_list as $toko): ?>
                    <th><?= htmlspecialchars($toko['nama_toko']) ?></th>
                <?php endforeach; ?>
                <th>Total Stok</th>
                <th>Minimal Stok</th>
                <th>Penambahan Stok (<?= date('d-m-Y', strtotime($tanggal)) ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($data_stok)) {
                echo '<tr><td colspan="' . (8 + count($toko_list)) . '" class="text-center">Data tidak ditemukan.</td></tr>';
            } else {
                $no = 1;
                foreach ($data_stok as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= number_format($row['harga_pokok']) ?></td>
                        <td><?= number_format($row['harga_jual']) ?></td>
                        <td><?= number_format($row['laba']) ?></td>
                        <?php foreach ($toko_list as $toko): ?>
                            <td><?= $row["stok_toko_" . $toko['id_toko']] ?? 0 ?></td>
                        <?php endforeach; ?>
                        <td><?= $row['total_stock'] ?? 0 ?></td>
                        <td><?= $row['minimal_stock'] ?></td>
                        <td><?= $row['penambahan_stok'] ?></td>
                    </tr>
            <?php endforeach;
            }
            ?>
        </tbody>
    </table>

</div>

<?php include('../layouts/footer.php') ?>