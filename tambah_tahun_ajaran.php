
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data dari form
$nama = trim($_POST['nama']);
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$status_aktif = $_POST['status_aktif'];

// Validasi input
if (
    empty($nama) ||
    empty($tanggal_mulai) ||
    empty($tanggal_selesai) ||
    !in_array($status_aktif, ['0', '1'], true)
) {
    echo "<script>
        alert('Semua data wajib diisi!');
        window.location='tambah_tahun_ajaran.php';
    </script>";
    exit;
}

// Validasi tanggal
if ($tanggal_selesai <= $tanggal_mulai) {
    echo "<script>
        alert('Tanggal selesai harus setelah tanggal mulai!');
        window.location='tambah_tahun_ajaran.php';
    </script>";
    exit;
}

// CEK APAKAH SUDAH ADA TAHUN AJARAN AKTIF
if ($status_aktif == '1') {

    $cek = mysqli_query(
        $koneksi,
        "SELECT id FROM t_tahun_ajaran
         WHERE status_aktif = 1
         LIMIT 1"
    );

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('GAGAL! Sudah ada tahun ajaran yang berstatus AKTIF. Hanya boleh ada satu tahun ajaran aktif!');
            window.location='tambah_tahun_ajaran.php';
        </script>";
        exit;
    }
}

// Simpan data tahun ajaran
$query = "INSERT INTO t_tahun_ajaran
          (nama, tanggal_mulai, tanggal_selesai, status_aktif)
          VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $query);

mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $nama,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif
);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
        alert('Tahun ajaran berhasil ditambahkan!');
        window.location='kelola_tahun_ajaran.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menambahkan tahun ajaran!');
        window.location='tambah_tahun_ajaran.php';
    </script>";
}

mysqli_stmt_close($stmt);
?>
