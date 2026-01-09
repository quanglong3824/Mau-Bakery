<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';
$full_name = '';
$email = '';
$phone = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']); // Usually auto-generated or same as email, but let's allow custom for now
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // If username is not in form input (assuming standard form didn't have it), let's use part of email
    if (empty($username) && !empty($email)) {
        $parts = explode('@', $email);
        $username = $parts[0] . rand(100, 999);
    }

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
    } elseif ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        if (isset($conn)) {
            try {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);

                if ($stmt->rowCount() > 0) {
                    $error = "Email này đã được đăng ký.";
                } else {
                    // Check username uniqueness
                    $stmt_u = $conn->prepare("SELECT id FROM users WHERE username = :username");
                    $stmt_u->execute(['username' => $username]);

                    if ($stmt_u->rowCount() > 0) {
                        $error = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.";
                    } else {
                        // Insert new user
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (username, password, email, full_name, phone, role, is_active) VALUES (:username, :password, :email, :full_name, :phone, 'user', 1)";
                        $insert_stmt = $conn->prepare($sql);
                        $insert_stmt->execute([
                            'username' => $username,
                            'password' => $hashed_password,
                            'email' => $email,
                            'full_name' => $full_name,
                            'phone' => $phone
                        ]);

                        $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
                        // Clear form
                        $full_name = $email = $phone = $username = '';
                    }
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
    <title>Đăng Ký - Mâu Bakery</title>
    <!-- Use same CSS as Login -->
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
            max-width: 450px;
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

        .success-msg {
            color: #27ae60;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 15px;
            background: rgba(39, 174, 96, 0.1);
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
            <script>setTimeout(function () { window.location.href = 'login.php'; }, 2000);</script>
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
            <a href="../index.php" style="color: #666; font-size: 0.8rem; display: inline-block; margin-top: 10px;">
                <i class="fas fa-arrow-left"></i> Về trang chủ
            </a>
        </div>
    </div>

</body>

</html>