<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Pengguna";
include('../layouts/header.php');
require_once('../../../config.php');

$result = mysqli_query($conn, "SELECT 
    user.id_user,
    user.nama,
    user.email,
    user.role,
    toko.nama_toko
FROM user
LEFT JOIN toko ON user.id_toko = toko.id_toko;
");

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800"><?= $judul ?></h1>
    <a href="tambah.php" class="btn btn-primary mb-2">Tambah Data</a>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>E-mail</th>
                            <th>Role</th>
                            <th>Toko</th>
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
                            while ($user = mysqli_fetch_array($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= $user['nama']; ?></td>
                                    <td><?= $user['email']; ?></td>
                                    <td><?= $user['role'] ?></td>
                                    <td><?= $user['nama_toko'] ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $user['id_user'] ?>" class="btn btn-success"><i class="far fa-edit"></i></a>
                                        <a href="hapus.php?id=<?= $user['id_user'] ?>" class="btn btn-danger tombol-hapus"><i class="far fa-trash-alt"></i></a>
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