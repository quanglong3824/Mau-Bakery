<?php
// controllers/ProfileController.php

// Ensure User is Logged In
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = [];
$orders = [];
$addresses = [];

if (isset($conn)) {
    try {
        // 1. Fetch User Details
        $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt_user->execute(['id' => $user_id]);
        $user = $stmt_user->fetch();

        if (!$user) {
            // User ID in session but not in DB? Weird edge case.
            session_destroy();
            header("Location: auth/login.php");
            exit;
        }

        // 2. Fetch Order History
        $stmt_orders = $conn->prepare("
            SELECT * FROM orders 
            WHERE user_id = :uid 
            ORDER BY created_at DESC
        ");
        $stmt_orders->execute(['uid' => $user_id]);
        $orders = $stmt_orders->fetchAll();

        // 3. Fetch Addresses
        $stmt_addr = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :uid ORDER BY is_default DESC, id DESC");
        $stmt_addr->execute(['uid' => $user_id]);
        $addresses = $stmt_addr->fetchAll();

        // 4. Fetch Contact History
        $contacts_history = [];
        // Assuming created_at exists, if not we might default to id desc
        $stmt_contacts = $conn->prepare("SELECT * FROM contacts WHERE email = :email ORDER BY id DESC");
        $stmt_contacts->execute(['email' => $user['email']]);
        $contacts_history = $stmt_contacts->fetchAll();

    } catch (PDOException $e) {
        $error = "Lỗi kết nối: " . $e->getMessage();
    }
}


// Handle POST Requests
$message = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        // DOB and Gender could be added here if DB supports

        try {
            $stmt_update = $conn->prepare("UPDATE users SET full_name = :name, phone = :phone WHERE id = :id");
            $stmt_update->execute(['name' => $full_name, 'phone' => $phone, 'id' => $user_id]);

            // Update session if name changed
            $_SESSION['full_name'] = $full_name;
            $message = "Cập nhật thông tin thành công!";

            // Refresh user data
            $stmt_user->execute(['id' => $user_id]);
            $user = $stmt_user->fetch();

        } catch (PDOException $e) {
            $error_msg = "Lỗi cập nhật: " . $e->getMessage();
        }
    } elseif ($action === 'change_password') {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            $error_msg = "Mật khẩu xác nhận không khớp.";
        } elseif (strlen($new_pass) < 6) {
            $error_msg = "Mật khẩu mới phải từ 6 ký tự.";
        } else {
            // Verify current password
            if (password_verify($current_pass, $user['password'])) {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt_pw = $conn->prepare("UPDATE users SET password = :pass WHERE id = :id");
                $stmt_pw->execute(['pass' => $new_hash, 'id' => $user_id]);
                $message = "Đổi mật khẩu thành công!";
            } else {
                $error_msg = "Mật khẩu hiện tại không đúng.";
            }
        }
    } elseif ($action === 'add_address') {
        $recipient_name = trim($_POST['recipient_name']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $is_default = isset($_POST['is_default']) ? 1 : 0;

        try {
            if ($is_default) {
                // Reset other defaults
                $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :uid")->execute(['uid' => $user_id]);
            }

            $stmt_add = $conn->prepare("INSERT INTO user_addresses (user_id, recipient_name, phone, address, is_default) VALUES (:uid, :name, :phone, :addr, :def)");
            $stmt_add->execute([
                'uid' => $user_id,
                'name' => $recipient_name,
                'phone' => $phone,
                'addr' => $address,
                'def' => $is_default
            ]);

            $message = "Thêm địa chỉ mới thành công!";
            // Refresh addresses
            $stmt_addr->execute(['uid' => $user_id]);
            $addresses = $stmt_addr->fetchAll();

        } catch (PDOException $e) {
            $error_msg = "Lỗi thêm địa chỉ: " . $e->getMessage();
        }
    }
}
?>