
<?php
// hapus_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Validasi ID kategori
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['pesan_error'] = "ID kategori pelanggaran tidak valid.";
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}

$id = (int) $_GET['id'];

// Periksa apakah data kategori tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = "Data kategori pelanggaran tidak ditemukan.";
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}

// Hapus data kategori
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['pesan_sukses'] =
            "Kategori pelanggaran '" . $data['nama'] . "' berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] =
            "Kategori pelanggaran gagal dihapus.";
    }

} else {
    $_SESSION['pesan_error'] =
        "Gagal menghapus kategori pelanggaran. Data mungkin masih digunakan oleh tabel lain.";
}

mysqli_stmt_close($stmt);

// Kembali ke halaman kelola kategori
header("Location: kelola_pelanggaran_kategori.php");
exit;
?>
