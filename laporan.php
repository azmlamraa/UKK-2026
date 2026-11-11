
<?php
// laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// AMBIL DATA SESSION
// ========================================

$role = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['id'] ?? $_SESSION['id_user'] ?? 0);

// ========================================
// CEK HAK AKSES
// ========================================

if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya dapat diakses oleh Admin dan Guru Wali Kelas.');
}

// ========================================
// CEK GURU WALI KELAS AKTIF
// ========================================

$id_guru = 0;

if ($role === 'guru') {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT g.id
         FROM t_guru g
         INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
         WHERE g.user_id = ?
         AND wk.status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt_guru) {
        die("Gagal mengambil data guru: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($stmt_guru);

    if (!$data_guru) {
        http_response_code(403);
        exit('Akses ditolak. Anda bukan Guru Wali Kelas yang aktif.');
    }

    $id_guru = (int) $data_guru['id'];
}

// ========================================
// FILTER TANGGAL
// ========================================

$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Validasi format tanggal
if (
    ($tanggal_mulai !== '' &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai)) ||

    ($tanggal_selesai !== '' &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai))
) {
    exit('Format tanggal tidak valid.');
}

// Validasi tanggal sebenarnya
foreach ([$tanggal_mulai, $tanggal_selesai] as $tanggal) {

    if ($tanggal !== '') {

        $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

        if (!$date || $date->format('Y-m-d') !== $tanggal) {
            exit('Tanggal tidak valid.');
        }
    }
}

// Validasi tanggal mulai dan selesai
if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// ========================================
// QUERY LAPORAN
// ========================================

$sql = "SELECT
            ps.id,
            ps.tahun_ajaran_id,
            ps.siswa_id,
            ps.nama_siswa,
            ps.kelas_id,
            ps.nama_kelas,
            ps.pelanggaran_id,
            ps.nama_pelanggaran,
            ps.pelanggaran_kategori_id,
            ps.guru_id,
            ps.nama_guru,
            ps.tanggal,
            ps.keterangan,
            ps.poin,
            ps.tindakan,
            ps.status

        FROM t_pelanggaran_siswa ps

        WHERE 1 = 1";

$types = "";
$params = [];

// ========================================
// FILTER HAK AKSES GURU
// ========================================

// Guru hanya melihat pelanggaran dari kelas
// yang menjadi tanggung jawabnya.

if ($role === 'guru') {

    $sql .= " AND EXISTS (

                SELECT 1

                FROM t_wali_kelas wk

                WHERE wk.guru_id = ?
                AND wk.kelas_id = ps.kelas_id
                AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
                AND wk.status_aktif = 1

            )";

    $types .= "i";
    $params[] = $id_guru;
}

// ========================================
// FILTER TANGGAL MULAI
// ========================================

if ($tanggal_mulai !== '') {

    $sql .= " AND ps.tanggal >= ?";

    $types .= "s";
    $params[] = $tanggal_mulai;
}

// ========================================
// FILTER TANGGAL SELESAI
// ========================================

if ($tanggal_selesai !== '') {

    $sql .= " AND ps.tanggal <= ?";

    $types .= "s";
    $params[] = $tanggal_selesai;
}

// Urutkan laporan berdasarkan tanggal
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ========================================
// EKSEKUSI QUERY
// ========================================

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan laporan: " . htmlspecialchars(mysqli_error($koneksi)));
}

// Bind parameter
if (!empty($params)) {

    $bind_params = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    call_user_func_array('mysqli_stmt_bind_param', $bind_params);
}

