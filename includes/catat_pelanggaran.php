
<?php
// catat_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data siswa
$sql_siswa = "
    SELECT
        ps.id,
        ps.nis,
        ps.nama,
        ps.kelas_id,
        k.nama AS nama_kelas
    FROM t_siswa ps
    LEFT JOIN t_kelas k ON ps.kelas_id = k.id
    WHERE ps.status_aktif = 1
    ORDER BY ps.nama ASC
";
$hasil_siswa = mysqli_query($koneksi, $sql_siswa);

// Ambil kategori pelanggaran
$sql_kategori = "
    SELECT id, nama
    FROM pelanggaran_kategori
    WHERE status_aktif = 1
    ORDER BY nama ASC
";
$hasil_kategori = mysqli_query($koneksi, $sql_kategori);

// Ambil jenis pelanggaran
$sql_pelanggaran = "
    SELECT
        p.id,
        p.nama,
        p.poin,
        p.pelanggaran_kategori_id,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p
    LEFT JOIN t_pelanggaran_kategori pk
        ON p.pelanggaran_kategori_id = pk.id
    WHERE p.status_aktif = 1
    ORDER BY p.nama ASC
";
$hasil_pelanggaran = mysqli_query($koneksi, $sql_pelanggaran);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Catat Pelanggaran Siswa</title>
</head>
<body>

    <h1>Catat Pelanggaran Siswa</h1>

    <?php
    if (isset($_SESSION['pesan_error'])) {
        echo '<p>' . htmlspecialchars($_SESSION['pesan_error']) . '</p>';
        unset($_SESSION['pesan_error']);
    }

    if (isset($_SESSION['pesan_sukses'])) {
        echo '<p>' . htmlspecialchars($_SESSION['pesan_sukses']) . '</p>';
        unset($_SESSION['pesan_sukses']);
    }
    ?>

    <form action="proses_simpan_pelanggaran.php" method="POST">

        <table cellpadding="6">

            <tr>
                <td>Siswa</td>
                <td>:</td>
                <td>
                    <select name="siswa_id" required>
                        <option value="">-- Pilih Siswa --</option>

                        <?php while ($s = mysqli_fetch_assoc($hasil_siswa)) { ?>
                            <option value="<?php echo $s['id']; ?>">
                                <?php
                                echo htmlspecialchars(
                                    $s['nis'] . ' - ' .
                                    $s['nama'] . ' - ' .
                                    $s['nama_kelas']
                                );
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Kategori Pelanggaran</td>
                <td>:</td>
                <td>
                    <select name="pelanggaran_kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>

                        <?php while ($k = mysqli_fetch_assoc($hasil_kategori)) { ?>
                            <option value="<?php echo $k['id']; ?>">
                                <?php echo htmlspecialchars($k['nama']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Jenis Pelanggaran</td>
                <td>:</td>
                <td>
                    <select name="pelanggaran_id" required>
                        <option value="">-- Pilih Pelanggaran --</option>

                        <?php while ($p = mysqli_fetch_assoc($hasil_pelanggaran)) { ?>
                            <option
                                value="<?php echo $p['id']; ?>"
                                data-kategori="<?php echo $p['pelanggaran_kategori_id']; ?>"
                                data-poin="<?php echo $p['poin']; ?>"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $p['nama'] . ' (' . $p['poin'] . ' poin)'
                                );
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Guru Pencatat (ID)</td>
                <td>:</td>
                <td>
                    <input type="number" name="guru_id" min="1" required>
                </td>
            </tr>

            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>
                    <input
                        type="date"
                        name="tanggal"
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >
                </td>
            </tr>

            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>
                    <textarea name="keterangan" rows="3" required></textarea>
                </td>
            </tr>

            <tr>
                <td>Poin</td>
                <td>:</td>
                <td>
                    <input type="number" name="poin" id="poin" min="0" required>
                </td>
            </tr>

            <tr>
                <td>Tindakan</td>
                <td>:</td>
                <td>
                    <textarea name="tindakan" rows="3"></textarea>
                </td>
            </tr>

            <tr>
                <td>Status</td>
                <td>:</td>
                <td>
                    <select name="status" required>
                        <option value="Belum Ditindaklanjuti">
                            Belum Ditindaklanjuti
                        </option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <input type="submit" value="Simpan Pelanggaran">
                </td>
            </tr>

        </table>
    </form>

    <p>
        <a href="dashboard.php">Kembali ke Dashboard</a> |
        <a href="kelola_pelanggaran_siswa.php">
            Kelola Pelanggaran Siswa
        </a>
    </p>

    <script>
        const pilihanPelanggaran =
            document.querySelector('select[name="pelanggaran_id"]');

        const pilihanKategori =
            document.querySelector('select[name="pelanggaran_kategori_id"]');

        const inputPoin = document.getElementById('poin');

        pilihanPelanggaran.addEventListener('change', function () {
            const opsi = this.options[this.selectedIndex];

            if (opsi.value !== '') {
                inputPoin.value = opsi.dataset.poin;
                pilihanKategori.value = opsi.dataset.kategori;
            } else {
                inputPoin.value = '';
            }
        });

        pilihanKategori.addEventListener('change', function () {
            const kategori = this.value;

            for (const opsi of pilihanPelanggaran.options) {
                if (opsi.value === '') {
                    opsi.hidden = false;
                    continue;
                }

                opsi.hidden = opsi.dataset.kategori !== kategori;
            }

            pilihanPelanggaran.value = '';
            inputPoin.value = '';
        });
    </script>

</body>
</html>
