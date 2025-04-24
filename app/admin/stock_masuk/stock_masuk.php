<?php
require_once('proses_masuk.php');
$judul = "Tambah Pengguna";
include('../layouts/header.php');


$barang = mysqli_query($conn, "SELECT * FROM stock ORDER BY idbarang DESC LIMIT 1");
?>
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
                                <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>


<?php include('../layouts/footer.php') ?>