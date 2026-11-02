
<?php
// proses_tambah_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_guru.php");
    exit;
}

// Ambil data
$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
$status_aktif = $_POST['status_aktif'] ?? '';

// Validasi input
if (
    $nip === '' ||
    $nama === '' ||
    $email === '' ||
    $password === '' ||
    $konfirmasi_password === ''
) {
    $_SESSION['pesan_error'] = "Semua data wajib diisi!";
    header("Location: tambah_guru.php");
    exit;
}

// Validasi panjang NIP
if (strlen($nip) > 10) {
    $_SESSION['pesan_error'] = "NIP maksimal 10 karakter!";
    header("Location: tambah_guru.php");
    exit;
}

// Validasi nama
if (strlen($nama) > 100) {
    $_SESSION['pesan_error'] = "Nama maksimal 100 karakter!";
    header("Location: tambah_guru.php");
    exit;
}

// Validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $_SESSION['pesan_error'] = "Format email tidak valid!";
    header("Location: tambah_guru.php");
    exit;
}

// Validasi password
if ($password !== $konfirmasi_password) {
    $_SESSION['pesan_error'] = "Konfirmasi password tidak cocok!";
    header("Location: tambah_guru.php");
    exit;
}

if (!in_array($status_aktif, ['0', '1'], true)) {
    $_SESSION['pesan_error'] = "Status aktif tidak valid!";
    header("Location: tambah_guru.php");
    exit;
}

// Cek NIP
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_guru WHERE nip = ?"
);
mysqli_stmt_bind_param($stmt, "s", $nip);
mysqli_stmt_execute($stmt);
$cek_nip = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($cek_nip) > 0) {
    $_SESSION['pesan_error'] = "NIP sudah terdaftar!";
    header("Location: tambah_guru.php");
    exit;
}
mysqli_stmt_close($stmt);

// Cek email
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_guru WHERE email = ?"
);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$cek_email = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($cek_email) > 0) {
    $_SESSION['pesan_error'] = "Email sudah terdaftar!";
    header("Location: tambah_guru.php");
    exit;
}
mysqli_stmt_close($stmt);

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Simpan data guru
$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO t_guru
    (nip, nama, email, password, status_aktif, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssssi",
    $nip,
    $nama,
    $email,
    $password_hash,
    $status_aktif
);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['pesan_sukses'] = "Data guru berhasil ditambahkan!";

    header("Location: kelola_guru.php");
    exit;

} else {

    $_SESSION['pesan_error'] = "Gagal menambahkan data guru!";

    header("Location: tambah_guru.php");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
