<?php
// file: dashboard.php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Anda berhasil login ke halaman dashboard.</p>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>