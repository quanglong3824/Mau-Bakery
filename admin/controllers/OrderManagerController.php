<?php
// admin/controllers/OrderManagerController.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

// Handle Action (Change Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        try {
            $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $new_status, 'id' => $order_id]);
            $msg = "Cập nhật trạng thái đơn hàng #ORD-$order_id thành công!";
        } catch (PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Fetch Orders
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$orders = $conn->query($sql)->fetchAll();
?>