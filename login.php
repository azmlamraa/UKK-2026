
<?php
session_start();

if (isset($_SESSION['role'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan_error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Pelanggaran Siswa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f3f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            width: 360px;
            background: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            border-top: 5px solid #0d6efd;
        }

        h2 {
            text-align: center;
            color: #212529;
            margin-bottom: 8px;
        }

        .deskripsi {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #212529;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 20px;
            outline: none;
        }

        input:focus {
            border-color: #0d6efd;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #0b5ed7;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 25px;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <h2>Login</h2>

        <p class="deskripsi">
            Sistem Informasi Pelanggaran Siswa
            <br>
            Silakan masuk menggunakan akun Anda
        </p>

        <?php if (!empty($pesan_error)) { ?>
            <div class="error">
                <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php } ?>

        <form action="proses_login.php" method="POST">

            <label>Email</label>
            <input type="email"
                   name="email"
                   placeholder="Masukkan email"
                   required>

            <label>Password</label>
            <input type="password"
                   name="password"
                   placeholder="Masukkan password"
                   required>

            <button type="submit">Login</button>

        </form>

        <div class="footer">
            &copy; <?= date('Y') ?> Sistem Pelanggaran Siswa
        </div>

    </div>

</body>
</html>
