<?php
// admin/controllers/OrderDetailController.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    header('Location: orders.php');
    exit;
}

// Fetch Order Info
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Fetch Order Items
$stmt = $conn->prepare("SELECT oi.*, p.image 
                        FROM order_items oi 
                        LEFT JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$items = $stmt->fetchAll();

// Handle Status Update in Detail Page (optional but good)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $new_status = $_POST['new_status'];
    $valid_statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $new_status, 'id' => $order_id]);
        $order['status'] = $new_status; // Update local variable for immediate display
        $msg = "Cập nhật trạng thái thành công!";
    }
}
?>