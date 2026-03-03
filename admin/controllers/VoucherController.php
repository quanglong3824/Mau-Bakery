<?php
// admin/controllers/VoucherController.php

require_once 'includes/auth_check.php';
require_once 'includes/functions.php';
require_once '../config/db.php';

// Auto-fix: Ensure created_at exists (prevents SQL error)
$conn->exec("ALTER TABLE coupons ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;");

$limit = 10;
$current_page = isset($_GET['page_no']) ? intval($_GET['page_no']) : 1;

// Handle Add Voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $code = strtoupper(trim($_POST['code']));
    $discount_value = floatval($_POST['discount_value']);
    $discount_type = $_POST['discount_type'];
    $min_order = floatval($_POST['min_order']);
    $expiry_date = $_POST['expiry_date'];
    $quantity = intval($_POST['quantity']);

    try {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_value, discount_type, min_order, expiry_date, quantity) 
                                VALUES (:code, :value, :type, :min, :expiry, :qty)");
        $stmt->execute([
            'code' => $code,
            'value' => $discount_value,
            'type' => $discount_type,
            'min' => $min_order,
            'expiry' => $expiry_date,
            'qty' => $quantity
        ]);
        $msg = "Thêm mã giảm giá $code thành công!";
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Handle Delete/Toggle
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $conn->query("UPDATE coupons SET is_active = 1 - is_active WHERE id = $id");
    header('Location: vouchers.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM coupons WHERE id = $id");
    header('Location: vouchers.php');
    exit;
}

// Pagination
$stmt_count = $conn->query("SELECT COUNT(*) FROM coupons");
$total_records = $stmt_count->fetchColumn();
$pagin = get_pagination_params($total_records, $current_page, $limit);
$offset = $pagin['offset'];
$total_pages = $pagin['total_pages'];
$current_page = $pagin['current_page'];

// Fetch Vouchers
$stmt = $conn->prepare("SELECT * FROM coupons ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$vouchers = $stmt->fetchAll();
?>