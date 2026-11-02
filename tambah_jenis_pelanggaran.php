
<?php
// tambah_jenis_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data kategori pelanggaran
$sql_kategori = "SELECT id, nama
                 FROM t_pelanggaran_kategori
                 ORDER BY nama ASC";

$hasil_kategori = mysqli_query($koneksi, $sql_kategori);

if (!$hasil_kategori) {
    die("Gagal mengambil kategori: " .
        htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Jenis Pelanggaran</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Tambah Jenis Pelanggaran</h1>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Tambah Jenis Pelanggaran
                </div>

                <div class="card-body">

                    <form action="proses_tambah_jenis_pelanggaran.php"
                          method="POST"
                          id="formTambahPelanggaran">

                        <!-- KATEGORI -->
                        <div class="mb-3">
                            <label for="pelanggaran_kategori_id"
                                   class="form-label">
                                Kategori Pelanggaran
                            </label>

                            <select name="pelanggaran_kategori_id"
                                    id="pelanggaran_kategori_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Kategori --</option>

                                <?php while ($kategori = mysqli_fetch_assoc($hasil_kategori)) { ?>
                                    <option value="<?= (int) $kategori['id']; ?>">
                                        <?= htmlspecialchars($kategori['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- KODE -->
                        <div class="mb-3">
                            <label for="kode" class="form-label">
                                Kode Pelanggaran
                            </label>

                            <input type="text"
                                   name="kode"
                                   id="kode"
                                   class="form-control"
                                   maxlength="20"
                                   placeholder="Contoh: PLG001"
                                   required>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Pelanggaran
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   maxlength="150"
                                   placeholder="Masukkan nama pelanggaran"
                                   required>
                        </div>

                        <!-- POIN -->
                        <div class="mb-3">
                            <label for="poin" class="form-label">
                                Poin Pelanggaran
                            </label>

                            <input type="number"
                                   name="poin"
                                   id="poin"
                                   class="form-control"
                                   min="0"
                                   placeholder="Masukkan jumlah poin"
                                   required>
                        </div>

                        <!-- DESKRIPSI -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea name="deskripsi"
                                      id="deskripsi"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan deskripsi pelanggaran"></textarea>
                        </div>

                        <!-- STATUS -->
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

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan Pelanggaran
                        </button>

                        <a href="kelola_jenis_pelanggaran.php"
                           class="btn btn-secondary">
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
