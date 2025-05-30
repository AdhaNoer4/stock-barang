<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Riwayat";
include('../layouts/header.php');
require_once('../../../config.php');

$id_toko = $_SESSION['id_toko'] ?? null;

$query = "
  SELECT rs.*, u.nama, b.nama_barang 
  FROM riwayat_stok rs 
  JOIN user u ON rs.id_user = u.id_user 
  JOIN barang b ON rs.id_barang = b.id_barang 
  WHERE rs.tanggal = CURDATE()
";

if ($id_toko) {
  $query .= " AND rs.id_toko = '$id_toko'";
}

$query .= " ORDER BY rs.tanggal DESC";

$result = mysqli_query($conn, $query);


?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengguna</h6>
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


<?php include "../layouts/footer.php" ?>