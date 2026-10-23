<?php
// dashboard.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));
$namaRole = ucfirst($role);

// Fungsi menghitung jumlah data
function jumlahData($koneksi, $sql)
{
    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {
        die("Gagal mengambil data: " . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_assoc($hasil);
    return (int) $data['total'];
}

// Data untuk dashboard admin
$total_siswa = 0;
$total_guru = 0;
$total_kelas = 0;
$total_tahun = 0;

// Data untuk dashboard guru
$total_pelanggaran = 0;
$total_poin = 0;

if ($role === 'admin') {
    $total_siswa = jumlahData(
        $koneksi,
        "SELECT COUNT(*) AS total FROM t_siswa"
    );

    $total_guru = jumlahData(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM t_guru g
         INNER JOIN t_users u ON g.user_id = u.id
         WHERE u.role = 'guru'"
    );

    $total_kelas = jumlahData(
        $koneksi,
        "SELECT COUNT(*) AS total FROM t_kelas"
    );

    $total_tahun = jumlahData(
        $koneksi,
        "SELECT COUNT(*) AS total FROM t_tahun_ajaran"
    );
}

if ($role === 'guru') {
    $total_tahun = jumlahData(
        $koneksi,
        "SELECT COUNT(*) AS total FROM t_tahun_ajaran"
    );

    $total_pelanggaran = jumlahData(
        $koneksi,
        "SELECT COUNT(*) AS total FROM t_pelanggaran_siswa"
    );

    // Total poin dijumlahkan dari jenis pelanggaran
    $total_poin = jumlahData(
        $koneksi,
        "SELECT COALESCE(SUM(p.poin), 0) AS total
         FROM t_pelanggaran_siswa ps
         INNER JOIN t_pelanggaran p
             ON ps.pelanggaran_id = p.id"
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR TETAP ADA -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN DASHBOARD -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2>Dashboard</h2>

            <p>
                Selamat datang,
                <strong>
                    <?= htmlspecialchars($namaRole, ENT_QUOTES, 'UTF-8'); ?>
                </strong>
                di halaman dashboard.
            </p>

            <div class="row">

                <?php if ($role === 'admin') : ?>

                    <!-- DATA SISWA KHUSUS ADMIN -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Data Siswa</h5>
                                <h2 class="text-primary">
                                    <?= number_format($total_siswa); ?>
                                </h2>
                                <a href="kelola_siswa.php"
                                   class="btn btn-primary btn-sm">
                                    Lihat Data Siswa
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- DATA GURU KHUSUS ADMIN -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Data Guru</h5>
                                <h2 class="text-success">
                                    <?= number_format($total_guru); ?>
                                </h2>
                                <a href="kelola_guru.php"
                                   class="btn btn-success btn-sm">
                                    Lihat Data Guru
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- DATA KELAS KHUSUS ADMIN -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title">Kelas</h5>
                                <h2 class="text-warning">
                                    <?= number_format($total_kelas); ?>
                                </h2>
                                <a href="kelola_kelas.php"
                                   class="btn btn-warning btn-sm">
                                    Lihat Data Kelas
                                </a>
                            </div>
                        </div>
                    </div>
                     <!-- DATA KELAS KHUSUS ADMIN -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title">Wali Kelas</h5>
                                <h2 class="text-warning">
                                    <?= number_format($total_kelas); ?>
                                </h2>
                                <a href="kelola_kelas.php"
                                   class="btn btn-warning btn-sm">
                                    Lihat Data Wali Kelas
                                </a>
                            </div>
                        </div>
                    </div>


                <?php endif; ?>

                <!-- TAHUN AJARAN UNTUK ADMIN DAN GURU -->
                <?php if ($role === 'admin' || $role === 'guru') : ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-info h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tahun Ajaran</h5>
                                <h2 class="text-info">
                                    <?= number_format($total_tahun); ?>
                                </h2>

                                <?php if ($role === 'admin') : ?>
                                    <a href="kelola_tahun_ajaran.php"
                                       class="btn btn-info btn-sm">
                                        Lihat Tahun Ajaran
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted">
                                        Total tahun ajaran
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($role === 'guru') : ?>

                    <!-- CATATAN PELANGGARAN KHUSUS GURU -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-danger h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Catatan Pelanggaran
                                </h5>
                                <h2 class="text-danger">
                                    <?= number_format($total_pelanggaran); ?>
                                </h2>
                                <a href="tindakan.php"
                                   class="btn btn-danger btn-sm">
                                    Lihat Pelanggaran
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL POIN KHUSUS GURU -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-secondary h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Total Poin Pelanggaran
                                </h5>
                                <h2 class="text-secondary">
                                    <?= number_format($total_poin); ?>
                                </h2>
                                <a href="rekap_poin.php"
                                   class="btn btn-secondary btn-sm">
                                    Lihat Rekap Poin
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if ($role !== 'admin' && $role !== 'guru') : ?>
                    <div class="col-12">
                        <div class="alert alert-danger">
                            Role akun tidak dikenali. Silakan hubungi admin.
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>