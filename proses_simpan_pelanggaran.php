<?php
// proses_simpan_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Pastikan form menggunakan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catat_pelanggaran.php');
    exit;
}

// ====================================
// AMBIL DATA DARI FORM
// ====================================

$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id = (int) ($_POST['kelas_id'] ?? 0);
$pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
$pelanggaran_kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
$poin = (int) ($_POST['poin'] ?? 0);

$tanggal = trim($_POST['tanggal'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');

// Tindakan dikosongkan terlebih dahulu
$tindakan = '';
$status = 'belum';


// ====================================
// AMBIL IDENTITAS GURU YANG LOGIN
// ====================================

// ID user dari session login
$id_user = (int) ($_SESSION['id_user'] ?? $_SESSION['id'] ?? 0);

if ($id_user <= 0) {
    die("Session user tidak ditemukan. Silakan login kembali.");
}

// Cari data guru berdasarkan user_id
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE user_id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data_guru = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Pastikan user yang login terdaftar sebagai guru
if (!$data_guru) {
    die("Data guru untuk akun yang sedang login tidak ditemukan. Periksa hubungan user_id pada tabel t_guru.");
}

$guru_id = (int) $data_guru['id'];
$nama_guru = $data_guru['nama'];


// ====================================
// VALIDASI DATA WAJIB
// ====================================

if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    $pelanggaran_id <= 0 ||
    $pelanggaran_kategori_id <= 0 ||
    $poin < 0 ||
    $tanggal === ''
) {
    echo "<script>
        alert('Data pelanggaran belum lengkap!');
        window.location='catat_pelanggaran.php';
    </script>";
    exit;
}


// ====================================
// VALIDASI FORMAT TANGGAL
// ====================================

$format_tanggal = DateTime::createFromFormat('Y-m-d', $tanggal);

if (!$format_tanggal || $format_tanggal->format('Y-m-d') !== $tanggal) {
    echo "<script>
        alert('Format tanggal tidak valid!');
        window.location='catat_pelanggaran.php';
    </script>";
    exit;
}


// ====================================
// AMBIL DATA SISWA
// ====================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $siswa_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data_siswa = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data_siswa) {
    die("Data siswa tidak ditemukan.");
}

$nama_siswa = $data_siswa['nama'];


// ====================================
// AMBIL DATA KELAS
// ====================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $kelas_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data_kelas = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data_kelas) {
    die("Data kelas tidak ditemukan.");
}

$nama_kelas = $data_kelas['nama'];


// ====================================
// PASTIKAN TAHUN AJARAN TERSEDIA
// ====================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data_tahun = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data_tahun) {
    die("Data tahun ajaran tidak ditemukan.");
}


// ====================================
// AMBIL DATA PELANGGARAN
// ====================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data_pelanggaran = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data_pelanggaran) {
    die("Data pelanggaran tidak ditemukan.");
}

$nama_pelanggaran = $data_pelanggaran['nama'];
$poin = (int) $data_pelanggaran['poin'];
$pelanggaran_kategori_id = (int) $data_pelanggaran['pelanggaran_kategori_id'];


// ====================================
// SIMPAN DATA PELANGGARAN
// ====================================

$query = "INSERT INTO t_pelanggaran_siswa (
    tahun_ajaran_id,
    siswa_id,
    nama_siswa,
    kelas_id,
    nama_kelas,
    pelanggaran_id,
    nama_pelanggaran,
    pelanggaran_kategori_id,
    guru_id,
    nama_guru,
    tanggal,
    keterangan,
    poin,
    tindakan,
    status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Gagal menyiapkan query: " . mysqli_error($koneksi));
}

// Bind parameter
mysqli_stmt_bind_param(
    $stmt,
    "iisisisiisssiss",
    $tahun_ajaran_id,
    $siswa_id,
    $nama_siswa,
    $kelas_id,
    $nama_kelas,
    $pelanggaran_id,
    $nama_pelanggaran,
    $pelanggaran_kategori_id,
    $guru_id,
    $nama_guru,
    $tanggal,
    $keterangan,
    $poin,
    $tindakan,
    $status
);


// ====================================
// JALANKAN QUERY
// ====================================

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    echo "<script>
        alert('Data pelanggaran berhasil disimpan!');
        window.location='tindakan.php';
    </script>";

    exit;

} else {

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    die("Gagal menyimpan data pelanggaran: " . $error);

}
?>