<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Pengguna";
include('../layouts/header.php');
require_once('../../../config.php');

if (isset($_POST['submit'])) {
    $ambil_id = mysqli_query($conn, "SELECT id_user FROM user ORDER BY id_user DESC LIMIT 1");

    if (mysqli_num_rows($ambil_id) > 0) {
        $row = mysqli_fetch_assoc($ambil_id);
        $id_user = $row["id_user"];
    }


    $nama = htmlspecialchars($_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);
    $email = htmlspecialchars($_POST['email']);
    $id_toko = htmlspecialchars($_POST['id_toko']);


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
        if (empty($id_toko)) {
            $pesan_kesalahan[] = "$icon_validasi Toko wajib diisi!";
        }
        if ($_POST['password'] !== $_POST['ulangi_password']) {
            $pesan_kesalahan[] = "$icon_validasi Password tidak cocok!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            mysqli_begin_transaction($conn);

            try {
                // Insert ke tabel user
                $stmtUser = mysqli_prepare($conn, "INSERT INTO user (nama, email, password, role, id_toko) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtUser, "ssssi", $nama, $email, $password, $role, $id_toko);
                mysqli_stmt_execute($stmtUser);

                // Commit transaksi jika semua berhasil
                mysqli_commit($conn);

                $_SESSION['berhasil'] = "Data berhasil ditambahkan!";
                header('Location: user.php');
                exit;
            } catch (Exception $e) {
                // Rollback jika terjadi kesalahan
                mysqli_rollback($conn);
                $_SESSION['gagal'] = "Data gagal ditambahkan! Terjadi kesalahan:" . $e->getMessage();
                header('Location: user.php');
                exit;
            }
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="tambah.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" class="form-control" value="<?php if (isset($_POST['nama'])) echo $_POST['nama'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email">E-mail</label>
                                <input type="email" name="email" class="form-control" value="<?php if (isset($_POST['email'])) echo $_POST['email'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="role">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">--Pilih role--</option>
                                    <option <?php if (isset($_POST['role']) && $_POST['role'] == 'admin') {
                                                echo 'selected';
                                            } ?> value="admin">Admin</option>
                                    <option <?php if (isset($_POST['role']) && $_POST['role'] == 'karyawan') {
                                                echo 'selected';
                                            } ?> value="karyawan">Karyawan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama_toko">Nama Toko</label>
                                <select name="id_toko" id="id_toko" class="form-control" required>
                                    <option value="">-- Pilih Toko --</option>
                                    <?php $query_toko = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko");
                                    while ($row = mysqli_fetch_assoc($query_toko)) : ?>
                                        <option <?php if (isset($_POST['nama_toko'])) echo $_POST['nama_toko'] ?> value="<?= $row['id_toko']; ?>"><?= $row['nama_toko']; ?></option>
                                    <?php endwhile; ?>
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
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="ulangi_password">Ulangi Password</label>
                                <input type="password" name="ulangi_password" class="form-control">
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