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
    $nama = htmlspecialchars($_POST['nama']);
    $role = htmlspecialchars($_POST['role']);
    $email = htmlspecialchars($_POST['email']);

    if (empty($_POST['password'])) {
        $password = $_POST['password_lama'];
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }


    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($nama)) {
            $pesan_kesalahan[] = "$icon_validasi Nama  wajib diisi!";
        }
        if (empty($password)) {
            $pesan_kesalahan[] = "$icon_validasi Password wajib diisi!";
        }
        if (empty($role)) {
            $pesan_kesalahan[] = "$icon_validasi Role wajib diisi!";
        }
        if (empty($email)) {
            $pesan_kesalahan[] = "$icon_validasi E-mail wajib diisi!";
        }
        if ($_POST['password'] !== $_POST['ulangi_password']) {
            $pesan_kesalahan[] = "$icon_validasi Password tidak cocok!";
        }
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {

            $user = mysqli_query($conn, "UPDATE user SET
                nama = '$nama',
                email = '$email',
                password = '$password',
                role = '$role'
                

                WHERE id_user = '$id'
            
            
            ");

            $_SESSION['berhasil'] = 'Data berhasil diupdate';
            header('Location: user.php');
            exit;
        }
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id'");

while ($pegawai = mysqli_fetch_array($result)) {
    $nama = $pegawai['nama'];
    $password = $pegawai['password'];
    $role = $pegawai['role'];
    $email = $pegawai['email'];
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" class="form-control" value="<?= $nama ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email">E-mail</label>
                                <input type="email" name="email" class="form-control" value="<?= $email ?>">
                            </div>
                            <div class="mb-3">
                                <label for="role">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">--Pilih role--</option>
                                    <option <?php if ($role == 'admin') {
                                                echo 'selected';
                                            } ?> value="admin">Admin</option>
                                    <option <?php if ($role == 'karyawan') {
                                                echo 'selected';
                                            } ?> value="karyawan">Karyawan</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="hidden" name="password_lama" value="<?= $password ?>">
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="ulangi_password">Ulangi Password</label>
                                <input type="password" name="ulangi_password" class="form-control">
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