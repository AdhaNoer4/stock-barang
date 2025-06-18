<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Data Barang";
include('../layouts/header.php');
require_once('../../../config.php');

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$filter_toko = isset($_GET['filter_toko']) ? (int)$_GET['filter_toko'] : 0;
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total rows
$countQuery = "SELECT COUNT(*) AS total FROM barang b ";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Main query with subqueries for stock and toko
$query = "
    SELECT 
        b.id_barang, 
        b.kode_barang, 
        b.nama_barang, 
        b.harga_pokok, 
        b.harga_jual, 
        b.minimal_stock,
        (
            SELECT s.stock FROM stock s WHERE s.id_barang = b.id_barang ".($filter_toko ? "AND s.id_toko = $filter_toko" : "ORDER BY s.id_toko LIMIT 1")."
        ) AS stock,
        (
            SELECT t.nama_toko FROM stock s 
            JOIN toko t ON s.id_toko = t.id_toko 
            WHERE s.id_barang = b.id_barang ".($filter_toko ? "AND t.id_toko = $filter_toko" : "ORDER BY s.id_toko LIMIT 1")."
        ) AS nama_toko
    FROM barang b
";

if (!empty($keyword)) {
    $query .= " WHERE b.nama_barang LIKE '%$keyword%' OR b.kode_barang LIKE '%$keyword%'";
}

$query .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Barang</h1>

    <div class="row">
        <div class="col-md-6">
            <a href="tambah_barang.php" class="btn btn-primary mb-2">Tambah Data</a>
            <a href="export_barang.php" class="btn btn-success mb-2">Export Excel</a>
        </div>
    </div>

    <form method="GET" class="form-inline mb-3">
        <label class="mr-2">Filter Toko:</label>
        <select name="filter_toko" class="form-control mr-2" onchange="this.form.submit()">
            <option value="">Semua Toko</option>
            <?php
            $toko_list = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko ORDER BY nama_toko");
            while ($toko = mysqli_fetch_assoc($toko_list)) {
                $selected = ($toko['id_toko'] == $filter_toko) ? 'selected' : '';
                echo "<option value='{$toko['id_toko']}' $selected>{$toko['nama_toko']}</option>";
            }
            ?>
        </select>
        <input type="text" name="keyword" class="form-control mr-2" placeholder="Cari barang..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="text-center">
                        <tr>
                            <th>No.</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga Pokok</th>
                            <th>Harga Jual</th>
                            <th>Minimal Stock</th>
                            <th>Stock</th>
                            <th>Nama Toko</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) === 0): ?>
                            <tr>
                                <td colspan="9" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= number_format($row['harga_pokok']) ?></td>
                                    <td><?= number_format($row['harga_jual']) ?></td>
                                    <td><?= $row['minimal_stock'] ?></td>
                                    <td><?= $row['stock'] ?? 0 ?></td>
                                    <td><?= $row['nama_toko'] ?? '-' ?></td>
                                    <td>
                                        <a href="edit_barang.php?id=<?= $row['id_barang'] ?>" class="btn btn-success btn-sm"><i class="far fa-edit"></i></a>
                                        <a href="hapus_barang.php?id_barang=<?= $row['id_barang'] ?>" class="btn btn-danger btn-sm tombol-hapus"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&filter_toko=<?= $filter_toko ?>&keyword=<?= urlencode($keyword) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include "../layouts/footer.php" ?>
