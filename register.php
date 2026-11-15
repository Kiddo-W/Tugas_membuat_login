<?php
session_start();
require 'koneksi.php';

// Jika user sudah login, langsung arahkan ke halaman utama
if (isset($_SESSION['username'])) {
    header("Location: halaman_utama.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok. Silakan coba lagi.";
    } else {
        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Simpan ke database (menggunakan struktur tabelmu: username & password)
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

        try {
            mysqli_stmt_execute($stmt);
            $_SESSION['username'] = $username;
            header("Location: halaman_utama.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = "Username sudah terpakai. Silakan pilih username lain.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="register.css">
</head>

<body>

    <div class="auth-container">
        <div class="auth-header">
            <h1>Buat akun</h1>
            <p>Daftar untuk membuat akun anda</p>
        </div>

        <!-- Kotak error tersambung dengan variabel $error dari PHP -->
        <div class="error-message" id="errorBox" style="<?php echo ($error !== '') ? 'display: flex;' : ''; ?>">
            <i class="fas fa-exclamation-circle"></i>
            <span
                id="errorText"><?php echo ($error !== '') ? htmlspecialchars($error) : 'Konfirmasi password tidak cocok. Silakan coba lagi.'; ?></span>
        </div>

        <form class="auth-form" method="POST" action="" onsubmit="return cekPassword()">
            <!-- Input Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" class="form-control" maxlength="16"
                        placeholder="Masukkan username" required>
                </div>
            </div>

            <!-- Input Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" minlength="8"
                        maxlength="8" placeholder="Masukkan password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)"
                        aria-label="Tampilkan password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Input Konfirmasi Password -->
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                        minlength="8" maxlength="8" placeholder="Konfirmasi password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)"
                        aria-label="Tampilkan konfirmasi password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn-primary">Daftar</button>
        </form>

        <!-- Bagian Bawah -->
        <div class="divider">
            <span>Sudah punya akun?</span>
        </div>
        <!-- Diubah dari login.html ke login.php -->
        <a href="login.php" class="btn-secondary">Masuk</a>
    </div>

    <script>
        // Fungsi tombol eye password
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

        // Validasi konfirmasi password
        function cekPassword() {
            const pass = document.getElementById('password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            const errorBox = document.getElementById('errorBox');
            const errorText = document.getElementById('errorText');

            if (pass !== confirmPass) {
                errorText.textContent = "Konfirmasi password tidak cocok. Silakan coba lagi.";
                errorBox.style.display = 'flex';
                return false;
            }
            errorBox.style.display = 'none';
            return true;
        }
    </script>
</body>

</html>