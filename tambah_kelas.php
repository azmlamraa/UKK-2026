
<?php
// tambah_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $status_aktif = (int) ($_POST['status_aktif'] ?? 1);

    // Validasi input
    if ($nama == '' || $tingkat == '' || $jurusan == '') {
        $pesan_error = "Semua data wajib diisi!";
    } else {

        // Cek apakah nama kelas sudah ada
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_kelas WHERE nama = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $nama);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {

            $pesan_error = "Nama kelas sudah terdaftar!";

        } else {

            // Simpan data kelas
            $sql = "INSERT INTO t_kelas
                    (nama, tingkat, jurusan, status_aktif)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($koneksi, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sssi",
                $nama,
                $tingkat,
                $jurusan,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {

                $_SESSION['pesan_sukses'] =
                    "Data kelas berhasil ditambahkan!";

                header("Location: kelola_kelas.php");
                exit;

            } else {

                $pesan_error = "Gagal menambahkan data kelas!";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Tambah Kelas</h2>

            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Form Tambah Kelas</h5>
                </div>

                <div class="card-body">

                    <?php if ($pesan_error != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan_error); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   placeholder="Contoh: XII RPL 1"
                                   value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tingkat</label>

                            <select name="tingkat" class="form-select" required>
                                <option value="">-- Pilih Tingkat --</option>

                                <?php foreach (['X', 'XI', 'XII'] as $t) { ?>
                                    <option value="<?= $t; ?>"
                                        <?= (($_POST['tingkat'] ?? '') == $t) ? 'selected' : ''; ?>>
                                        <?= $t; ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>

                            <select name="jurusan" class="form-select" required>
                                <option value="">-- Pilih Jurusan --</option>

                                <?php
                                $daftar_jurusan = ['RPL', 'TKJ', 'TKR', 'TSM', 'BD'];

                                foreach ($daftar_jurusan as $j) {
                                ?>
                                    <option value="<?= $j; ?>"
                                        <?= (($_POST['jurusan'] ?? '') == $j) ? 'selected' : ''; ?>>
                                        <?= $j; ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>

                            <select name="status_aktif" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>

                            <a href="kelola_kelas.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
