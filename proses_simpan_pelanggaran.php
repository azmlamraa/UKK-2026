
<?php
// proses_simpan_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data dari form
$siswa_id = (int) $_POST['siswa_id'];
$pelanggaran_kategori_id = (int) $_POST['pelanggaran_kategori_id'];
$pelanggaran_id = (int) $_POST['pelanggaran_id'];
$guru_id = (int) $_POST['guru_id'];

$tanggal = $_POST['tanggal'];
$keterangan = trim($_POST['keterangan']);
$tindakan = trim($_POST['tindakan']);
$status = $_POST['status'];

// Validasi status
$status_valid = array(
    'Belum Ditindaklanjuti',
    'Diproses',
    'Selesai'
);

if (!in_array($status, $status_valid, true)) {
    $_SESSION['pesan_error'] = "Status pelanggaran tidak valid.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Validasi data wajib
if (
    $siswa_id <= 0 ||
    $pelanggaran_kategori_id <= 0 ||
    $pelanggaran_id <= 0 ||
    $guru_id <= 0 ||
    empty($tanggal) ||
    empty($keterangan)
) {
    $_SESSION['pesan_error'] = "Semua data wajib harus diisi.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Ambil data siswa
$sql_siswa = "
    SELECT s.id, s.nis, s.nama, s.kelas_id, k.nama AS nama_kelas
    FROM t_siswa s
    LEFT JOIN t_kelas k ON s.kelas_id = k.id
    WHERE s.id = ? AND s.status_aktif = 1
";

$stmt = mysqli_prepare($koneksi, $sql_siswa);
mysqli_stmt_bind_param($stmt, "i", $siswa_id);
mysqli_stmt_execute($stmt);

$hasil_siswa = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($hasil_siswa);
mysqli_stmt_close($stmt);

if (!$siswa) {
    $_SESSION['pesan_error'] = "Data siswa tidak ditemukan atau tidak aktif.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Ambil data pelanggaran dan pastikan kategorinya sesuai
$sql_pelanggaran = "
    SELECT id, nama, poin, pelanggaran_kategori_id
    FROM t_pelanggaran
    WHERE id = ?
      AND pelanggaran_kategori_id = ?
      AND status_aktif = 1
";

$stmt = mysqli_prepare($koneksi, $sql_pelanggaran);
mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $pelanggaran_id,
    $pelanggaran_kategori_id
);
mysqli_stmt_execute($stmt);

$hasil_pelanggaran = mysqli_stmt_get_result($stmt);
$pelanggaran = mysqli_fetch_assoc($hasil_pelanggaran);
mysqli_stmt_close($stmt);

if (!$pelanggaran) {
    $_SESSION['pesan_error'] = "Jenis pelanggaran tidak valid.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Pastikan guru tersedia
$sql_guru = "
    SELECT id, nama
    FROM t_guru
    WHERE id = ? AND status_aktif = 1
";

$stmt = mysqli_prepare($koneksi, $sql_guru);
mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);

$hasil_guru = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($hasil_guru);
mysqli_stmt_close($stmt);

if (!$guru) {
    $_SESSION['pesan_error'] = "Data guru tidak ditemukan atau tidak aktif.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Ambil tahun ajaran aktif
$sql_tahun = "
    SELECT id
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
";

$hasil_tahun = mysqli_query($koneksi, $sql_tahun);
$tahun = mysqli_fetch_assoc($hasil_tahun);

if (!$tahun) {
    $_SESSION['pesan_error'] = "Tahun ajaran aktif belum tersedia.";
    header("Location: catat_pelanggaran.php");
    exit;
}

$tahun_ajaran_id = (int) $tahun['id'];
$kelas_id = (int) $siswa['kelas_id'];

$nama_siswa = $siswa['nama'];
$nama_kelas = $siswa['nama_kelas'] ?? '';
$nama_pelanggaran = $pelanggaran['nama'];
$nama_guru = $guru['nama'];

// Poin diambil dari database, bukan dari input form
$poin = (int) $pelanggaran['poin'];

// Simpan catatan pelanggaran
$sql_simpan = "
    INSERT INTO t_pelanggaran_siswa (
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
        status,
        created_at,
        updated_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        NOW(), NOW()
    )
";

$stmt = mysqli_prepare($koneksi, $sql_simpan);

mysqli_stmt_bind_param(
    $stmt,
    "iisisi siisssiss"
    // Ganti bagian tipe di bawah dengan string yang benar:
);
?>

