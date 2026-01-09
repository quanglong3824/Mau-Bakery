<?php
// admin/controllers/UserManagerController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';

$page_title = "Quản Lý Người Dùng";

// Handle Actions (Ban/Delete)
$message = "";
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'toggle_status') {
        $id = intval($_POST['id']);
        $status = intval($_POST['status']); // 0 or 1
        $conn->prepare("UPDATE users SET is_active = :status WHERE id = :id")->execute(['status' => $status, 'id' => $id]);
        $message = "Đã cập nhật trạng thái người dùng.";
    }
}

// Fetch Users
$stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>