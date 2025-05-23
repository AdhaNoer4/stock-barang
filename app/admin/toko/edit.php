<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../../auth/login.php?pesan=tolak_akses");
}
$judul = "Edit Pengguna";
include('../layouts/header.php');
require_once('../../../config.php');

if (isset($_POST['edit'])) {

    $id = htmlspecialchars($_POST['id']);
    $nama_toko = htmlspecialchars($_POST['nama_toko']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);

    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pesan_kesalahan = [];
        if (empty($nama_toko)) {
            $pesan_kesalahan[] = "$icon_validasi Nama Toko  wajib diisi!";
        }
        if (empty($alamat)) {
            $pesan_kesalahan[] = "$icon_validasi Alamat wajib diisi!";
        }
        if (empty($telepon)) {
            $pesan_kesalahan[] = "$icon_validasi Telepon wajib diisi!";
        }
        if (!preg_match('/^[0-9]{10,15}$/', $telepon)) {
            $pesan_kesalahan[] = "$icon_validasi Telepon tidak valid!";
        }
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {

            $user = mysqli_query($conn, "UPDATE toko SET
                nama_toko = '$nama_toko',
                alamat = '$alamat',
                telepon = '$telepon'
                WHERE id_toko = '$id'
            ");
            if (!$user) {
                $_SESSION['gagal'] = 'Data gagal diupdate';
                header('Location: edit.php?id=' . $id);
                exit;
            }

            $_SESSION['berhasil'] = 'Data berhasil diupdate';
            header('Location: toko.php');
            exit;
        }
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($conn, "SELECT * FROM toko WHERE id_toko = '$id'");

while ($toko = mysqli_fetch_array($result)) {
    $nama_toko = $toko['nama_toko'];
    $alamat = $toko['alamat'];
    $telepon = $toko['telepon'];
}
?>

<div class="page-body">
    <div class="container-xl">
        <h1 class="h3 mb-2 text-gray-800"><?= $judul ?></h1>
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama_toko">Nama Toko</label>
                                <input type="text" name="nama_toko" class="form-control" value="<?= $nama_toko ?>">
                            </div>
                            <div class="mb-3">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" class="form-control" value="<?= $alamat ?>"></input>
                            </div>

                            <div class="mb-3">
                                <label for="telepon">Telepon</label>
                                <input type="text" name="telepon" class="form-control" value="<?= $telepon ?>">
                            </div>
                            <input type="hidden" value="<?= $id ?>" name="id">
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary" name="edit">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>


<?php include('../layouts/footer.php') ?>