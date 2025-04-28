<?php
require_once('proses_masuk.php');
$judul = "Tambah Pengguna";
include('../layouts/header.php');


$barang = mysqli_query($conn, "SELECT * FROM stock ORDER BY idbarang");
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
</div>
<!-- /.container-fluid -->

<?php include('../layouts/footer.php') ?>