
<?php
// hapus_wali_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Memeriksa hak akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Hanya admin yang boleh menghapus data.");
}

// Mengambil ID dari URL
$id = (int) ($_GET['id'] ?? 0);

// Memeriksa ID
if ($id <= 0) {
    die("ID wali kelas tidak ditemukan!");
}

// Mengambil data wali kelas sebelum dihapus
$sql = "SELECT
            w.id,
            w.kelas_id,
            k.nama AS nama_kelas
        FROM tbl_wali_kelas w
        LEFT JOIN tbl_kelas k ON w.kelas_id = k.id
        WHERE w.id = ?";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

// Memeriksa apakah data ditemukan
if (!$data) {
    $_SESSION['pesan_error'] = "Data wali kelas tidak ditemukan!";
    header("Location: kelola_wali_kelas.php");
    exit;
}

// Menghapus data wali kelas
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM tbl_wali_kelas WHERE id = ?"
);

if (!$hapus) {
    die("Query hapus gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {

    // Mencatat aktivitas penghapusan
    $id_user = $_SESSION['id_user'] ?? null;
    $nama_kelas = $data['nama_kelas'] ?? 'Tidak diketahui';

    $aktivitas = "Menghapus wali kelas untuk kelas: " . $nama_kelas;
    $waktu = date('Y-m-d H:i:s');

    if ($id_user !== null) {

        $log = mysqli_prepare(
            $koneksi,
            "INSERT INTO tbl_log (id_user, aktivitas, waktu)
             VALUES (?, ?, ?)"
        );

        if ($log) {
            mysqli_stmt_bind_param(
                $log,
                "iss",
                $id_user,
                $aktivitas,
                $waktu
            );

            mysqli_stmt_execute($log);
            mysqli_stmt_close($log);
        }
    }

    $_SESSION['pesan_sukses'] = "Data wali kelas berhasil dihapus!";

} else {

    if (mysqli_errno($koneksi) == 1451) {
        $_SESSION['pesan_error'] =
            "Data wali kelas tidak dapat dihapus karena masih digunakan!";
    } else {
        $_SESSION['pesan_error'] =
            "Gagal menghapus wali kelas: " . mysqli_error($koneksi);
    }
}

mysqli_stmt_close($hapus);

// Kembali ke halaman kelola wali kelas
header("Location: kelola_wali_kelas.php");
exit;

?>
