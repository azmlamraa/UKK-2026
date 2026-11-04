
<?php
// proses_tambah_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$status_aktif = $_POST['status_aktif'];

// Validasi tanggal
if ($tanggal_selesai <= $tanggal_mulai) {
    echo "<script>
        alert('Tanggal selesai harus setelah tanggal mulai!');
        window.location='tambah_tahun_ajaran.php';
    </script>";
    exit;
}

// CEK TAHUN AJARAN AKTIF
if ($status_aktif == '1') {

    $cek = mysqli_query($koneksi, "
        SELECT id
        FROM t_tahun_ajaran
        WHERE status_aktif = 1
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Gagal! Sudah ada tahun ajaran yang aktif. Hanya boleh ada satu tahun ajaran aktif!');
            window.location='tambah_tahun_ajaran.php';
        </script>";
        exit;
    }
}

// INSERT DATA
$sql = "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')";

if (mysqli_query($koneksi, $sql)) {

    echo "<script>
        alert('Tahun ajaran berhasil ditambahkan!');
        window.location='kelola_tahun_ajaran.php';
    </script>";
    exit;

} else {

    echo "Gagal menambahkan data tahun ajaran: "
         . mysqli_error($koneksi);

}
?>
