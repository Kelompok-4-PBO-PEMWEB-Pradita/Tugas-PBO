<!DOCTYPE html>
<html>
<!-- file: login.php -->
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <form action="proses_login.php" method="POST">
            <h2>Login</h2>
            <?php if(isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </form>
    </div>
</body>
</html>