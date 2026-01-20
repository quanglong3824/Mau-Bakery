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
    $district_code = isset($_POST['district']) ? $_POST['district'] : '';
    $address_specific = isset($_POST['address_specific']) ? trim($_POST['address_specific']) : '';

    // Define Fees logic (Same as View for validation)
    $districts_data = [
        'Q1' => ['name' => 'Quận 1', 'fee' => 15000],
        'Q3' => ['name' => 'Quận 3', 'fee' => 15000],
        'Q4' => ['name' => 'Quận 4', 'fee' => 15000],
        'Q5' => ['name' => 'Quận 5', 'fee' => 15000],
        'Q10' => ['name' => 'Quận 10', 'fee' => 15000],
        'BINHTHANH' => ['name' => 'Quận Bình Thạnh', 'fee' => 15000],
        'PHUNHUAN' => ['name' => 'Quận Phú Nhuận', 'fee' => 15000],

        'Q6' => ['name' => 'Quận 6', 'fee' => 30000],
        'Q7' => ['name' => 'Quận 7', 'fee' => 30000],
        'Q8' => ['name' => 'Quận 8', 'fee' => 30000],
        'Q11' => ['name' => 'Quận 11', 'fee' => 30000],
        'TANBINH' => ['name' => 'Quận Tân Bình', 'fee' => 30000],
        'GOVAP' => ['name' => 'Quận Gò Vấp', 'fee' => 30000],
        'TANPHU' => ['name' => 'Quận Tân Phú', 'fee' => 30000],

        'Q12' => ['name' => 'Quận 12', 'fee' => 50000],
        'BINHTAN' => ['name' => 'Quận Bình Tân', 'fee' => 50000],
        'THUDUC' => ['name' => 'TP. Thủ Đức', 'fee' => 50000],

        'BINHCHANH' => ['name' => 'Huyện Bình Chánh', 'fee' => 60000],
        'HOCMON' => ['name' => 'Huyện Hóc Môn', 'fee' => 60000],
        'NHABE' => ['name' => 'Huyện Nhà Bè', 'fee' => 60000],
        'CUCHI' => ['name' => 'Huyện Củ Chi', 'fee' => 70000],
        'CANGIO' => ['name' => 'Huyện Cần Giờ', 'fee' => 100000],
    ];

    // Validate District and Get Fee
    $shipping_fee = 0;
    $district_name = '';

    if (array_key_exists($district_code, $districts_data)) {
        $shipping_fee = $districts_data[$district_code]['fee'];
        $district_name = $districts_data[$district_code]['name'];
    } else {
        // Fallback for invalid district
        $shipping_fee = 30000;
        $district_name = $district_code;
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