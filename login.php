
<?php
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

    <title>Login - Sistem Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #0f2f75, #2563eb, #60a5fa);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Dekorasi background */
        body::before {
            content: "";
            position: fixed;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            top: -120px;
            left: -100px;
        }

        body::after {
            content: "";
            position: fixed;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            bottom: -180px;
            right: -100px;
        }

        /* CARD LOGIN */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 22px;
            padding: 40px 38px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
            animation: muncul 0.6s ease;
        }

        @keyframes muncul {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* HEADER */
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .school-icon {
            width: 85px;
            height: 85px;
            background: linear-gradient(135deg, #1d4ed8, #60a5fa);
            color: white;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(37,99,235,0.25);
        }

        .login-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: #172554;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 13px;
            color: #64748b;
            line-height: 1.7;
            margin: 0;
        }

        .header-line {
            width: 55px;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            border-radius: 10px;
            margin: 18px auto 0;
        }

        /* INFORMASI FITUR */
        .feature-list {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 28px;
            padding: 15px 10px;
            background: #eff6ff;
            border-radius: 12px;
        }

        .feature-item {
            text-align: center;
            flex: 1;
            color: #2563eb;
            font-size: 19px;
        }

        .feature-item small {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            margin-top: 6px;
        }

        /* FORM */
        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-group-text {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            border-radius: 10px 0 0 10px;
            padding: 12px 15px;
        }

        .form-control {
            background: #f8fafc;
            border: 1px solid #dbeafe;
            padding: 12px;
            font-size: 14px;
        }

        .form-control:focus {
            background: white;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        .password-input {
            border-radius: 10px 0 0 10px !important;
        }

        .btn-toggle {
            background: #f8fafc;
            border: 1px solid #dbeafe;
            border-left: none;
            border-radius: 0 10px 10px 0;
            color: #2563eb;
            padding: 0 13px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-toggle:hover {
            background: #eff6ff;
        }

        /* BUTTON LOGIN */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            margin-top: 5px;
            box-shadow: 0 7px 18px rgba(37,99,235,0.25);
            transition: 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(37,99,235,0.3);
        }

        .alert {
            font-size: 13px;
            border-radius: 10px;
        }

        /* FOOTER */
        .login-footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 25px;
            padding-top: 18px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .login-footer i {
            color: #2563eb;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 23px;
                border-radius: 18px;
            }

            .login-header h2 {
                font-size: 21px;
            }

            .school-icon {
                width: 72px;
                height: 72px;
                font-size: 32px;
            }
        }
    </style>
</head>

<body>

<div class="login-card">

    <!-- HEADER -->
    <div class="login-header">

        <div class="school-icon">
            <i class="fas fa-school"></i>
        </div>

        <h2>Sistem Pelanggaran Siswa</h2>

        <p>
            Sistem Informasi Pengelolaan Data Pelanggaran
            dan Kedisiplinan Siswa
        </p>

        <div class="header-line"></div>

    </div>

    <!-- FITUR SISTEM -->
    <div class="feature-list">

        <div class="feature-item">
            <i class="fas fa-user-graduate"></i>
            <small>Data Siswa</small>
        </div>

        <div class="feature-item">
            <i class="fas fa-chalkboard-teacher"></i>
            <small>Data Guru</small>
        </div>

        <div class="feature-item">
            <i class="fas fa-clipboard-list"></i>
            <small>Laporan</small>
        </div>

    </div>

    <!-- FORM LOGIN -->
    <h5 class="fw-bold mb-1" style="color:#172554;">
        Login Akun
    </h5>

    <p class="text-secondary small mb-4">
        Silakan masuk untuk melanjutkan.
    </p>

    <?php if (!empty($pesan_error)) { ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php } ?>

    <form action="proses_login.php" method="POST">

        <div class="mb-3">

            <label for="email" class="form-label">
                Email
            </label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>

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

        </div>

        <div class="mb-4">

            <label for="password" class="form-label">
                Password
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control password-input"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

                <button
                    type="button"
                    class="btn-toggle"
                    id="togglePassword"
                >
                    <i class="fas fa-eye"></i>
                    Lihat
                </button>

            </div>

        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>
            Login Sekarang
        </button>

    </form>

    <!-- FOOTER -->
    <div class="login-footer">

        <i class="fas fa-shield-alt me-1"></i>
        Akses khusus pengguna terdaftar.

        <br>

        &copy; <?= date('Y') ?> Sistem Pelanggaran Siswa.
        All Rights Reserved.

    </div>

</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {

    const password = document.getElementById('password');

    if (password.type === 'password') {
        password.type = 'text';
        this.innerHTML = '<i class="fas fa-eye-slash"></i> Sembunyikan';
    } else {
        password.type = 'password';
        this.innerHTML = '<i class="fas fa-eye"></i> Lihat';
    }

});
</script>

</body>
</html>
