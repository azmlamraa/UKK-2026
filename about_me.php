
<?php
// about_me.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nama = "Azmil Amira";
$kelas = "XI RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Menyanyi, Menari, Baca Novel";
$cita_cita = "Multitalenta";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .foto-profil {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }

        .profil-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .info-card {
            transition: transform 0.2s;
        }

        .info-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="d-flex flex-wrap justify-content-between
                        align-items-center mb-4">

                <div>
                    <h1 class="fw-bold mb-1">About Me</h1>
                    <p class="text-muted mb-0">
                        Kenali saya lebih dekat!
                    </p>
                </div>

                <a href="dashboard.php" class="btn btn-secondary mt-2">
                    &larr; Kembali ke Dashboard
                </a>

            </div>

            <!-- PROFIL -->
            <div class="card shadow-sm profil-card mb-4">

                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="fw-bold mb-1">Profil Saya</h3>
                    <p class="mb-0">Informasi pribadi dan cita-cita saya</p>
                </div>

                <div class="card-body p-4 p-md-5">

                    <!-- FOTO PROFIL -->
                    <div class="text-center mb-4">

                        <img src="img/haechan.jpg"
                             alt="Foto profil Azmil Amira"
                             class="rounded-circle foto-profil mb-3">

                        <h3 class="text-primary fw-bold">
                            <?= htmlspecialchars($nama); ?>
                        </h3>

                        <p class="text-muted mb-2">
                            <?= htmlspecialchars($kelas); ?> |
                            <?= htmlspecialchars($jurusan); ?>
                        </p>

                        <span class="badge bg-primary p-2">
                            Pelajar Rekayasa Perangkat Lunak
                        </span>

                    </div>

                    <hr class="my-4">

                    <!-- INFORMASI DIRI -->
                    <h5 class="text-primary fw-bold mb-3">
                        Informasi Diri
                    </h5>

                    <div class="row">

                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card info-card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted">Nama Lengkap</h6>
                                    <p class="fw-bold mb-0">
                                        <?= htmlspecialchars($nama); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card info-card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted">Kelas</h6>
                                    <p class="fw-bold mb-0">
                                        <?= htmlspecialchars($kelas); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card info-card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted">Sekolah</h6>
                                    <p class="fw-bold mb-0">
                                        <?= htmlspecialchars($sekolah); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card info-card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted">Jurusan</h6>
                                    <p class="fw-bold mb-0">
                                        <?= htmlspecialchars($jurusan); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card info-card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted">Hobi</h6>
                                    <p class="fw-bold mb-0">
                                        <?= htmlspecialchars($hobi); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card info-card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted">Cita-cita</h6>
                                    <p class="fw-bold mb-0">
                                        <?= htmlspecialchars($cita_cita); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- DESKRIPSI -->
                    <h5 class="text-primary fw-bold mt-4 mb-3">
                        Deskripsi Saya
                    </h5>

                    <div class="alert alert-primary mb-0">
                        Saya adalah seorang pelajar yang sedang belajar
                        Rekayasa Perangkat Lunak. Saya senang mempelajari
                        teknologi dan membuat website. Saya ingin terus belajar
                        agar dapat mengembangkan kemampuan dan meraih cita-cita.
                    </div>

                </div>

                <div class="card-footer text-center text-muted py-3">
                    &copy; <?= date('Y'); ?>
                    <?= htmlspecialchars($nama); ?>
                </div>

            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
