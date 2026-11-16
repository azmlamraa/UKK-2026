
<?php
// penempatan_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ===============================
// AMBIL DAFTAR SISWA
// ===============================
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// ===============================
// AMBIL DAFTAR TAHUN AJARAN
// ===============================
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// ===============================
// AMBIL DAFTAR KELAS
// ===============================
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// ===============================
// AMBIL DATA PENEMPATAN
// ===============================
$sql = "SELECT
            ks.id,
            s.nis,
            s.nama AS nama_siswa,
            ta.nama AS tahun_ajaran,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            ks.tanggal_mulai,
            ks.tanggal_selesai,
            ks.status_aktif
        FROM t_kelas_siswa ks
        LEFT JOIN t_siswa s
            ON ks.siswa_id = s.id
        LEFT JOIN t_tahun_ajaran ta
            ON ks.tahun_ajaran_id = ta.id
        LEFT JOIN t_kelas k
            ON ks.kelas_id = k.id
        ORDER BY ks.id DESC";

$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penempatan Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap -->
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <style>

        body {
            background-color: #f4f7fb;
        }

        .main-content {
            padding: 30px;
        }

        /* ===============================
           HEADER
        =============================== */

        .page-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            border-radius: 18px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
        }

        .page-header h2 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        /* ===============================
           CARD
        =============================== */

        .content-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
        }

        /* ===============================
           FORM
        =============================== */

        .form-label {
            font-weight: 600;
            color: #374151;
        }

        .form-control,
        .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            padding: 10px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-simpan {
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .btn-simpan:hover {
            background-color: #1d4ed8;
            color: white;
        }

        .btn-dashboard {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 9px 15px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-dashboard:hover {
            background-color: #f3f4f6;
            color: #111827;
        }

        /* ===============================
           ALERT
        =============================== */

        .alert {
            border-radius: 10px;
        }

        /* ===============================
           TABEL
        =============================== */

        #tabelPenempatan {
            width: 100% !important;
            margin: 0 !important;
            table-layout: fixed;
        }

        #tabelPenempatan thead th {
            background-color: #212529;
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 12px 8px;
            white-space: nowrap;
        }

        #tabelPenempatan tbody td {
            font-size: 12px;
            padding: 11px 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        #tabelPenempatan tbody tr:hover {
            background-color: #f8fafc;
        }

        /* ===============================
           LEBAR KOLOM
        =============================== */

        #tabelPenempatan th:nth-child(1),
        #tabelPenempatan td:nth-child(1) {
            width: 5%;
            text-align: center;
        }

        #tabelPenempatan th:nth-child(2),
        #tabelPenempatan td:nth-child(2) {
            width: 9%;
        }

        #tabelPenempatan th:nth-child(3),
        #tabelPenempatan td:nth-child(3) {
            width: 15%;
        }

        #tabelPenempatan th:nth-child(4),
        #tabelPenempatan td:nth-child(4) {
            width: 11%;
        }

        #tabelPenempatan th:nth-child(5),
        #tabelPenempatan td:nth-child(5) {
            width: 9%;
        }

        #tabelPenempatan th:nth-child(6),
        #tabelPenempatan td:nth-child(6) {
            width: 8%;
        }

        #tabelPenempatan th:nth-child(7),
        #tabelPenempatan td:nth-child(7) {
            width: 9%;
        }

        #tabelPenempatan th:nth-child(8),
        #tabelPenempatan td:nth-child(8) {
            width: 12%;
        }

        #tabelPenempatan th:nth-child(9),
        #tabelPenempatan td:nth-child(9) {
            width: 12%;
        }

        #tabelPenempatan th:nth-child(10),
        #tabelPenempatan td:nth-child(10) {
            width: 10%;
            text-align: center;
        }

        /* ===============================
           STATUS
        =============================== */

        .status-aktif {
            background-color: #dcfce7;
            color: #15803d;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-nonaktif {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        /* ===============================
           DATATABLES
        =============================== */

        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 7px 10px !important;
            margin-left: 8px !important;
        }

        .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            padding: 5px 8px;
        }

        .dataTables_info {
            margin-top: 15px;
        }

        .dataTables_paginate {
            margin-top: 15px;
        }

        /* PENTING:
           TIDAK ADA SCROLL HORIZONTAL */

        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }

        /* ===============================
           HILANGKAN GARIS GESER
        =============================== */

        .table-responsive {
            overflow-x: hidden !important;
        }

        /* ===============================
           RESPONSIVE
        =============================== */

        @media (max-width: 1100px) {

            #tabelPenempatan th,
            #tabelPenempatan td {
                font-size: 11px;
                padding: 7px 5px;
            }

            .status-aktif,
            .status-nonaktif {
                font-size: 10px;
                padding: 4px 7px;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 main-content">

            <!-- HEADER -->
            <div class="page-header">

                <h2>👨‍🎓 Penempatan Siswa</h2>

                <p>
                    Kelola penempatan siswa ke dalam kelas berdasarkan tahun ajaran.
                </p>

            </div>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                <div class="alert alert-success">

                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>

                </div>

            <?php } ?>


            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger">

                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>

                </div>

            <?php } ?>


            <!-- FORM PENEMPATAN -->
            <div class="content-card">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h5 class="fw-bold mb-1">
                            📝 Form Penempatan Siswa
                        </h5>

                        <small class="text-muted">
                            Masukkan siswa ke kelas yang sesuai.
                        </small>

                    </div>

                    <a href="dashboard.php"
                       class="btn-dashboard">
                        ← Dashboard
                    </a>

                </div>


                <form action="proses_simpan_penempatan.php" method="POST">

                    <div class="row g-3">

                        <!-- SISWA -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Siswa
                            </label>

                            <select name="siswa_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    -- Pilih Siswa --
                                </option>

                                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                    <option value="<?php echo $s['id']; ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            $s['nis'] . ' - ' . $s['nama']
                                        );
                                        ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- TAHUN AJARAN -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Tahun Ajaran
                            </label>

                            <select name="tahun_ajaran_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    -- Pilih Tahun Ajaran --
                                </option>

                                <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>

                                    <option value="<?php echo $ta['id']; ?>">

                                        <?php
                                        echo htmlspecialchars($ta['nama']);
                                        ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- KELAS -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Kelas
                            </label>

                            <select name="kelas_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    -- Pilih Kelas --
                                </option>

                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                    <option value="<?php echo $k['id']; ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            $k['nama'] .
                                            ' - Tingkat ' .
                                            $k['tingkat'] .
                                            ' - ' .
                                            $k['jurusan']
                                        );
                                        ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- STATUS -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1">
                                    Aktif
                                </option>

                                <option value="0">
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>


                        <!-- TANGGAL MULAI -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   required>

                        </div>


                        <!-- TANGGAL SELESAI -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control">

                        </div>

                    </div>


                    <div class="mt-4">

                        <button type="submit"
                                class="btn-simpan">
                            💾 Simpan Penempatan
                        </button>

                    </div>

                </form>

            </div>


            <!-- DATA PENEMPATAN -->
            <div class="content-card">

                <div class="mb-4">

                    <h5 class="fw-bold mb-1">
                        👥 Data Penempatan Siswa
                    </h5>

                    <small class="text-muted">
                        Daftar siswa yang sudah ditempatkan ke kelas.
                    </small>

                </div>


                <!-- TABEL -->

                <table id="tabelPenempatan"
                       class="table table-bordered table-hover align-middle">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NIS</th>

                            <th>Nama Siswa</th>

                            <th>Tahun Ajaran</th>

                            <th>Kelas</th>

                            <th>Tingkat</th>

                            <th>Jurusan</th>

                            <th>Tanggal Mulai</th>

                            <th>Tanggal Selesai</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $no = 1;

                        while ($row = mysqli_fetch_assoc($hasil)) {

                        ?>

                        <tr>

                            <td>
                                <?php echo $no++; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['nis'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['nama_siswa'] ?? '-'
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['tahun_ajaran'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['nama_kelas'] ?? '-'
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['tingkat'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['jurusan'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['tanggal_mulai'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>

                                <?php

                                if (
                                    !empty($row['tanggal_selesai']) &&
                                    $row['tanggal_selesai'] != '0000-00-00'
                                ) {

                                    echo htmlspecialchars(
                                        $row['tanggal_selesai']
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>

                            <td>

                                <?php if ($row['status_aktif'] == 1) { ?>

                                    <span class="status-aktif">
                                        Aktif
                                    </span>

                                <?php } else { ?>

                                    <span class="status-nonaktif">
                                        Tidak Aktif
                                    </span>

                                <?php } ?>

                            </td>

                        </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </main>

    </div>

</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>


<script>

$(document).ready(function () {

    $('#tabelPenempatan').DataTable({

        paging: true,

        searching: true,

        ordering: true,

        info: true,

        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],

        /* JANGAN PAKAI scrollX */

        autoWidth: false,

        order: [
            [0, 'asc']
        ],

        language: {

            search: "Cari:",

            searchPlaceholder:
                "Cari siswa, NIS, kelas...",

            lengthMenu:
                "Tampilkan _MENU_ data",

            info:
                "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",

            infoEmpty:
                "Menampilkan 0 sampai 0 dari 0 siswa",

            infoFiltered:
                "(disaring dari _MAX_ total siswa)",

            zeroRecords:
                "Data penempatan tidak ditemukan",

            emptyTable:
                "Belum ada data penempatan siswa",

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