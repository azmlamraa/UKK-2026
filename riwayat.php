
<?php
// riwayat.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// PENCARIAN
// ========================================

$cari = trim($_GET['cari'] ?? '');

$cari_sql = mysqli_real_escape_string(
    $koneksi,
    $cari
);

// ========================================
// FILTER TINDAKAN
// ========================================

$filter_tindakan = $_GET['tindakan'] ?? '';

$filter_tindakan = in_array(
    $filter_tindakan,
    ['belum', 'sudah'],
    true
) ? $filter_tindakan : '';


// ========================================
// QUERY DASAR
// ========================================

$sql = "SELECT *
        FROM t_pelanggaran_siswa
        WHERE 1=1";


// ========================================
// PENCARIAN DATA
// ========================================

if ($cari !== '') {

    $sql .= " AND (
        nama_siswa LIKE '%$cari_sql%'
        OR nama_pelanggaran LIKE '%$cari_sql%'
        OR nama_kelas LIKE '%$cari_sql%'
        OR nama_guru LIKE '%$cari_sql%'
    )";

}


// ========================================
// FILTER BERDASARKAN TINDAKAN
// ========================================

if ($filter_tindakan === 'belum') {

    // Belum memiliki tindakan

    $sql .= " AND (
        tindakan IS NULL
        OR TRIM(tindakan) = ''
    )";

} elseif ($filter_tindakan === 'sudah') {

    // Sudah memiliki tindakan

    $sql .= " AND (
        tindakan IS NOT NULL
        AND TRIM(tindakan) != ''
    )";

}


// ========================================
// URUTKAN DATA TERBARU
// ========================================

$sql .= " ORDER BY tanggal DESC, id DESC";


// ========================================
// JALANKAN QUERY
// ========================================

$query = mysqli_query($koneksi, $sql);

if (!$query) {

    die('Gagal mengambil data: ' . mysqli_error($koneksi));

}

$jumlah = mysqli_num_rows($query);


// ========================================
// HITUNG JUMLAH TINDAKAN
// ========================================

$jumlah_belum = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran_siswa
     WHERE tindakan IS NULL
        OR TRIM(tindakan) = ''"
);

$data_belum = mysqli_fetch_assoc($jumlah_belum);

$jumlah_sudah = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran_siswa
     WHERE tindakan IS NOT NULL
       AND TRIM(tindakan) != ''"
);

$data_sudah = mysqli_fetch_assoc($jumlah_sudah);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Riwayat Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <!-- JUDUL -->

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                <div>

                    <h2>Riwayat Pelanggaran Siswa</h2>

                    <p class="text-muted mb-0">
                        Daftar seluruh catatan pelanggaran siswa.
                    </p>

                </div>

                <a href="dashboard.php"
                   class="btn btn-secondary">

                    Kembali ke Dashboard

                </a>

            </div>


            <!-- RINGKASAN -->

            <div class="row mb-4">

                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Riwayat
                            </h6>

                            <h3 class="fw-bold text-primary">
                                <?= $jumlah; ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Belum Ada Tindakan
                            </h6>

                            <h3 class="fw-bold text-danger">
                                <?= (int) $data_belum['total']; ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Sudah Ada Tindakan
                            </h6>

                            <h3 class="fw-bold text-success">
                                <?= (int) $data_sudah['total']; ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PENCARIAN DAN FILTER -->

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Pencarian dan Filter Riwayat
                    </h5>

                </div>


                <div class="card-body">

                    <form method="GET" action="riwayat.php">

                        <div class="row g-3">

                            <!-- PENCARIAN -->

                            <div class="col-md-6">

                                <label class="form-label">
                                    Cari Riwayat
                                </label>

                                <input
                                    type="text"
                                    name="cari"
                                    class="form-control"
                                    placeholder="Nama siswa, pelanggaran, kelas, guru..."
                                    value="<?= htmlspecialchars($cari); ?>">

                            </div>


                            <!-- FILTER TINDAKAN -->

                            <div class="col-md-3">

                                <label class="form-label">
                                    Filter Tindakan
                                </label>

                                <select name="tindakan"
                                        class="form-select">

                                    <option value="">
                                        Semua Data
                                    </option>

                                    <option value="belum"
                                        <?= $filter_tindakan === 'belum' ? 'selected' : ''; ?>>

                                        Belum Ditindak

                                    </option>

                                    <option value="sudah"
                                        <?= $filter_tindakan === 'sudah' ? 'selected' : ''; ?>>

                                        Sudah Ditindak

                                    </option>

                                </select>

                            </div>


                            <!-- TOMBOL -->

                            <div class="col-md-3 d-flex align-items-end gap-2">

                                <button type="submit"
                                        class="btn btn-primary">

                                    Cari

                                </button>

                                <a href="riwayat.php"
                                   class="btn btn-secondary">

                                    Reset

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- TABEL RIWAYAT -->

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Data Riwayat Pelanggaran
                    </h5>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelRiwayat"
                               class="table table-striped table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Guru Pencatat</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                                    <?php

                                    $tindakan_data = trim(
                                        $data['tindakan'] ?? ''
                                    );

                                    ?>

                                    <tr>

                                        <td></td>


                                        <td>
                                            <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                        </td>


                                        <td class="text-center">

                                            <span class="badge bg-warning text-dark">

                                                <?= (int) ($data['poin'] ?? 0); ?>

                                            </span>

                                        </td>


                                        <td>
                                            <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                        </td>


                                        <!-- TINDAKAN -->

                                        <td>

                                            <?php if ($tindakan_data !== '') { ?>

                                                <?= htmlspecialchars($tindakan_data); ?>

                                            <?php } else { ?>

                                                <span class="badge bg-danger">
                                                    Belum Ditindak
                                                </span>

                                            <?php } ?>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

                                            <?php

                                            $status_data = trim(
                                                $data['status'] ?? ''
                                            );

                                            if (strtolower($status_data) === 'selesai') {

                                                echo '<span class="badge bg-success">Selesai</span>';

                                            } elseif ($status_data !== '') {

                                                echo '<span class="badge bg-secondary">'
                                                    . htmlspecialchars($status_data)
                                                    . '</span>';

                                            } else {

                                                echo '<span class="badge bg-secondary">-</span>';

                                            }

                                            ?>

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>


<!-- JQUERY -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- BOOTSTRAP JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>


<!-- DATATABLES -->

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<!-- INISIALISASI DATATABLES -->

<script>

$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,

        ordering: true,

        paging: true,

        order: [[1, 'desc']],

        columnDefs: [

            {
                orderable: false,
                searchable: false,
                targets: 0
            }

        ],

        drawCallback: function () {

            var api = this.api();

            var nomorAwal = api.page.info().start;

            api.column(0, {

                search: 'applied',

                order: 'applied'

            }).nodes().each(function (cell, i) {

                cell.innerHTML = nomorAwal + i + 1;

            });

        },

        language: {

            search: "Cari di tabel:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data riwayat",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Riwayat pelanggaran tidak ditemukan",

            emptyTable: "Belum ada riwayat pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        }

    });

});

</script>

</body>
</html>
