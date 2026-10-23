
<?php
// login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    <title>Login - Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #212529;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .login-card {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        /* Bagian kiri menyerupai sidebar dashboard */
        .login-sidebar {
            background: #212529;
            color: #ffffff;
            padding: 45px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .school-icon {
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #343a40;
            border-radius: 6px;
            font-size: 32px;
            margin-bottom: 25px;
        }

        .login-sidebar h2 {
            font-size: 27px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .login-sidebar p {
            color: #dee2e6;
            line-height: 1.7;
        }

        .feature {
            margin-top: 22px;
        }

        .feature strong {
            color: #ffffff;
        }

        .feature small {
            display: block;
            color: #adb5bd;
            margin-top: 5px;
        }

        /* Bagian form */
        .login-content {
            padding: 45px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-content h3 {
            font-size: 27px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 11px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
        }

        .btn-login {
            background: #0d6efd;
            color: white;
            padding: 11px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #0b5ed7;
            color: white;
        }

        .btn-toggle {
            border: 1px solid #ced4da;
            border-radius: 0 5px 5px 0;
            background: #ffffff;
            color: #212529;
        }

        .btn-toggle:hover {
            background: #f1f3f5;
        }

        .footer {
            border-top: 1px solid #dee2e6;
            margin-top: 25px;
            padding-top: 20px;
            color: #6c757d;
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 767px) {
            .login-container {
                padding: 15px;
            }

            .login-card {
                grid-template-columns: 1fr;
            }

            .login-sidebar {
                padding: 30px 25px;
            }

            .login-sidebar h2 {
                font-size: 23px;
            }

            .login-content {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-card">

        <!-- BAGIAN INFORMASI -->
        <div class="login-sidebar">

            <div class="school-icon">
                &#127979;
            </div>

            <h2>Sistem Pelanggaran Siswa</h2>

            <p>
                Selamat datang di sistem informasi pengelolaan
                data pelanggaran dan kedisiplinan siswa.
            </p>

            <div class="feature">
                <strong>&#10003; Data Siswa</strong>
                <small>Kelola informasi siswa dengan mudah.</small>
            </div>

            <div class="feature">
                <strong>&#10003; Data Guru dan Kelas</strong>
                <small>Kelola data guru, kelas, dan tahun ajaran.</small>
            </div>

            <div class="feature">
                <strong>&#10003; Laporan Pelanggaran</strong>
                <small>Pantau dan rekap data pelanggaran siswa.</small>
            </div>

        </div>

        <!-- BAGIAN FORM LOGIN -->
        <div class="login-content">

            <h3>Login</h3>

            <p class="subtitle">
                Silakan masuk untuk melanjutkan.
            </p>

            <?php if (!empty($pesan_error)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($pesan_error) ?>
                </div>
            <?php } ?>

            <form action="proses_login.php" method="POST">

                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <div class="input-group">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="btn btn-toggle"
                            id="togglePassword"
                        >
                            Lihat
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Login Sekarang
                </button>

            </form>

            <div class="footer">
                &copy; <?= date('Y') ?> Sistem Pelanggaran Siswa
                <br>
                Akses khusus pengguna terdaftar.
            </div>

        </div>

    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const password = document.getElementById('password');

    if (password.type === 'password') {
        password.type = 'text';
        this.textContent = 'Sembunyikan';
    } else {
        password.type = 'password';
        this.textContent = 'Lihat';
    }
});
</script>

</body>
</html>
