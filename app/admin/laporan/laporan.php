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

$result = false;
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    $jenis = $_GET['jenis'];

    $query = "SELECT rs.*, s.namabarang FROM riwayat_stok rs 
              JOIN stock s ON rs.idbarang = s.idbarang 
              WHERE rs.tanggal BETWEEN '$dari' AND '$sampai'";

    if (!empty($jenis)) {
        $query .= " AND rs.jenis = '$jenis'";
    }

    $query .= " ORDER BY rs.tanggal DESC";

    $result = mysqli_query($conn, $query);
}
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Laporan</h1>
    <div class="page-body">
        <div class="container-xl">
            <form method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="dari">Dari Tanggal:</label>
                                    <input type="date" name="dari" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="sampai">Sampai Tanggal:</label>
                                    <input type="date" name="sampai" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis">Jenis</label>
                                    <select name="jenis" id="jenis" class="form-control">
                                        <option value="">-- Semua --</option>
                                        <option value="masuk">Masuk</option>
                                        <option value="keluar">Keluar</option>
                                    </select>
                                </div>
                                <div class="mb-3 text-end">
                                    <button type="submit" class="btn btn-primary" name="submit">Tampilkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!--Table Riwayat Masuk -->
    <div class="card shadow mb-4 mt-4">
        <div class="card-header py-3 flex-row justify-content-between d-flex">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Laporan</h6>
            <?php if (isset($_GET['dari']) && isset($_GET['sampai'])): ?>
                <a href="cetak_excel.php?dari=<?= $_GET['dari']; ?>&sampai=<?= $_GET['sampai']; ?>&jenis=<?= $_GET['jenis']; ?>"
                    class="btn btn-success mt-2" target="_blank">Export Excel</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Barang</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) === 0) { ?>
                            <tr>
                                <td colspan="5">Data kosong, Silahkan tambah data baru</td>
                            </tr>
                        <?php } elseif ($result) { ?>
                            <?php $no = 1;
                            while ($laporan = mysqli_fetch_array($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= $laporan['namabarang']; ?></td>
                                    <td><?= $laporan['jenis']; ?></td>
                                    <td><?= $laporan['jumlah'] ?></td>
                                    <td><?= $laporan['tanggal'] ?></td>

                                </tr>
                            <?php endwhile; ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5">Silakan pilih filter dan klik Tampilkan untuk melihat laporan.</td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include('../layouts/footer.php') ?>