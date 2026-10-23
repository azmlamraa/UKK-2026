
<?php
// proses_edit_guru.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Pastikan form dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kelola_guru.php");
    exit;
}

// Ambil dan validasi data
$id = $_POST['id'] ?? '';
$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$status_aktif = $_POST['status_aktif'] ?? '';

// Validasi ID
if (!is_numeric($id) || (int)$id <= 0) {
    $_SESSION['pesan_error'] = "ID guru tidak valid.";
    header("Location: kelola_guru.php");
    exit;
}

$id = (int) $id;

// Validasi input wajib
if (
    $nip === '' ||
    $nama === '' ||
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !in_array($status_aktif, ['0', '1'], true)
) {
    $_SESSION['pesan_error'] = "Data guru tidak valid. Periksa kembali semua isian.";
    header("Location: edit_guru.php?id=" . $id);
    exit;
}

// Validasi panjang NIP sesuai form
if (strlen($nip) > 10) {
    $_SESSION['pesan_error'] = "NIP maksimal 10 karakter.";
    header("Location: edit_guru.php?id=" . $id);
    exit;
}

// Periksa apakah guru tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_guru WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$guru) {
    $_SESSION['pesan_error'] = "Data guru tidak ditemukan.";
    header("Location: kelola_guru.php");
    exit;
}

// Update data guru
$stmt = mysqli_prepare(
    $koneksi,
    "UPDATE t_guru
     SET nip = ?, nama = ?, email = ?, status_aktif = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "sssii",
    $nip,
    $nama,
    $email,
    $status_aktif,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Data guru berhasil diperbarui.";
    mysqli_stmt_close($stmt);

    header("Location: kelola_guru.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Gagal memperbarui data guru.";

    mysqli_stmt_close($stmt);

    header("Location: edit_guru.php?id=" . $id);
    exit;
}
?>
