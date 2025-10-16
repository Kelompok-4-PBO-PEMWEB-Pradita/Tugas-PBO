<?php
// file: proses_login.php
session_start();

include 'koneksi.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        header("Location: dashboard.php");
        $stmt->close();
        $koneksi->close();
        exit();
    } else {
        header("Location: login.php?error=Email atau password salah!");
        $stmt->close();
        $koneksi->close();
        exit();
    }
} else {
    header("Location: login.php?error=Email atau password salah!");
    $stmt->close();
    $koneksi->close();
    exit();
}
?>