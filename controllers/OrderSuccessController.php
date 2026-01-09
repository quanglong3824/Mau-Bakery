<?php
// controllers/OrderSuccessController.php

$order_code = isset($_GET['order_id']) ? $_GET['order_id'] : '';

// Init variables
$order = null;
$order_items = [];

if ($order_code && isset($conn)) {
    // 1. Fetch Order
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = :code");
    $stmt->execute(['code' => $order_code]);
    $order = $stmt->fetch();

    if ($order) {
        // 2. Fetch Items
        $stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = :id");
        $stmt_items->execute(['id' => $order['id']]);
        $order_items = $stmt_items->fetchAll();
    }
}
?>