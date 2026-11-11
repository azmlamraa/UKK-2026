<?php
$current_page = basename($_SERVER['PHP_SELF']);

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id_user'] ?? $_SESSION['id'] ?? 0);

$is_wali_kelas = false;


// CEK APAKAH GURU TERDAFTAR SEBAGAI WALI KELAS
if (
    $role === "guru" &&
    $id_user > 0 &&
    isset($koneksi)
) {
    $query_wali = mysqli_prepare(
        $koneksi,
        "SELECT w.id
         FROM t_wali_kelas w
         INNER JOIN t_guru g ON w.guru_id = g.id
         WHERE g.user_id = ?
         AND w.status_aktif = 1
         LIMIT 1"
    );

    if ($query_wali) {
        mysqli_stmt_bind_param($query_wali, "i", $id_user);
        mysqli_stmt_execute($query_wali);

        mysqli_stmt_bind_result($query_wali, $id_wali);

        if (mysqli_stmt_fetch($query_wali)) {
            $is_wali_kelas = true;
        }

        mysqli_stmt_close($query_wali);
    }
}


// SIDEBAR ADMIN
if ($role === "admin") {
?>

    <!-- SIDEBAR ADMIN -->
    <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
        <h4 class="text-white mb-4">
            Pelanggaran Siswa
        </h4>

        <ul class="nav nav-pills flex-column">

            <li class="nav-item mb-2">
                <a href="dashboard.php"
                   class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
                    Dashboard
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_siswa.php"
                   class="nav-link <?= ($current_page == 'kelola_siswa.php') ? 'active' : 'text-white' ?>">
                    Data Siswa
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_guru.php"
                   class="nav-link <?= ($current_page == 'kelola_guru.php') ? 'active' : 'text-white' ?>">
                    Data Guru
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_kelas.php"
                   class="nav-link <?= ($current_page == 'kelola_kelas.php') ? 'active' : 'text-white' ?>">
                    Data Kelas
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_tahun_ajaran.php"
                   class="nav-link <?= ($current_page == 'kelola_tahun_ajaran.php') ? 'active' : 'text-white' ?>">
                    Data Tahun Ajaran
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_wali_kelas.php"
                   class="nav-link <?= ($current_page == 'kelola_wali_kelas.php') ? 'active' : 'text-white' ?>">
                    Data Wali Kelas
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="penempatan_siswa.php"
                   class="nav-link <?= ($current_page == 'penempatan_siswa.php') ? 'active' : 'text-white' ?>">
                    Penempatan Siswa
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_pelanggaran_kategori.php"
                   class="nav-link <?= ($current_page == 'kelola_pelanggaran_kategori.php') ? 'active' : 'text-white' ?>">
                    Kategori Pelanggaran
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="kelola_jenis_pelanggaran.php"
                   class="nav-link <?= ($current_page == 'kelola_jenis_pelanggaran.php') ? 'active' : 'text-white' ?>">
                    Jenis Pelanggaran
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="laporan.php"
                   class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : 'text-white' ?>">
                    Laporan
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="about_me.php"
                   class="nav-link <?= ($current_page == 'about_me.php') ? 'active' : 'text-white' ?>">
                    About Me
                </a>
            </li>

            <hr class="text-secondary">

            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger">
                    Logout
                </a>
            </li>

        </ul>
    </div>


<?php
}


// SIDEBAR WALI KELAS
elseif (
    $is_wali_kelas ||
    $role === "wali_kelas" ||
    $role === "wali kelas" ||
    $role === "walikelas"
) {
?>

    <!-- SIDEBAR WALI KELAS -->
    <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
        <h4 class="text-white mb-4">
            Pelanggaran Siswa
        </h4>

        <ul class="nav nav-pills flex-column">

            <li class="nav-item mb-2">
                <a href="dashboard.php"
                   class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
                    Dashboard
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="catat_pelanggaran.php"
                   class="nav-link <?= ($current_page == 'catat_pelanggaran.php') ? 'active' : 'text-white' ?>">
                    Catat Pelanggaran
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="tindakan.php"
                   class="nav-link <?= ($current_page == 'tindakan.php') ? 'active' : 'text-white' ?>">
                    Tindakan
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="riwayat.php"
                   class="nav-link <?= ($current_page == 'riwayat.php') ? 'active' : 'text-white' ?>">
                    Riwayat
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="rekap_poin.php"
                   class="nav-link <?= ($current_page == 'rekap_poin.php') ? 'active' : 'text-white' ?>">
                    Rekap Poin
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="laporan.php"
                   class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : 'text-white' ?>">
                    Laporan
                </a>
            </li>
           
            <hr class="text-secondary">

            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger">
                    Logout
                </a>
            </li>

        </ul>
    </div>


<?php
}


// SIDEBAR GURU BIASA
elseif ($role === "guru") {
?>

    <!-- SIDEBAR GURU -->
    <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
        <h4 class="text-white mb-4">
            Pelanggaran Siswa
        </h4>

        <ul class="nav nav-pills flex-column">

            <li class="nav-item mb-2">
                <a href="dashboard.php"
                   class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
                    Dashboard
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="catat_pelanggaran.php"
                   class="nav-link <?= ($current_page == 'catat_pelanggaran.php') ? 'active' : 'text-white' ?>">
                    Catat Pelanggaran
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="tindakan.php"
                   class="nav-link <?= ($current_page == 'tindakan.php') ? 'active' : 'text-white' ?>">
                    Tindakan
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="riwayat.php"
                   class="nav-link <?= ($current_page == 'riwayat.php') ? 'active' : 'text-white' ?>">
                    Riwayat
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="rekap_poin.php"
                   class="nav-link <?= ($current_page == 'rekap_poin.php') ? 'active' : 'text-white' ?>">
                    Rekap Poin
                </a>
            </li>

            <hr class="text-secondary">

            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger">
                    Logout
                </a>
            </li>

        </ul>
    </div>

<?php
}
?>