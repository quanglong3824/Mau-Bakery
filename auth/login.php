<?php
require_once 'controllers/AuthController.php';

// Initialize Controller
$auth = new AuthController($conn);
$auth->handleLogin();

// Shortcuts for View
$error = $auth->error;
$username = $auth->username;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Mâu Bakery</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="login-container">
        <div class="logo-area">
            <i class="fas fa-birthday-cake"></i> Mâu Bakery
        </div>
        <h3 style="text-align: center; margin-bottom: 20px; color: #444;">Đăng Nhập</h3>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Tên đăng nhập hoặc Email"
                    value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Mật khẩu" required>
            </div>

            <div style="text-align: right; font-size: 0.85rem; margin-bottom: 20px;">
                <a href="forgot_password.php" style="color: #888; text-decoration: none;">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-login">Đăng Nhập</button>
        </form>

        <div class="links">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a><br>
            <a href="../index.php" class="home-link">
                <i class="fas fa-arrow-left"></i> Về trang chủ
            </a>
        </div>
    </div>

</body>

</html>