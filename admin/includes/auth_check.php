<?php
// admin/includes/auth_check.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Check if user has 'admin' or 'staff' role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    echo "<h1>403 Forbidden</h1><p>Bạn không có quyền truy cập trang quản trị.</p><a href='../index.php'>Về trang chủ</a>";
    exit;
}

// 3. If staff, check specific page permissions
if ($_SESSION['role'] === 'staff') {
    $current_page = basename($_SERVER['PHP_SELF']);

    // Pages that all staff can access
    $always_allowed = ['index.php', 'reset_password.php', 'support.php']; 

    if (!in_array($current_page, $always_allowed)) {
        require_once __DIR__ . '/../../config/db.php';
        $stmt = $conn->prepare("SELECT COUNT(*) FROM staff_permissions WHERE user_id = ? AND permission_key = ? AND is_granted = 1");
        $stmt->execute([$_SESSION['user_id'], $current_page]);
        $has_permission = $stmt->fetchColumn() > 0;

        if (!$has_permission) {
            echo "<h1>403 Forbidden</h1><p>Bạn không có quyền truy cập trang này: <b>$current_page</b>. Vui lòng liên hệ Admin.</p><a href='index.php'>Quay lại Dashboard</a>";
            exit;
        }
    }
}