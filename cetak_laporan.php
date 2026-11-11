
<?php
// cetak_laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// FUNGSI HTML AMAN
// ========================================

function e($data)
{
    return htmlspecialchars(
        (string) ($data ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}

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
    exit('Akses ditolak. Halaman hanya dapat diakses Admin dan Guru.');
}

// ========================================
// CEK GURU WALI KELAS AKTIF
// ========================================

$id_guru = 0;

if ($role === 'guru') {

    $sql_guru = "SELECT g.id
                 FROM t_guru g
                 INNER JOIN t_wali_kelas wk
                    ON wk.guru_id = g.id
                 WHERE g.user_id = ?
                   AND wk.status_aktif = 1
                 LIMIT 1";

    $stmt_guru = mysqli_prepare($koneksi, $sql_guru);

    if (!$stmt_guru) {
        die("Gagal mengambil data guru: " . e(mysqli_error($koneksi)));
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
foreach ([$tanggal_mulai, $tanggal_selesai] as $tanggal) {

    if ($tanggal !== '') {

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            exit('Format tanggal tidak valid.');
        }

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
// FILTER GURU
// ========================================

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

// Urutkan berdasarkan tanggal
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ========================================
// EKSEKUSI QUERY
// ========================================

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan laporan: " . e(mysqli_error($koneksi)));
}

// Bind parameter
if (!empty($params)) {

    $bind_params = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    if (!call_user_func_array('mysqli_stmt_bind_param', $bind_params)) {
        die("Gagal menghubungkan parameter laporan.");
    }
}

// Jalankan query
if (!mysqli_stmt_execute($stmt)) {
    die("Gagal mengambil data: " . e(mysqli_stmt_error($stmt)));
}

$hasil = mysqli_stmt_get_result($stmt);

if (!$hasil) {
    die("Gagal membaca hasil laporan.");
}

// ========================================
// HITUNG JUMLAH DATA DAN TOTAL POIN
// ========================================

$data_laporan = [];
$total_poin = 0;

while ($row = mysqli_fetch_assoc($hasil)) {

    $data_laporan[] = $row;

    $total_poin += (int) ($row['poin'] ?? 0);
}

$jumlah_data = count($data_laporan);

// ========================================
// FORMAT PERIODE LAPORAN
// ========================================

if ($tanggal_mulai !== '' && $tanggal_selesai !== '') {

    $periode = date('d-m-Y', strtotime($tanggal_mulai))
             . ' s/d '
             . date('d-m-Y', strtotime($tanggal_selesai));

} elseif ($tanggal_mulai !== '') {

    $periode = 'Mulai tanggal ' . date('d-m-Y', strtotime($tanggal_mulai));

} elseif ($tanggal_selesai !== '') {

    $periode = 'Sampai tanggal ' . date('d-m-Y', strtotime($tanggal_selesai));

} else {

    $periode = 'Seluruh Periode';
}

// Tanggal cetak
$tanggal_cetak = date('d-m-Y');

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cetak Laporan Pelanggaran Siswa</title>

    <style>

        /* =================================
           PENGATURAN DASAR
        ================================= */

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f3f5;
            color: #222;
            margin: 0;
            padding: 25px;
            font-size: 12px;
        }

        .container {
            background: white;
            max-width: 1200px;
            margin: auto;
            padding: 35px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }

        /* =================================
           TOMBOL
        ================================= */

        .tombol {
            max-width: 1200px;
            margin: 0 auto 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-cetak {
            background: #0d6efd;
        }

        .btn-kembali {
            background: #6c757d;
        }

        /* =================================
           KOP LAPORAN
        ================================= */

        .kop {
            text-align: center;
            border-bottom: 3px solid #222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .kop h2 {
            margin: 0 0 5px;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop h3 {
            margin: 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .kop p {
            margin: 4px 0;
            font-size: 11px;
        }

        /* =================================
           JUDUL LAPORAN
        ================================= */

        .judul {
            text-align: center;
            margin: 25px 0 20px;
        }

        .judul h3 {
            margin: 0 0 8px;
            font-size: 17px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .judul p {
            margin: 4px 0;
            font-size: 12px;
        }

        /* =================================
           INFORMASI LAPORAN
        ================================= */

        .informasi {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .informasi td {
            padding: 4px 2px;
            font-size: 11px;
        }

        /* =================================
           TABEL LAPORAN
        ================================= */

        .tabel-laporan {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tabel-laporan th,
        .tabel-laporan td {
            border: 1px solid #444;
            padding: 8px 6px;
            vertical-align: middle;
        }

        .tabel-laporan th {
            background: #e9ecef;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .tabel-laporan td {
            font-size: 10px;
        }

        .tengah {
            text-align: center;
        }

        .total {
            font-weight: bold;
            background: #f1f3f5;
        }

        /* =================================
           RINGKASAN
        ================================= */

        .ringkasan {
            margin-top: 15px;
            border: 1px solid #444;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 12px;
        }

        /* =================================
           TANDA TANGAN
        ================================= */

        .ttd {
            width: 100%;
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-kanan {
            width: 250px;
            text-align: center;
            font-size: 12px;
        }

        .ttd-nama {
            margin-top: 65px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* =================================
           FOOTER
        ================================= */

        .footer {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #aaa;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        /* =================================
           RESPONSIVE
        ================================= */

        @media screen and (max-width: 768px) {

            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                overflow-x: auto;
            }

            .tabel-laporan {
                min-width: 850px;
            }

            .ringkasan {
                gap: 10px;
                flex-direction: column;
            }
        }

        /* =================================
           PENGATURAN CETAK
        ================================= */

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {

            body {
                background: white;
                padding: 0;
                margin: 0;
                font-size: 10px;
            }

            .container {
                max-width: none;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .tabel-laporan {
                width: 100%;
            }

            .tabel-laporan th {
                background: #e9ecef !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total {
                background: #f1f3f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .tabel-laporan tr {
                page-break-inside: avoid;
            }

            .kop,
            .judul,
            .informasi {
                page-break-inside: avoid;
            }
        }

    </style>

</head>

<body>

    <!-- =================================
         TOMBOL AKSI
    ================================= -->

    <div class="tombol no-print">

        <a
            href="laporan.php?tanggal_mulai=<?= urlencode($tanggal_mulai); ?>&tanggal_selesai=<?= urlencode($tanggal_selesai); ?>"
            class="btn btn-kembali"
        >
            Kembali ke Laporan
        </a>

        <button
            type="button"
            onclick="window.print()"
            class="btn btn-cetak"
        >
            Cetak Laporan
        </button>

    </div>


    <div class="container">

        <!-- =================================
             KOP LAPORAN
        ================================= -->

        <div class="kop">

            <h2>NAMA SEKOLAH</h2>

            <h3>SISTEM INFORMASI PELANGGARAN SISWA</h3>

            <p>Dokumen Laporan Data Pelanggaran Siswa</p>

        </div>


        <!-- =================================
             JUDUL LAPORAN
        ================================= -->

        <div class="judul">

            <h3>Laporan Pelanggaran Siswa</h3>

            <p>
                Periode: <?= e($periode); ?>
            </p>

        </div>


        <!-- =================================
             INFORMASI LAPORAN
        ================================= -->

        <table class="informasi">

            <tr>

                <td width="130">Tanggal Cetak</td>

                <td width="10">:</td>

                <td><?= e($tanggal_cetak); ?></td>

            </tr>

            <tr>

                <td>Dicetak Oleh</td>

                <td>:</td>

                <td>
                    <?= $role === 'admin' ? 'Administrator' : 'Guru Wali Kelas'; ?>
                </td>

            </tr>

            <tr>

                <td>Jumlah Data</td>

                <td>:</td>

                <td>
                    <?= $jumlah_data; ?> Data Pelanggaran
                </td>

            </tr>

        </table>


        <!-- =================================
             TABEL DATA PELANGGARAN
        ================================= -->

        <table class="tabel-laporan">

            <thead>

                <tr>

                    <th width="35">No</th>

                    <th>Nama Siswa</th>

                    <th>Kelas</th>

                    <th>Nama Pelanggaran</th>

                    <th>Guru Pencatat</th>

                    <th>Tanggal</th>

                    <th width="50">Poin</th>

                    <th>Tindakan</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                <?php if ($jumlah_data > 0) { ?>

                    <?php $no = 1; ?>

                    <?php foreach ($data_laporan as $row) { ?>

                        <tr>

                            <td class="tengah">
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= e($row['nama_siswa']); ?>
                            </td>

                            <td class="tengah">
                                <?= e($row['nama_kelas']); ?>
                            </td>

                            <td>
                                <?= e($row['nama_pelanggaran']); ?>
                            </td>

                            <td>
                                <?= e($row['nama_guru']); ?>
                            </td>

                            <td class="tengah">

                                <?php
                                if (!empty($row['tanggal'])) {
                                    echo e(date('d-m-Y', strtotime($row['tanggal'])));
                                } else {
                                    echo '-';
                                }
                                ?>

                            </td>

                            <td class="tengah">
                                <?= (int) $row['poin']; ?>
                            </td>

                            <td>
                                <?= e($row['tindakan']); ?>
                            </td>

                            <td class="tengah">
                                <?= e($row['status']); ?>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td colspan="9" class="tengah">
                            Tidak ada data pelanggaran pada periode ini.
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

            <?php if ($jumlah_data > 0) { ?>

                <tfoot>

                    <tr class="total">

                        <td colspan="6" class="tengah">
                            TOTAL POIN
                        </td>

                        <td class="tengah">
                            <?= $total_poin; ?>
                        </td>

                        <td colspan="2"></td>

                    </tr>

                </tfoot>

            <?php } ?>

        </table>


        <!-- =================================
             RINGKASAN LAPORAN
        ================================= -->

        <div class="ringkasan">

            <span>
                Jumlah Data: <?= $jumlah_data; ?> Pelanggaran
            </span>

            <span>
                Total Poin: <?= $total_poin; ?> Poin
            </span>

        </div>


        <!-- =================================
             TANDA TANGAN
        ================================= -->

        <div class="ttd">

            <div class="ttd-kanan">

                <p>
                    Dicetak pada <?= e($tanggal_cetak); ?>
                </p>

                <p>
                    Mengetahui,
                </p>

                <p>
                    <?= $role === 'admin' ? 'Administrator' : 'Guru Wali Kelas'; ?>
                </p>

                <div class="ttd-nama">
                    (........................................)
                </div>

            </div>

        </div>


        <!-- =================================
             FOOTER
        ================================= -->

        <div class="footer">

            Laporan ini dibuat melalui Sistem Informasi Pelanggaran Siswa.

        </div>

    </div>

</body>

</html>

<?php

// ========================================
// TUTUP STATEMENT
// ========================================

mysqli_stmt_close($stmt);

?>
