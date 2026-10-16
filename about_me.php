<?php
session_start();
include "config/koneksi.php";


$nama = "Azmil Amira";
$kelas = "XII RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Menyanyi dan Menari";
$cita_cita = "Multitalenta";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
    <div class="row">
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

    <h2>Tentang Saya</h2>

    <p>Nama: <?php echo $nama; ?></p>
    <p>Kelas: <?php echo $kelas; ?></p>
    <p>Sekolah: <?php echo $sekolah; ?></p>
    <p>Jurusan: <?php echo $jurusan; ?></p>
    <p>Hobi: <?php echo $hobi; ?></p>
    <p>Cita-cita: <?php echo $cita_cita; ?></p>

    <h3>Deskripsi Diri</h3>
    <p>
        Saya adalah seorang pelajar yang sedang belajar
        Rekayasa Perangkat Lunak. Saya senang mempelajari
        teknologi dan membuat website. Saya ingin terus
        belajar agar bisa menjadi Web Developer.
    </p>

    <a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>