<?php
// controllers/OrderDetailController.php

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='auth/login.php';</script>";
    exit;
}

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($product_id > 0 && $rating >= 1 && $rating <= 5) {
        try {
            $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (:uid, :pid, :rating, :comment)");
            $stmt->execute([
                'uid' => $user_id,
                'pid' => $product_id,
                'rating' => $rating,
                'comment' => $comment
            ]);
            $msg_review = "Cảm ơn bạn đã đánh giá sản phẩm!";
        } catch (PDOException $e) {
            $err_review = "Lỗi: " . $e->getMessage();
        }
    }
}

// Get Order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = null;

if (isset($conn) && $order_id > 0) {
    // 1. Fetch Order Data (Security: Check user_id matches session)
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $order_id, 'user_id' => $_SESSION['user_id']]);
    $order_data = $stmt->fetch();

    if ($order_data) {
        // 2. Fetch Order Items
        $stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = :id");
        $stmt_items->execute(['id' => $order_id]);
        $items = $stmt_items->fetchAll();

        // 3. Map to View Structure
        $order = [
            'id' => $order_data['id'],
            'code' => $order_data['order_code'],
            'date' => date('d/m/Y H:i', strtotime($order_data['created_at'])),
            'status' => $order_data['status'],
            'payment_method' => $order_data['payment_method'],
            'payment_status' => $order_data['payment_status'],
            'shipping_method' => $order_data['shipping_fee'] > 30000 ? 'Giao hàng hỏa tốc' : 'Giao hàng tiêu chuẩn',
            'customer' => [
                'name' => $order_data['recipient_name'],
                'phone' => $order_data['recipient_phone'],
                'address' => $order_data['shipping_address'],
                'note' => $order_data['note']
            ],
            'items' => [],
            'subtotal' => $order_data['total_amount'] - $order_data['shipping_fee'] + $order_data['discount_amount'], // approximate reverse calc
            'shipping_fee' => $order_data['shipping_fee'],
            'discount' => $order_data['discount_amount'],
            'total' => $order_data['total_amount']
        ];

        foreach ($items as $item) {
            // We might not have image here if not joined with products. 
            // Ideally we should join. Let's do a quick fetch or join in query.
            // For now let's assume valid product_id to fetch image.
            $img = 'assets/images/default-cake.jpg'; // Fallback
            $stmt_prod = $conn->prepare("SELECT image FROM products WHERE id = :pid");
            $stmt_prod->execute(['pid' => $item['product_id']]);
            $p = $stmt_prod->fetch();
            if ($p)
                $img = $p['image'];

            // Fetch Reviews by this user for these products to check if already reviewed
            $reviewed_products = [];
            $stmt_reviews = $conn->prepare("SELECT product_id FROM reviews WHERE user_id = :uid");
            $stmt_reviews->execute(['uid' => $_SESSION['user_id']]);
            $reviewed_products = $stmt_reviews->fetchAll(PDO::FETCH_COLUMN);

            $order['items'][] = [
                'product_id' => $item['product_id'], // Need this for rating
                'name' => $item['product_name'],
                'image' => $img,
                'size' => $item['size'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'is_reviewed' => in_array($item['product_id'], $reviewed_products)
            ];
        }
    }
}
?>