<?php
// admin/controllers/OrderManagerController.php

require_once 'includes/auth_check.php';
require_once 'includes/functions.php';
require_once '../config/db.php';

// Pagination settings
$limit = 10;
$current_page = isset($_GET['page_no']) ? intval($_GET['page_no']) : 1;

// Handle Action (Change Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];

    $valid_statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        try {
            $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $new_status, 'id' => $order_id]);
            $msg = "Cập nhật trạng thái đơn hàng thành công!";
        } catch (PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Fetch Total Records for Pagination
$stmt_count = $conn->query("SELECT COUNT(*) FROM orders");
$total_records = $stmt_count->fetchColumn();

// Get Pagination parameters
$pagin = get_pagination_params($total_records, $current_page, $limit);
$offset = $pagin['offset'];
$total_pages = $pagin['total_pages'];
$current_page = $pagin['current_page'];

// Fetch Orders with LIMIT & OFFSET
$stmt = $conn->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();
?>