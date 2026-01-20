<?php
// auth/controllers/AuthController.php

require_once '../config/db.php';
session_start();

class AuthController
{

    public $error = '';
    public $success = '';

    // Form data holders
    public $username = '';
    public $full_name = '';
    public $email = '';
    public $phone = '';

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Handle Login Logic
     */
    public function handleLogin()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($this->username) || empty($password)) {
                $this->error = "Vui lòng nhập tên đăng nhập và mật khẩu.";
                return;
            }

            try {
                // Check user exists
                $stmt = $this->conn->prepare("SELECT id, username, password, full_name, role, is_active FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username' => $this->username, 'email' => $this->username]);
                $user = $stmt->fetch();

                if ($user) {
                    if ($user['is_active'] == 0) {
                        $this->error = "Tài khoản của bạn đã bị khóa.";
                    } elseif (password_verify($password, $user['password'])) {
                        // Success
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['role'] = $user['role'];

                        // Redirect
                        if ($user['role'] === 'admin') {
                            header("Location: ../admin/index.php");
                        } else {
                            header("Location: ../index.php");
                        }
                        exit;
                    } else {
                        $this->error = "Mật khẩu không chính xác.";
                    }
                } else {
                    $this->error = "Tài khoản không tồn tại.";
                }
            } catch (PDOException $e) {
                $this->error = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }

    /**
     * Handle Register Logic
     */
    public function handleRegister()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->full_name = trim($_POST['full_name']);
            $this->email = trim($_POST['email']);
            $this->phone = trim($_POST['phone']);
            $this->username = trim($_POST['username']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Auto-generate username if missing
            if (empty($this->username) && !empty($this->email)) {
                $parts = explode('@', $this->email);
                $this->username = $parts[0] . rand(100, 999);
            }

            // Validation
            if (empty($this->full_name) || empty($this->email) || empty($password)) {
                $this->error = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
            } elseif ($password !== $confirm_password) {
                $this->error = "Mật khẩu xác nhận không khớp.";
            } elseif (strlen($password) < 6) {
                $this->error = "Mật khẩu phải có ít nhất 6 ký tự.";
            } else {
                try {
                    // Check email existence
                    $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
                    $stmt->execute(['email' => $this->email]);

                    if ($stmt->rowCount() > 0) {
                        $this->error = "Email này đã được đăng ký.";
                    } else {
                        // Check username existence
                        $stmt_u = $this->conn->prepare("SELECT id FROM users WHERE username = :username");
                        $stmt_u->execute(['username' => $this->username]);

                        if ($stmt_u->rowCount() > 0) {
                            $this->error = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.";
                        } else {
                            // Insert
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $sql = "INSERT INTO users (username, password, email, full_name, phone, role, is_active) VALUES (:username, :password, :email, :full_name, :phone, 'user', 1)";
                            $insert = $this->conn->prepare($sql);
                            $insert->execute([
                                'username' => $this->username,
                                'password' => $hashed_password,
                                'email' => $this->email,
                                'full_name' => $this->full_name,
                                'phone' => $this->phone
                            ]);

                            $this->success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
                            // Clear sensitive data
                            $this->username = $this->full_name = $this->email = $this->phone = '';
                        }
                    }
                } catch (PDOException $e) {
                    $this->error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }
    }
}
?>