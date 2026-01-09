<?php
// controllers/OrderController.php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'place_order') {

    // Check if cart is empty
    if (empty($_SESSION['cart'])) {
        header("Location: index.php?page=cart");
        exit;
    }

    $recipient_name = trim($_POST['fullname']);
    $recipient_phone = trim($_POST['phone']);
    $shipping_address = trim($_POST['address']);
    $email = trim($_POST['email']); // Optional
    $note = trim($_POST['note']);
    $payment_method = $_POST['payment_method'];
    $shipping_method = $_POST['shipping_method'];

    // Calculate Totals
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $shipping_fee = ($shipping_method === 'express') ? 50000 : 30000;
    $total_amount = $subtotal + $shipping_fee;

    // Generate Order Code
    $order_code = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    try {
        $conn->beginTransaction();

        // 1. Insert into orders
        $sql = "INSERT INTO orders (user_id, order_code, total_amount, shipping_fee, payment_method, status, recipient_name, recipient_phone, shipping_address, note) 
                VALUES (:user_id, :order_code, :total_amount, :shipping_fee, :payment_method, 'pending', :recipient_name, :recipient_phone, :shipping_address, :note)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'order_code' => $order_code,
            'total_amount' => $total_amount,
            'shipping_fee' => $shipping_fee,
            'payment_method' => $payment_method,
            'recipient_name' => $recipient_name,
            'recipient_phone' => $recipient_phone,
            'shipping_address' => $shipping_address,
            'note' => $note
        ]);

        $order_id = $conn->lastInsertId();

        // 2. Insert into order_items
        $sql_item = "INSERT INTO order_items (order_id, product_id, product_name, size, price, quantity) 
                     VALUES (:order_id, :product_id, :product_name, :size, :price, :quantity)";
        $stmt_item = $conn->prepare($sql_item);

        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->execute([
                'order_id' => $order_id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'size' => isset($item['size']) ? $item['size'] : 'Standard',
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }

        $conn->commit();

        // 3. Clear Cart
        unset($_SESSION['cart']);

        // 4. Redirect
        header("Location: index.php?page=order_success&order_id=" . $order_code);
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Lỗi xử lý đơn hàng: " . $e->getMessage();
        // Ideally redirect to detailed error page
        exit;
    }
}
?>