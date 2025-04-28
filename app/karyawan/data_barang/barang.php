<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Data Barang";
include('../layouts/header.php');
require_once('../../../config.php');

$result = mysqli_query($conn, "SELECT idbarang, namabarang, deskripsi, stock FROM stock");

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Barang</h1>
    <a href="tambah_barang.php" class="btn btn-primary mb-2">Tambah Data</a>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Stock</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) === 0) { ?>
                            <tr>
                                <td colspan="5">Data kosong, Silahkan tambah data baru</td>
                            </tr>
                        <?php } else { ?>
                            <?php $no = 1;
                            while ($stock = mysqli_fetch_array($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= $stock['namabarang']; ?></td>
                                    <td><?= $stock['deskripsi']; ?></td>
                                    <td><?= $stock['stock'] ?></td>
                                    <td>
                                        <a href="edit_barang.php?id=<?= $stock['idbarang'] ?>" class="btn btn-success"><i class="far fa-edit"></i></a>
                                        <a href="hapus_barang.php?id=<?= $stock['idbarang'] ?>" class="btn btn-danger tombol-hapus"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php } ?>


                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->


<?php include "../layouts/footer.php" ?>