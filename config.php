<?php

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stokbarang");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


