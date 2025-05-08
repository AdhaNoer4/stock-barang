<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "karyawan") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Dashboard";
include('../layouts/header.php');
require_once('../../../config.php');

// query untuk menghitung total barang
$total_barang_query = mysqli_query($conn, "SELECT COUNT(*) AS total_barang FROM stock");
$total_barang_data = mysqli_fetch_assoc($total_barang_query);
$total_barang = $total_barang_data['total_barang'];

// query untuk menghitung total stock menipis
$total_stock_menipis_query = mysqli_query($conn, "SELECT COUNT(*) AS total_stock_menipis FROM stock WHERE stock <= 5");
$total_stock_menipis_data = mysqli_fetch_assoc($total_stock_menipis_query);
$total_stock_menipis = $total_stock_menipis_data['total_stock_menipis'];

// query untuk menghitung total re-stock
$total_restock_query = mysqli_query($conn, "SELECT COUNT(*) AS total_restock FROM masuk");
$total_restock_data = mysqli_fetch_assoc($total_restock_query);
$total_restock = $total_restock_data['total_restock'];

// query untuk menghitung total Aktivitas
$total_aktivitas_query = mysqli_query($conn, "SELECT COUNT(*) AS total_aktivitas FROM riwayat_stok WHERE jenis = 'masuk' OR jenis = 'keluar'");
$total_aktivitas_data = mysqli_fetch_assoc($total_aktivitas_query);
$total_aktivitas = $total_aktivitas_data['total_aktivitas'];

?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total barang -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Barang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_barang ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock menipis -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Stock Menipis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_stock_menipis ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-square fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Re-Stock-->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Re-Stock
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= $total_restock ?></div>
                                </div>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Aktivitas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_aktivitas ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php
include "../layouts/footer.php"
?>