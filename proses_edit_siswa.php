<?php
// proses_edit_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nis = $_POST['nis'];
$nisn = $_POST['nisn'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat'];
$nama = $_POST['nama'];
$status_aktif = $_POST['status_aktif'];

$sql = "UPDATE tbl_siswa SET
        nip = '$nis',
        nama = '$nama',
        jenis_kelamin = '$jenis_kelamin',
        tanggal_lahir = '$tanggal_lahir',
        alamat = '$alamat',
        status_aktif = '$status_aktif',
        WHERE id = '$id'";

$hasil = mysqli_query($koneksi, $sql);

if ($hasil) {
    header("Location: kelola_siswa.php");
    exit;
} else {
    echo "Gagal mengubah data siswa: " . mysqli_error($koneksi);
}
?>