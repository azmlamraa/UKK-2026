<?php
// hapus_tahun_ajaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// 1. Pastikan koneksi database tersedia
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    die("Koneksi database tidak tersedia. Periksa config/koneksi.php");
}

// 2. Ambil dan validasi ID tahun ajaran
$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit((string) $id) || (int) $id < 1) {
    header('Location: kelola_tahun_ajaran.php');
    exit;
}

$id = (int) $id;

// 3. Periksa apakah tahun ajaran tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$tahunAjaran = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$tahunAjaran) {
    echo "<script>
        alert('Data tahun ajaran tidak ditemukan!');
        window.location='kelola_tahun_ajaran.php';
    </script>";
    exit;
}

// 4. Periksa apakah kolom tahun_ajaran_id boleh NULL
$hasilKolom = mysqli_query(
    $koneksi,
    "SHOW COLUMNS FROM t_pelanggaran_siswa LIKE 'tahun_ajaran_id'"
);

$kolom = mysqli_fetch_assoc($hasilKolom);

if (!$kolom) {
    die("Kolom tahun_ajaran_id tidak ditemukan pada t_pelanggaran_siswa.");
}

if ($kolom['Null'] !== 'YES') {
    echo "<script>
        alert('Kolom tahun_ajaran_id belum mengizinkan NULL. Struktur database perlu disesuaikan terlebih dahulu agar data pelanggaran tetap tersimpan.');
        window.location='kelola_tahun_ajaran.php';
    </script>";
    exit;
}

// 5. Mulai transaksi setelah koneksi dan validasi selesai
mysqli_begin_transaction($koneksi);

try {
    // Lepaskan hubungan tahun ajaran tanpa menghapus catatan pelanggaran
    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE t_pelanggaran_siswa
         SET tahun_ajaran_id = NULL
         WHERE tahun_ajaran_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Hapus tahun ajaran
    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_tahun_ajaran WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Simpan perubahan jika semuanya berhasil
    mysqli_commit($koneksi);

    echo "<script>
        alert('Tahun ajaran berhasil dihapus. Data pelanggaran siswa tetap tersimpan.');
        window.location='kelola_tahun_ajaran.php';
    </script>";
    exit;

} catch (Throwable $e) {
    mysqli_rollback($koneksi);

    error_log("Gagal menghapus tahun ajaran: " . $e->getMessage());

    echo "<script>
        alert('Penghapusan gagal. Periksa relasi database dan log error PHP.');
        window.location='kelola_tahun_ajaran.php';
    </script>";
    exit;
}
?>