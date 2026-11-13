
<?php
// cetak_laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// AMBIL DATA SESSION
// ========================================

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? $_SESSION['id_user'] ?? 0);

// ========================================
// CEK HAK AKSES
// ========================================

if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    die("Anda tidak memiliki akses.");
}

// ========================================
// CEK WALI KELAS UNTUK ROLE GURU
// ========================================

$id_guru = 0;

if ($role === 'guru') {

    $query_guru = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($query_guru, "i", $id_user);
    mysqli_stmt_execute($query_guru);

    $hasil_guru = mysqli_stmt_get_result($query_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($query_guru);

    if (!$data_guru) {
        http_response_code(403);
        die("Data guru tidak ditemukan.");
    }

    $id_guru = (int) $data_guru['id'];

    $cek_wali = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM t_wali_kelas
         WHERE guru_id = ?
         AND status_aktif = 1
         LIMIT 1"
    );

    mysqli_stmt_bind_param($cek_wali, "i", $id_guru);
    mysqli_stmt_execute($cek_wali);

    $hasil_wali = mysqli_stmt_get_result($cek_wali);
    $data_wali = mysqli_fetch_assoc($hasil_wali);

    mysqli_stmt_close($cek_wali);

    if (!$data_wali) {
        http_response_code(403);
        die("Anda bukan wali kelas aktif.");
    }
}

// ========================================
// FUNGSI TANGGAL INDONESIA
// ========================================

function tanggalIndonesia($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $pecah = explode('-', $tanggal);

    if (count($pecah) !== 3) {
        return $tanggal;
    }

    return (int) $pecah[2] . ' ' .
           $bulan[(int) $pecah[1]] . ' ' .
           $pecah[0];
}

// ========================================
// FILTER TANGGAL
// ========================================

$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

if (
    ($tanggal_mulai !== '' &&
    !DateTime::createFromFormat('Y-m-d', $tanggal_mulai)) ||
    ($tanggal_selesai !== '' &&
    !DateTime::createFromFormat('Y-m-d', $tanggal_selesai))
) {
    die("Format tanggal tidak valid.");
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    die("Tanggal mulai tidak boleh melebihi tanggal selesai.");
}

// ========================================
// QUERY DATA LAPORAN
// ========================================

$sql = "SELECT
            ps.id,
            ps.nama_siswa,
            ps.nama_kelas,
            ps.nama_pelanggaran,
            ps.nama_guru,
            ps.tanggal,
            ps.keterangan,
            ps.poin,
            ps.tindakan,
            ps.status
        FROM t_pelanggaran_siswa ps
        WHERE 1=1";

$types = "";
$params = [];

// ========================================
// FILTER KHUSUS WALI KELAS
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
// FILTER TANGGAL
// ========================================

if ($tanggal_mulai !== '') {
    $sql .= " AND ps.tanggal >= ?";
    $types .= "s";
    $params[] = $tanggal_mulai;
}

if ($tanggal_selesai !== '') {
    $sql .= " AND ps.tanggal <= ?";
    $types .= "s";
    $params[] = $tanggal_selesai;
}

$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ========================================
// EKSEKUSI QUERY
// ========================================

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query gagal dipersiapkan: " . mysqli_error($koneksi));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

// ========================================
// HITUNG JUMLAH DATA
// ========================================

$total_data = mysqli_num_rows($result);

// ========================================
// TANGGAL CETAK
// ========================================

