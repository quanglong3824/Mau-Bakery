<?php
require_once 'controllers/ResetPasswordController.php';

if ($message === "SUCCESS") {
    echo "<h1>Đã reset mật khẩu Admin thành công!</h1>";
    echo "<p>Username: <b>admin</b></p>";
    echo "<p>Password: <b>123456</b></p>";
    echo "<p>Hash mới: " . $hash . "</p>";
    echo "<a href='login.php'>Quay lại trang đăng nhập</a>";
} else {
    echo $message;
}
?>