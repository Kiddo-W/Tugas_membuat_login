<?php
session_start();

// Jika belum login, paksa kembali ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Utama</title>
</head>
<body>
    <h2>Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>Kamu berhasil masuk ke halaman utama.</p>
    <a href="logout.php">Keluar (Logout)</a>
</body>
</html>