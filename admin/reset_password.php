<?php
require_once __DIR__ . '/../config/db.php';

$new_password = '123456';
$hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("UPDATE users SET password = :pass WHERE username = 'admin'");
    $stmt->execute(['pass' => $hash]);
    echo "<h1>Đã reset mật khẩu Admin thành công!</h1>";
    echo "<p>Username: <b>admin</b></p>";
    echo "<p>Password: <b>123456</b></p>";
    echo "<p>Hash mới: " . $hash . "</p>";
    echo "<a href='login.php'>Quay lại trang đăng nhập</a>";
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>