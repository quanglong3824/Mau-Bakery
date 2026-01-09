<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin' AND is_active = 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role']; // Critical for auth_check

                header("Location: index.php");
                exit;
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng (hoặc bạn không phải Admin)!';
            }
        } catch (PDOException $e) {
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản Trị - Mâu Bakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #4a4a4a;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .brand-logo {
            font-size: 2rem;
            color: #b19cd9;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #b19cd9;
            box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #b19cd9;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #9b85c1;
        }

        .error-msg {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #888;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .back-link:hover {
            color: #b19cd9;
        }
    </style>
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