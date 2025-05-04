<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
require_once('../../../config.php');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_riwayat_stok.xls");

if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    $jenis = $_GET['jenis'];

    $query = "SELECT rs.*, s.namabarang FROM riwayat_stok rs 
              JOIN stock s ON rs.idbarang = s.idbarang 
              WHERE rs.tanggal BETWEEN '$dari' AND '$sampai'";

    if (!empty($jenis)) {
        $query .= " AND rs.jenis = '$jenis'";
    }

    $query .= " ORDER BY rs.tanggal DESC";
    $result = mysqli_query($conn, $query);
?>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($laporan = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $laporan['namabarang']; ?></td>
                    <td><?= $laporan['jenis']; ?></td>
                    <td><?= $laporan['jumlah'] ?></td>
                    <td><?= $laporan['tanggal'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>