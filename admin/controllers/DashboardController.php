<?php
// admin/controllers/DashboardController.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

// Stats Init
$stats = [
    'orders' => 0,
    'revenue' => 0,
    'products' => 0,
    'users' => 0
];

if (isset($conn)) {
    // Today's orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
    $stats['orders'] = $stmt->fetchColumn();

    // Today's Revenue (Completed)
    $stmt = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed' AND DATE(created_at) = CURDATE()");
    $stats['revenue'] = $stmt->fetchColumn() ?: 0;

    // Total Products
    $stmt = $conn->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
    $stats['products'] = $stmt->fetchColumn();

    // Total Users
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $stats['users'] = $stmt->fetchColumn();

    // Recent Orders (for table)
    $stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>