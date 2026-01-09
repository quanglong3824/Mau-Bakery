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

// 2. Check if user has 'admin' role
// We assume 'role' is stored in session during login. 
// If not, we might need to fetch from DB, but for performance, storing in session is better.
// Let's verify how login handles this. If login doesn't store role, we add a check here.

require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Double check with DB to be sure (in case session is stale/spoofed conceptually, though session server side is safe)
    // For now, trusting session is standard.

    // If not admin, redirect to user home or show 403
    echo "<h1>403 Forbidden</h1><p>Bạn không có quyền truy cập trang này.</p><a href='../index.php'>Về trang chủ</a>";
    exit;
}