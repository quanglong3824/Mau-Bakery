<?php
require_once 'controllers/AuthController.php';

// Initialize Controller
$auth = new AuthController($conn);
$auth->handleRegister();

// Shortcuts for View
$error = $auth->error;
$success = $auth->success;
$username = $auth->username;
$full_name = $auth->full_name;
$email = $auth->email;
$phone = $auth->phone;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Mâu Bakery</title>
    <!-- Use same CSS as Login -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="login-container register-container">
        <div class="logo-area">
            <i class="fas fa-birthday-cake"></i> Mâu Bakery
        </div>
        <h3 style="text-align: center; margin-bottom: 20px; color: #444;">Đăng Ký Thành Viên</h3>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-msg"><i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <i class="fas fa-user-circle"></i>
                <input type="text" name="username" placeholder="Tên đăng nhập (Tùy chọn)"
                    value="<?php echo htmlspecialchars($username); ?>">
            </div>

            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" placeholder="Họ và tên"
                    value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>

            <div class="form-group">
                <i class="fas fa-at"></i>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>"
                    required>
            </div>

            <div class="form-group">
                <i class="fas fa-phone"></i>
                <input type="tel" name="phone" placeholder="Số điện thoại"
                    value="<?php echo htmlspecialchars($phone); ?>">
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Mật khẩu (Tối thiểu 6 ký tự)" required>
            </div>

            <div class="form-group">
                <i class="fas fa-check-circle"></i>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="btn-login">Đăng Ký Ngay</button>
        </form>

        <div class="links">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a><br>
            <a href="../index.php" class="home-link">
                <i class="fas fa-arrow-left"></i> Về trang chủ
            </a>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>

</html>