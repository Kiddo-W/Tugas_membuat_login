<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: halaman_utama.php");
    exit;
}

$error = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT username, password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: halaman_utama.php");
            exit;
        } else {
            $error = "Username atau password salah. Silakan coba lagi.";
        }
    } else {
        $error = "Username atau password salah. Silakan coba lagi.";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <h1>Selamat datang kembali</h1>
            <p>Masuk untuk melanjutkan ke akun anda</p>
        </div>

        <div class="error-message" id="errorBox" style="<?php echo ($error !== '') ? 'display: flex;' : ''; ?>">
            <i class="fas fa-exclamation-circle"></i>
            <span id="errorText"><?php echo htmlspecialchars($error); ?></span>
        </div>

        <form class="auth-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" class="form-control" maxlength="16" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" minlength="8" maxlength="8" placeholder="Masukkan password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Tampilkan password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary">Masuk</button>
        </form>

        <div class="divider">
            <span>Belum punya akun?</span>
        </div>
        <a href="register.php" class="btn-secondary">Daftar</a>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const pwInput = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (pwInput.type === "password") {
                pwInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>