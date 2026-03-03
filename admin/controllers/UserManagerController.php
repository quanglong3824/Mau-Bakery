<?php
// admin/controllers/UserManagerController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

$page_title = "Quản Lý Người Dùng";

// Pagination settings
$limit = 10;
$current_page = isset($_GET['page_no']) ? intval($_GET['page_no']) : 1;

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

        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);

        if ($check->rowCount() > 0) {
            $error_msg = "Email này đã được sử dụng bởi người dùng khác!";
        } else {
            if (!empty($password)) {
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=?, password=? WHERE id=?");
                $res = $stmt->execute([$full_name, $email, $phone, $role, $hashed_pass, $id]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=? WHERE id=?");
                $res = $stmt->execute([$full_name, $email, $phone, $role, $id]);
            }
            $message = "Cập nhật thông tin thành công!";
        }
    }

    // 4. Delete User
    elseif ($_POST['action'] == 'delete_user') {
        $id = intval($_POST['id']);
        if ($id == $_SESSION['user_id']) {
            $error_msg = "Bạn không thể tự xóa chính mình!";
        } else {
            try {
                $conn->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $id]);
                $message = "Đã xóa người dùng thành công.";
            } catch (PDOException $e) {
                $error_msg = "Không thể xóa người dùng này vì họ đã có lịch sử giao dịch. Hãy chọn Khóa tài khoản thay vì Xóa.";
            }
        }
    }

    // 5. Update Staff Permissions
    elseif ($_POST['action'] == 'update_permissions') {
        $user_id = intval($_POST['user_id']);
        $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
        
        // Clear existing permissions
        $conn->prepare("DELETE FROM staff_permissions WHERE user_id = ?")->execute([$user_id]);
        
        // Insert new permissions
        $stmt = $conn->prepare("INSERT INTO staff_permissions (user_id, permission_key, is_granted) VALUES (?, ?, 1)");
        foreach ($permissions as $key) {
            $stmt->execute([$user_id, $key]);
        }
        $message = "Đã cập nhật quyền hạn nhân viên.";
    }
}

// Fetch Staff Permissions if needed (e.g., for a specific user)
function get_user_permissions($user_id, $conn) {
    $stmt = $conn->prepare("SELECT permission_key FROM staff_permissions WHERE user_id = ? AND is_granted = 1");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch Total Records for Pagination
$stmt_count = $conn->query("SELECT COUNT(*) FROM users");
$total_records = $stmt_count->fetchColumn();

// Get Pagination parameters
$pagin = get_pagination_params($total_records, $current_page, $limit);
$offset = $pagin['offset'];
$total_pages = $pagin['total_pages'];
$current_page = $pagin['current_page'];

// Fetch Users with LIMIT & OFFSET
$stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
?>