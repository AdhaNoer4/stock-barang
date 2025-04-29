<?php
require_once('proses_masuk.php');
$judul = "Tambah Pengguna";
include('../layouts/header.php');


$barang = mysqli_query($conn, "SELECT * FROM stock ORDER BY idbarang");
$result = mysqli_query($conn, "SELECT rs.*, s.namabarang, u.nama FROM riwayat_stok rs JOIN stock s ON rs.idbarang = s.idbarang JOIN user u ON rs.id_user = u.id_user WHERE aksi = 'masuk' ORDER BY rs.tanggal DESC");
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Stock Masuk</h1>
    <div class="page-body">
        <div class="container-xl">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="idbarang">Nama Barang</label>
                                    <select name="idbarang" class="form-control">
                                        <option value="">--Pilih Barang--</option>
                                        <?php while ($row = mysqli_fetch_assoc($barang)): ?>
                                            <option value="<?= $row['idbarang'] ?>"><?= $row['namabarang'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="qty">Jumlah Masuk</label>
                                    <input type="qty" name="qty" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan">Keterangan</label>
                                    <input type="keterangan" name="keterangan" class="form-control">
                                </div>
                                <div class="mb-3 text-end">
                                    <button type="submit" class="btn btn-primary" name="submit">Masukan</button>
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal / Jam</th>
                            <th>Nama</th>
                            <!-- <th>Aksi</th> -->
                            <th>Jumlah</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) === 0) { ?>
                            <tr>
                                <td colspan="5">Data kosong, Silahkan tambah data baru</td>
                            </tr>
                        <?php } else { ?>
                            <?php $no = 1;
                            while ($riwayat = mysqli_fetch_array($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= $riwayat['tanggal']; ?></td>
                                    <td><?= $riwayat['namabarang']; ?></td>
                                    <!-- <td><?= $riwayat['aksi'] ?></td> -->
                                    <td><?= $riwayat['jumlah'] ?></td>
                                    <td><?= $riwayat['nama'] ?></td>

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

<?php include('../layouts/footer.php') ?>