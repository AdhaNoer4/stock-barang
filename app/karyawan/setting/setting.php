<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
require_once "../../../config.php";
include "../layouts/header.php";
$judul = "Pengaturan Aplikasi";

// Ambil data pengaturan dari database
$query = "SELECT * FROM pengaturan WHERE id = 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $tema = $_POST['tema_bootstrap'];

    // Cek apakah ada upload logo baru
    if ($_FILES['logo']['name'] !== '') {
        $query = "UPDATE pengaturan SET logo = ?, tema_bootstrap = ? WHERE id = 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $fileName, $tema);
    } else {
        $query = "UPDATE pengaturan SET tema_bootstrap = ? WHERE id = 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $tema);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['berhasil'] = "Pengaturan berhasil disimpan!";
        header("Location: setting.php");
    } else {
        $_SESSION['gagal'] = "Pengaturan gagal disimpan!";
        header("Location: setting.php");
    }
}
?>

<!-- Begin Page Content -->
<div class="container-xl">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800"><?= $judul ?></h1>
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">


                    <div class="mb-3">
                        <label for="tema_bootstrap">Tema Warna</label>
                        <select name="tema_bootstrap" class="form-control">
                            <?php
                            $opsi_warna = ['success', 'secondary', 'primary', 'danger', 'warning', 'info', 'dark'];
                            foreach ($opsi_warna as $warna) {
                                $selected = ($row['tema_bootstrap'] === $warna) ? 'selected' : '';
                                echo "<option value=\"$warna\" $selected>$warna</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include "../layouts/footer.php" ?>