$tanggal_cetak = tanggalIndonesia(date('Y-m-d'));

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cetak Laporan Pelanggaran Siswa</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", serif;
            color: #000;
            font-size: 12px;
            margin: 0;
        }

        .container {
            width: 100%;
        }

        .kop-surat {
            text-align: center;
            position: relative;
            padding-bottom: 12px;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }

        .kop-surat h2 {
            font-size: 20px;
            margin: 0 0 5px;
            text-transform: uppercase;
        }

        .kop-surat h3 {
            font-size: 17px;
            margin: 0 0 5px;
            text-transform: uppercase;
        }

        .kop-surat p {
            font-size: 12px;
            margin: 3px 0;
        }

        .judul {
            text-align: center;
            margin: 20px 0;
        }

        .judul h3 {
            font-size: 17px;
            text-decoration: underline;
            margin: 0 0 7px;
        }

        .judul p {
            margin: 0;
        }

        .informasi {
            margin-bottom: 12px;
            line-height: 1.7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
        }

        table th {
            text-align: center;
            background: #eaeaea;
            font-weight: bold;
        }

        table td {
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .ttd {
            width: 100%;
            margin-top: 35px;
            border: none;
        }

        .ttd td {
            width: 50%;
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }

        .nama-ttd {
            margin-top: 65px;
            font-weight: bold;
            text-decoration: underline;
        }

        .tombol {
            margin-bottom: 20px;
            text-align: right;
        }

        .tombol button,
        .tombol a {
            display: inline-block;
            padding: 9px 15px;
            background: #198754;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .tombol a {
            background: #6c757d;
        }

        .kosong {
            text-align: center;
            padding: 15px;
        }

        @media print {
            .tombol {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="tombol">
        <a href="laporan.php?<?= htmlspecialchars(http_build_query([
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai
        ])) ?>">
            Kembali
        </a>

        <button onclick="window.print()">
            Cetak Laporan
        </button>
    </div>

    <!-- KOP SURAT -->

    <div class="kop-surat">

        <h2>Laporan Resmi Sekolah</h2>

        <h3>SMK Muhammadiyah Kota Tasikmalaya</h3>

        <p>Kota Tasikmalaya, Jawa Barat</p>

    </div>

    <!-- JUDUL LAPORAN -->

    <div class="judul">

        <h3>LAPORAN DATA PELANGGARAN SISWA</h3>

        <p>SMK Muhammadiyah Kota Tasikmalaya</p>

    </div>

    <!-- INFORMASI LAPORAN -->

    <div class="informasi">

        <strong>Tanggal Cetak:</strong>
        <?= htmlspecialchars($tanggal_cetak) ?>
        <br>

        <strong>Periode Laporan:</strong>

        <?php
        if ($tanggal_mulai !== '' && $tanggal_selesai !== '') {
            echo htmlspecialchars(tanggalIndonesia($tanggal_mulai));
            echo " - ";
            echo htmlspecialchars(tanggalIndonesia($tanggal_selesai));
        } elseif ($tanggal_mulai !== '') {
            echo "Mulai " . htmlspecialchars(tanggalIndonesia($tanggal_mulai));
        } elseif ($tanggal_selesai !== '') {
            echo "Sampai " . htmlspecialchars(tanggalIndonesia($tanggal_selesai));
        } else {
            echo "Seluruh Periode";
        }
        ?>

        <br>

        <strong>Jumlah Data:</strong>
        <?= $total_data ?> Data Pelanggaran

        <br>

        <strong>Dicetak Oleh:</strong>
        <?= $role === 'admin' ? 'Administrator' : 'Guru Wali Kelas' ?>

    </div>

    <!-- TABEL LAPORAN -->

    <table>

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

            <?php if ($total_data > 0): ?>

                <?php $no = 1; ?>

                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td class="text-center">
                            <?= $no++ ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_siswa'] ?? '-') ?>
                        </td>

                        <td class="text-center">
                            <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_guru'] ?? '-') ?>
                        </td>

                        <td class="text-center">
                            <?= htmlspecialchars(tanggalIndonesia($row['tanggal'] ?? '')) ?>
                        </td>

                        <td class="text-center">
                            <?= htmlspecialchars((string) ($row['poin'] ?? 0)) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['tindakan'] ?? '-') ?>
                        </td>

                        <td class="text-center">
                            <?= htmlspecialchars($row['status'] ?? '-') ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="9" class="kosong">
                        Tidak ada data pelanggaran siswa.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

    <!-- TANDA TANGAN -->

    <table class="ttd">

        <tr>

            <td>
                Mengetahui,<br>
                Kepala SMK Muhammadiyah Kota Tasikmalaya

                <div class="nama-ttd">
                    ........................................
                </div>

                NBM. ................................
            </td>

            <td>
                Tasikmalaya, <?= htmlspecialchars($tanggal_cetak) ?><br>
                Petugas Pengelola Data,

                <div class="nama-ttd">
                    Administrator
                </div>

            </td>

        </tr>

    </table>

</div>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
