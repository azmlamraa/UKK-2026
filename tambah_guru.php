
<?php
// tambah_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Guru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Tambah Guru</h1>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Tambah Guru
                </div>

                <div class="card-body">

                    <form action="proses_tambah_guru.php"
                          method="POST">

                       <div class="mb-3">
                       <label for="nip" class="form-label">NIP</label>
                       <input type="text"
                              name="nip"
                              id="nip"
                              class="form-control"
                              maxlength="18"
                              oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                              required>
                       </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Guru</label>
                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   maxlength="100"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   maxlength="100"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">
                                Konfirmasi Password
                            </label>
                            <input type="password"
                                   name="konfirmasi_password"
                                   id="konfirmasi_password"
                                   class="form-control"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>
                                <option value="1" selected>Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Guru
                        </button>

                        <a href="kelola_guru.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
