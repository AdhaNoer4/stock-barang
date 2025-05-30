<?php

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stock_barang3");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


