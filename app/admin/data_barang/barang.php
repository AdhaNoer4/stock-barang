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

// Base query
$query = "
    SELECT 
        barang.id_barang, 
        barang.kode_barang, 
        barang.nama_barang, 
        barang.harga_pokok, 
        barang.harga_jual, 
        barang.minimal_stock, 
        stock.stock, 
        stock.id_toko, 
        stock.id_stock,
        toko.nama_toko
    FROM barang
    LEFT JOIN stock ON barang.id_barang = stock.id_barang
    LEFT JOIN toko ON stock.id_toko = toko.id_toko
";

// Tambah filter pencarian jika ada keyword
if (!empty($keyword)) {
    $query .= " WHERE 
        barang.nama_barang LIKE '%$keyword%' OR
        barang.kode_barang LIKE '%$keyword%' OR
        toko.nama_toko LIKE '%$keyword%'";
}

$result = mysqli_query($conn, $query);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Barang</h1>

    <div class="row mb-3">
        <div class="col-md-6">
            <a href="tambah_barang.php" class="btn btn-primary">Tambah Data</a>
        </div>

    <!-- Data Barang -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= number_format($row['harga_pokok']) ?></td>
                                    <td><?= number_format($row['harga_jual']) ?></td>
                                    <td><?= $row['minimal_stock'] ?></td>
                                    <td><?= $row['stock'] ?></td>
                                    <td><?= $row['nama_toko'] ?></td>
                                    <td>
                                        <a href="edit_barang.php?id=<?= $row['id_barang'] ?>" class="btn btn-success btn-sm"><i class="far fa-edit"></i></a>
                                        <a href="hapus_barang.php?id=<?= $row['id_stock'] ?>&id_barang=<?= $row['id_barang'] ?>" class="btn btn-danger btn-sm tombol-hapus"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php include "../layouts/footer.php" ?>
