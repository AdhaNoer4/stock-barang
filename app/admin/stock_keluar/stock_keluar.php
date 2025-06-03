<?php
require('proses_keluar.php');
require_once('../../../config.php');
$judul = "Stock keluar";
include('../layouts/header.php');

// Ambil daftar toko
$queryToko = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko");

// Simpan toko terpilih ke session
if (isset($_POST['pilih_toko'])) {
    $_SESSION['id_toko'] = $_POST['id_toko'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil toko dari session jika sudah dipilih
$id_toko_terpilih = $_SESSION['id_toko'] ?? '';

$barang = [];
if (!empty($id_toko_terpilih)) {
    $barang = mysqli_query($conn, "SELECT b.nama_barang, s.id_barang 
        FROM stock s 
        JOIN barang b ON s.id_barang = b.id_barang 
        WHERE s.id_toko = '$id_toko_terpilih' 
        ORDER BY b.nama_barang ASC");
}

// Ambil riwayat stok masuk hari ini untuk toko terpilih
$result = [];
if (!empty($id_toko_terpilih)) {
    $result = mysqli_query($conn, "
        SELECT rs.*, u.nama, b.nama_barang 
        FROM riwayat_stok rs
        JOIN barang b ON rs.id_barang = b.id_barang
        JOIN user u ON rs.id_user = u.id_user
        WHERE rs.jenis = 'masuk' 
          AND rs.tanggal = CURDATE()
          AND rs.id_toko = '$id_toko_terpilih'
        ORDER BY rs.tanggal DESC
    ");
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?= $judul; ?></h1>

    <!-- Form Pilih Toko -->
    <form method="POST">
        <div class="mb-3">
            <label for="id_toko">Pilih Toko</label>
            <select name="id_toko" class="form-control" required onchange="this.form.submit()">
                <option value="">-- Pilih Toko --</option>
                <?php while ($toko = mysqli_fetch_assoc($queryToko)) : ?>
                    <option value="<?= $toko['id_toko']; ?>" <?= ($id_toko_terpilih == $toko['id_toko']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($toko['nama_toko']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="hidden" name="pilih_toko" value="1">
        </div>
    </form>

    <!-- Form Stock Keluar -->
    <?php if ($id_toko_terpilih): ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_toko" value="<?= $id_toko_terpilih ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                        <label for="id_barang">Nama Barang</label>
                                        <select name="id_barang" class="form-control select-barang" required>
                                            <option value="">--Cari Barang--</option>
                                            <?php while ($row = mysqli_fetch_assoc($barang)): ?>
                                                <option value="<?= $row['id_barang'] ?>"><?= $row['nama_barang'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                            <div class="mb-3">
                                <label for="jumlah">Jumlah Keluar</label>
                                <input type="number" name="jumlah" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="penerima">Penerima</label>
                                <input type="text" name="penerima" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary" name="submit">Keluarkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <!-- Riwayat Hari Ini -->
    <div class="card shadow mb-4 mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Keluar Hari Ini</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
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
                        <?php if (mysqli_num_rows($result) === 0): ?>
                            <tr><td colspan="6">Data kosong, silakan tambah data baru.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; while ($riwayat = mysqli_fetch_array($result)) : ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= $riwayat['tanggal']; ?></td>
                                    <td><?= $riwayat['nama_barang']; ?></td>
                                    <td><?= $riwayat['jumlah'] ?></td>
                                    <td><?= $riwayat['nama'] ?></td>
                                    <td><?= $riwayat['jenis'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>
