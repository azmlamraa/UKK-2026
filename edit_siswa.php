<?php
// edit_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_GET['id'];

$sql = "SELECT * FROM tbl_siswa WHERE id = '$id'";
$hasil = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($hasil);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Siswa</title>
</head>

<body>

    <h1>Edit Siswa</h1>

    <form action="proses_edit_siswa.php" method="POST">

        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <table>

            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>
                    <input type="text" name="nip"
                        value="<?php echo $data['nis']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>NISN</td>
                <td>:</td>
                <td>
                    <input type="text" name="nisn"
                        value="<?php echo $data['nisn']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>Nama </td>
                <td>:</td>
                <td>
                    <input type="text" name="nama"
                        value="<?php echo $data['nama']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>
                    <input type="text" name="jenis_kelamin"
                        value="<?php echo $data['jenis_kelamin']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>Tanggal Lahir</td>
                <td>:</td>
                <td>
                    <input type="text" name="tanggal_lahir"
                        value="<?php echo $data['tanggal_lahir']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>
                    <input type="text" name="alamat"
                        value="<?php echo $data['alamat']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>Status Aktif</td>
                <td>:</td>
                <td>
                    <select name="status_aktif" required>

                        <option value="1"
                            <?php if ($data['status_aktif'] == 1) echo 'selected'; ?>>
                            Aktif
                        </option>

                        <option value="0"
                            <?php if ($data['status_aktif'] == 0) echo 'selected'; ?>>
                            Tidak Aktif
                        </option>

                    </select>
                </td>
            </tr>

        </table>

    </form>

    <p>
        <a href="kelola_siswa.php">Kembali ke Kelola Guru</a>
    </p>

</body>
</html>