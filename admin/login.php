<?php
// admin/login.php
require_once 'controllers/LoginController.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản Trị - Mâu Bakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div class="login-card">
        <div class="brand-logo">
            <i class="fas fa-crown"></i> Admin Panel
        </div>

        <?php if ($error): ?>
            <div class="error-msg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-input" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-input" placeholder="••••••" required>
            </div>
            <button type="submit" class="btn-submit">Đăng Nhập</button>
        </form>

        <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Về trang chủ</a>
    </div>
</body>

</html>