// Jalankan query
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- DataTables Bootstrap 5 -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 7px;
        }

        .card-header {
            font-weight: 500;
        }

        .form-label {
            font-weight: 500;
        }

        table th,
        table td {
            vertical-align: middle;
        }

        @media print {

            .no-print,
            .sidebar,
            #tabelLaporan_wrapper .dt-search,
            #tabelLaporan_wrapper .dt-length,
            #tabelLaporan_wrapper .dt-paging,
            #tabelLaporan_wrapper .dt-info {
                display: none !important;
            }

            main {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background: white;
                font-size: 11px;
            }

            table {
                width: 100% !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-header {
                background: white !important;
                color: black !important;
                border-bottom: 1px solid black;
            }

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

            <!-- JUDUL -->
            <h1 class="mb-3">
                Laporan Pelanggaran Siswa
            </h1>

            <p class="text-muted">
                Halaman laporan data pelanggaran siswa.
            </p>


            <!-- INFORMASI AKSES -->
            <div class="alert alert-info no-print">

                <?php if ($role === 'admin') { ?>

                    Anda login sebagai <strong>Admin</strong>.
                    Anda dapat melihat seluruh laporan pelanggaran siswa.

                <?php } else { ?>

                    Anda login sebagai <strong>Guru Wali Kelas</strong>.
                    Anda hanya dapat melihat laporan dari kelas yang menjadi tanggung jawab Anda.

                <?php } ?>

            </div>


            <!-- ================================= -->
            <!-- FILTER LAPORAN -->
            <!-- ================================= -->

            <div class="card shadow-sm mb-4 no-print">

                <div class="card-header bg-primary text-white">

                    Filter Laporan

                </div>

                <div class="card-body">

                    <form method="GET" action="laporan.php">

                        <div class="row align-items-end">

                            <!-- TANGGAL MULAI -->
                            <div class="col-md-4 mb-3">

                                <label
                                    for="tanggal_mulai"
                                    class="form-label"
                                >
                                    Tanggal Mulai
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_mulai"
                                    id="tanggal_mulai"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggal_mulai); ?>"
                                >

                            </div>


                            <!-- TANGGAL SELESAI -->
                            <div class="col-md-4 mb-3">

                                <label
                                    for="tanggal_selesai"
                                    class="form-label"
                                >
                                    Tanggal Selesai
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_selesai"
                                    id="tanggal_selesai"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggal_selesai); ?>"
                                >

                            </div>


                            <!-- TOMBOL TAMPILKAN DAN RESET -->
                            <div class="col-md-4 mb-3">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Tampilkan
                                </button>

                                <a
                                    href="laporan.php"
                                    class="btn btn-secondary"
                                >
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- ================================= -->
            <!-- TABEL LAPORAN -->
            <!-- ================================= -->

            <div class="card shadow-sm">

                <!-- HEADER TABEL -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <span>
                        Data Laporan Pelanggaran
                    </span>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-light btn-sm no-print"
                    >
                        Cetak Laporan
                    </button>

                </div>


                <!-- BODY TABEL -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table
                            id="tabelLaporan"
                            class="table table-striped table-bordered table-hover align-middle w-100"
                        >

                            <!-- JUDUL KOLOM -->
                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>

                                    <th>Nama Siswa</th>

                                    <th>Kelas</th>

                                    <th>Nama Pelanggaran</th>

                                    <th>Guru Pencatat</th>

                                    <th>Tanggal</th>

                                    <th>Poin</th>

                                    <th>Tindakan</th>

                                    <th>Status</th>

                                </tr>

                            </thead>


                            <!-- ISI TABEL -->
                            <tbody>

                                <?php

                                $no = 1;

                                while ($row = mysqli_fetch_assoc($hasil)) {

                                ?>

                                    <tr>

                                        <!-- NOMOR -->
                                        <td>
                                            <?= $no++; ?>
                                        </td>


                                        <!-- NAMA SISWA -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>


                                        <!-- KELAS -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>


                                        <!-- NAMA PELANGGARAN -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                        </td>


                                        <!-- GURU PENCATAT -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                        </td>


                                        <!-- TANGGAL -->
                                        <td>
                                            <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                        </td>


                                        <!-- POIN -->
                                        <td>
                                            <?= (int) $row['poin']; ?>
                                        </td>


                                        <!-- TINDAKAN -->
                                        <td>
                                            <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                                        </td>


                                        <!-- STATUS -->
                                        <td>
                                            <?= htmlspecialchars($row['status'] ?? '-'); ?>
                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            <!-- TOMBOL KEMBALI -->
            <div class="mt-3 no-print">

                <a
                    href="dashboard.php"
                    class="btn btn-secondary"
                >
                    Kembali ke Dashboard
                </a>

            </div>

        </main>

    </div>

</div>


<!-- ================================= -->
<!-- JAVASCRIPT -->
<!-- ================================= -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelLaporan').DataTable({

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data laporan",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data laporan tidak ditemukan",

            emptyTable: "Belum ada data pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        },

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[5, 'desc']],

        columnDefs: [

            {

                targets: 0,

                orderable: false,

                searchable: false

            }

        ]

    });

});

</script>

</body>

</html>

<?php

// Tutup statement
mysqli_stmt_close($stmt);

?>
