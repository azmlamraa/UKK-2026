<?php
include 'includes/cek_session.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Dashbord - Sistem Pelanggaran Siswa</title>
    </head>
    <body>
        <h1>Selamat datang, <?php echo $_SESSION['name']; ?></h1>
        <p>Anda login sebagai: <?php echo $_SESSION['role']; ?></p>

        <ul>
            <?php if ($_SESSION['role'] == 'admin') { ?>
            <li><a href="kelola_guru.php">kelola guru</a></li>
            <li><a href="kelola_siswa.php">kelola siswa</a></li>
            <li><a href="kelola_kelas.php">kelola kelas</a></li>
            <li><a href="kelola_tahun_ajaran.php">kelola tahun ajaran</a></li>
             <li><a href="penempatan_siswa.php">penempatan siswa</a></li>
            <li><a href="kelola_wali_kelas.php">kelola wali kelas</a></li>
            <li><a href="kelola_pelanggaran_kategori.php">kelola pelanggaran kategori</a></li>
        <?php } ?>

        <?php if($_SESSION['role'] == 'guru') { ?>
        <li><a href="kelola_catat_pelanggaran.php">catat pelanggaran</a></li>
        <li><a href="kelola_tindakan.php">tindakan</a></li>
        <?php } ?>
        </ul>
        
        <a href="logout.php">Logout</a>
    </body>
</html>