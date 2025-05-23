<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Toko";
include('../layouts/header.php');
require_once('../../../config.php');

if (isset($_POST['submit'])) {
    $ambil_id = mysqli_query($conn, "SELECT id_toko FROM toko ORDER BY id_toko DESC LIMIT 1");

    if (mysqli_num_rows($ambil_id) > 0) {
        $row = mysqli_fetch_assoc($ambil_id);
        $id_toko = $row["id_toko"];
    }


    $nama_toko = htmlspecialchars($_POST['nama_toko']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);


    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            mysqli_begin_transaction($conn);

            try {
                // Insert ke tabel user
                $stmtUser = mysqli_prepare($conn, "INSERT INTO toko (nama_toko, alamat, telepon) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmtUser, "ssi", $nama_toko, $alamat, $telepon);
                mysqli_stmt_execute($stmtUser);

                // Commit transaksi jika semua berhasil
                mysqli_commit($conn);

                $_SESSION['berhasil'] = "Data berhasil ditambahkan!";
                header('Location: toko.php');
                exit;
            } catch (Exception $e) {
                // Rollback jika terjadi kesalahan
                mysqli_rollback($conn);
                $_SESSION['gagal'] = "Data gagal ditambahkan! Terjadi kesalahan:" . $e->getMessage();
                header('Location: toko.php');
                exit;
            }
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <h1 class="h3 mb-2 text-gray-800"><?= $judul ?></h1>
        <form action="tambah.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-5">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama_toko">Nama Toko</label>
                                <input type="text" name="nama_toko" class="form-control" value="<?php if (isset($_POST['nama_toko'])) echo $_POST['nama_toko'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="alamat">Alamat</label>
                                <textarea type="textarea" name="alamat" class="form-control" value="<?php if (isset($_POST['alamat'])) echo $_POST['alamat'] ?>"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="telepon">Telepon</label>
                                <input type="number" name="telepon" class="form-control" value="<?php if (isset($_POST['telepon'])) echo $_POST['telepon'] ?>">
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