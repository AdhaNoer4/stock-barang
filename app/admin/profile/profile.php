<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Profile";
include('../layouts/header.php');
require_once('../../../config.php');

$id = $_SESSION['id_user'];
$result = mysqli_query($conn, "SELECT 
    user.id_user,
    user.nama,
    user.email,
    user.role,
    toko.nama_toko
FROM user
LEFT JOIN toko ON user.id_toko = toko.id_toko
WHERE user.id_user = $id");

?>

<?php while ($user = mysqli_fetch_array($result)) : ?>

    <div class="page-body">
        <div class="container-xl">

            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card mb-5">
                        <div class="card-body ">
                            <h4 class="text-center">Profile</h4>

                            <table class="table mt-3">
                                <tr>
                                    <td>Nama</td>
                                    <td>: <?= $user['nama']; ?></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>: <?= $user['email']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>: <?= $user['role']; ?></td>
                                </tr>
                                <tr>
                                    <td>Toko</td>
                                    <td>: <?= $user['nama_toko']; ?></td>
                                </tr>
                            </table>
                            <div class="text-center">
                                <a class="btn btn-primary" href="../user/edit.php?id=<?= $id ?>">Ubah</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php endwhile; ?>
<?php include('../layouts/footer.php') ?>