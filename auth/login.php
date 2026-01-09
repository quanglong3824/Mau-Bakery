<?php
session_start();
require_once '../config/db.php';

$error = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập tên đăng nhập và mật khẩu.";
    } else {
        if (isset($conn)) {
            try {
                // Check user exists
                $stmt = $conn->prepare("SELECT id, username, password, full_name, role, is_active FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username' => $username, 'email' => $username]);
                $user = $stmt->fetch();

                if ($user) {
                    if ($user['is_active'] == 0) {
                        $error = "Tài khoản của bạn đã bị khóa.";
                    } elseif (password_verify($password, $user['password'])) {
                        // Password correct - Security: Regenerate Session ID
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['role'] = $user['role'];

                        // Redirect based on role or to home
                        if ($user['role'] === 'admin') {
                            header("Location: ../admin/dashboard.php"); // Assuming admin path
                        } else {
                            header("Location: ../index.php");
                        }
                        exit;
                    } else {
                        $error = "Mật khẩu không chính xác.";
                    }
                } else {
                    $error = "Tài khoản không tồn tại.";
                }
            } catch (PDOException $e) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        } else {
            $error = "Lỗi kết nối cơ sở dữ liệu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Mâu Bakery</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .logo-area {
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Quicksand', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .form-group input:focus {
            border-color: var(--accent-color);
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1rem;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #9b85c1;
        }

        .error-msg {
            color: #e74c3c;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 15px;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            border-radius: 5px;
        }

        .links {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .links a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
    </style>
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
            <a href="../index.php" style="color: #666; font-size: 0.8rem; display: inline-block; margin-top: 10px;">
                <i class="fas fa-arrow-left"></i> Về trang chủ
            </a>
        </div>
    </div>

</body>

</html>