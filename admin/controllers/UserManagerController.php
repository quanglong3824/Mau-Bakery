<?php
// admin/controllers/UserManagerController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';

$page_title = "Quản Lý Người Dùng";

// Handle Actions (Ban/Delete)
// Handle Actions (Ban/Delete/Create/Update)
$message = "";
$error_msg = "";

if (isset($_POST['action'])) {

    // 1. Toggle Status (Khóa/Mở)
    if ($_POST['action'] == 'toggle_status') {
        $id = intval($_POST['id']);
        $status = intval($_POST['status']); // 0 or 1
        $conn->prepare("UPDATE users SET is_active = :status WHERE id = :id")->execute(['status' => $status, 'id' => $id]);
        $message = "Đã cập nhật trạng thái người dùng.";
    }

    // 2. Create User
    elseif ($_POST['action'] == 'create_user') {
        $username = trim($_POST['username']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $phone = trim($_POST['phone']);
        $role = $_POST['role'];

        // Check duplicates
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            $error_msg = "Tên đăng nhập hoặc Email đã tồn tại!";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_pass, $full_name, $email, $phone, $role])) {
                $message = "Thêm người dùng mới thành công!";
            } else {
                $error_msg = "Lỗi khi thêm người dùng.";
            }
        }
    }

    // 3. Update User
    elseif ($_POST['action'] == 'update_user') {
        $id = intval($_POST['id']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $role = $_POST['role'];
        $password = trim($_POST['password']);

        // Check email duplication (excluding current user)
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);

        if ($check->rowCount() > 0) {
            $error_msg = "Email này đã được sử dụng bởi người dùng khác!";
        } else {
            if (!empty($password)) {
                // Update with password
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=?, password=? WHERE id=?");
                $res = $stmt->execute([$full_name, $email, $phone, $role, $hashed_pass, $id]);
            } else {
                // Update without password
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=? WHERE id=?");
                $res = $stmt->execute([$full_name, $email, $phone, $role, $id]);
            }
            $message = "Cập nhật thông tin thành công!";
        }
    }

    // 4. Delete User
    elseif ($_POST['action'] == 'delete_user') {
        $id = intval($_POST['id']);
        // Prevent deleting self (though logic in view handles this mostly)
        if ($id == $_SESSION['user_id']) {
            $error_msg = "Bạn không thể tự xóa chính mình!";
        } else {
            try {
                $conn->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $id]);
                $message = "Đã xóa người dùng thành công.";
            } catch (PDOException $e) {
                // Constraint violation (e.g., user is in orders)
                $error_msg = "Không thể xóa người dùng này vì họ đã có lịch sử giao dịch. Hãy chọn Khóa tài khoản thay vì Xóa.";
            }
        }
    }
}

// Fetch Users
$stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>