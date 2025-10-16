<?php
// file: proses_register.php
include 'koneksi.php';

$username = $_POST['username'];
$email    = $_POST['email'];
$password = $_POST['password'];

$check_stmt = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    header("Location: register.php?error=Email sudah terdaftar. Silakan gunakan email lain.");
    exit();
}
$check_stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    echo "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
} else {
    header("Location: register.php?error=Terjadi kesalahan. Coba lagi.");
    exit();
}

$stmt->close();
$koneksi->close();
?>