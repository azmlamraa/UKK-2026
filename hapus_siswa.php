<?php
// hapus_siswa.php
session_start();

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_GET['id'];

// Ambil nama siswa sebelum dihapus
$cek = mysqli_query(
    $koneksi,
    "SELECT nama FROM t_siswa WHERE id = '$id'"
);

$data = mysqli_fetch_assoc($cek);

// Hapus data siswa
$sql = "DELETE FROM t_siswa WHERE id = '$id'";

header('Location: kelola_siswa.php');
exit;
?>