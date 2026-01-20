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
    $email = trim($_POST['email']); // Optional
    $note = trim($_POST['note']);
    $payment_method = $_POST['payment_method'];

    // Handle Address Mapping
    $city = isset($_POST['city']) ? trim($_POST['city']) : 'TP. Hồ Chí Minh';
    $district_id = isset($_POST['district']) ? $_POST['district'] : 0;
    $address_specific = isset($_POST['address_specific']) ? trim($_POST['address_specific']) : '';
    
    // Validate District and Get Fee from Database
    $shipping_fee = 0;
    $district_name = '';

    try {
        $stmt_zone = $conn->prepare("SELECT name, fee FROM shipping_zones WHERE id = :id AND is_active = 1");
        $stmt_zone->execute(['id' => $district_id]);
        $zone = $stmt_zone->fetch(PDO::FETCH_ASSOC);

        if ($zone) {
            $shipping_fee = $zone['fee'];
            $district_name = $zone['name'];
        } else {
            // Fallback for invalid district ID (hack attempt?)
            $shipping_fee = 30000; 
            $district_name = "Khu vực không xác định ($district_id)";
        }
    } catch (PDOException $e) {
        $shipping_fee = 30000;
        $district_name = "Lỗi xác định khu vực";
    }

    // Compose Full Address for DB
    $shipping_address = $address_specific . ", " . $district_name . ", " . $city;

    // Calculate Totals
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

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