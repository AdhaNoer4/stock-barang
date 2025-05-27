<?php
require('proses_keluar.php');
$judul = "Stock Keluar";
include('../layouts/header.php');


$barang = mysqli_query($conn, "SELECT b.nama_barang, s.id_barang FROM stock s JOIN barang b ON s.id_barang = b.id_barang WHERE id_toko = '$_SESSION[id_toko]' ORDER BY b.nama_barang ASC");
$result = mysqli_query($conn, "SELECT rs.*,s.id_barang, u.nama, b.nama_barang FROM riwayat_stok rs JOIN stock s ON rs.id_barang = s.id_barang JOIN user u ON rs.id_user = u.id_user JOIN barang b ON rs.id_barang = b.id_barang WHERE jenis = 'keluar' AND tanggal = CURDATE() ORDER BY rs.tanggal DESC");
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800"><?= $judul; ?></h1>
    <div class="page-body">
        <div class="container-xl">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="id_barang">Nama Barang</label>
                                    <select name="id_barang" class="form-control">
                                        <option value="">--Pilih Barang--</option>
                                        <?php while ($row = mysqli_fetch_assoc($barang)): ?>
                                            <option value="<?= $row['id_barang'] ?>"><?= $row['nama_barang'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlah">Jumlah Keluar</label>
                                    <input type="number" name="jumlah" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="penerima">Penerima</label>
                                    <input type="text" name="penerima" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control">
                                </div>

                                <div class="mb-3 text-end">
                                    <button type="submit" class="btn btn-primary" name="submit">Keluarkan</button>
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
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Keluar Hari ini</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>User</th>
                            <th>Jenis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) === 0) { ?>
                            <tr>
                                <td colspan="6">Data kosong, Silahkan tambah data baru</td>
                            </tr>
                        <?php } else { ?>
                            <?php $no = 1;
                            while ($riwayat = mysqli_fetch_array($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= $riwayat['tanggal']; ?></td>
                                    <td><?= $riwayat['nama_barang']; ?></td>
                                    <td><?= $riwayat['jumlah'] ?></td>
                                    <td><?= $riwayat['nama'] ?></td>
                                    <td><?= $riwayat['jenis'] ?></td>

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