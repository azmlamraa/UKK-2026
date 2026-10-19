<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data laporan pelanggaran siswa
$sql = "SELECT
            ps.id,
            s.nis,
            s.nama,
            pk.nama AS pelanggaran
        FROM t_pelanggaran_siswa AS ps
        JOIN t_siswa AS s ON ps.siswa_id = s.id
        JOIN t_pelanggaran_kategori AS pk
            ON ps.pelanggaran_id = pk.id
        ORDER BY ps.id DESC";

$data = mysqli_query($koneksi, $sql);

// Periksa jika query gagal
if (!$data) {
    die("Gagal mengambil data laporan: " .
        mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #ffffff;
            font-family: Arial, sans-serif;
            color: #212529;
        }

        .page-title {
            font-size: 36px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .navigation {
            margin-bottom: 18px;
            font-size: 14px;
        }

        .navigation a {
            color: #0d6efd;
            text-decoration: none;
        }

        .navigation a:hover {
            text-decoration: underline;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .table-responsive {
            width: 100%;
        }

        #tabelLaporan {
            width: 100%;
        }

        #tabelLaporan thead th {
            background-color: #f1f1f1;
            color: #212529;
            vertical-align: middle;
            white-space: nowrap;
        }

        #tabelLaporan tbody td {
            vertical-align: middle;
        }

        #tabelLaporan tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .dataTables_wrapper {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <!-- Konten utama -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="page-title">
                Laporan Pelanggaran Siswa
            </h1>

            <div class="navigation">
                <a href="dashboard.php">Kembali ke Dashboard</a>
                | Laporan Pelanggaran
            </div>

            <!-- Tombol aksi -->
            <div class="action-buttons">

                <a href="cetak_laporan.php"
                   target="_blank"
                   class="btn btn-primary">
                    Cetak Laporan
                </a>

                <a href="export_laporan.php"
                   class="btn btn-success">
                    Export Excel
                </a>

            </div>

            <!-- Tabel laporan -->
            <div class="table-responsive">

                <table id="tabelLaporan"
                       class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Pelanggaran</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        if (mysqli_num_rows($data) > 0) {
                            while ($row = mysqli_fetch_assoc($data)) {
                        ?>

                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['nis'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['nama'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['pelanggaran'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>
                        </tr>

                        <?php
                            }
                        } else {
                        ?>

                        <tr>
                            <td colspan="4" class="text-center">
                                Belum ada data pelanggaran siswa.
                            </td>
                        </tr>

                        <?php
                        }
                        ?>
                    </tbody>

                </table>

            </div>

        </main>

    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    const tabel = $('#tabelLaporan');

    // Aktifkan DataTables hanya jika terdapat data laporan
    if (tabel.find('tbody tr').length > 0 &&
        tabel.find('tbody tr td[colspan]').length === 0) {

        tabel.DataTable({
            language: {
                search: "Cari:",
                searchPlaceholder: "Cari laporan...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data yang dicari tidak ditemukan",
                emptyTable: "Belum ada data pelanggaran siswa",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            ordering: true,
            searching: true,
            paging: true,
            info: true,
            order: [[0, 'asc']],
            columnDefs: