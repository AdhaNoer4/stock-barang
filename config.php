<?php

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stock_barang");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


