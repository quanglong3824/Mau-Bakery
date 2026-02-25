<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $code = isset($data['code']) ? trim($data['code']) : '';
    $subtotal = isset($data['subtotal']) ? floatval($data['subtotal']) : 0;

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = :code AND is_active = 1");
        $stmt->execute(['code' => $code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ.']);
            exit;
        }

        if (strtotime($coupon['expiry_date']) < time()) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết hạn.']);
            exit;
        }

        if ($coupon['quantity'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
            exit;
        }

        if ($subtotal < $coupon['min_order']) {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu (' . number_format($coupon['min_order'], 0, ',', '.') . 'đ).']);
            exit;
        }

        $discount_amount = 0;
        if ($coupon['discount_type'] == 'percent') {
            $discount_amount = $subtotal * ($coupon['discount_value'] / 100);
        } else {
            $discount_amount = $coupon['discount_value'];
        }

        // Cap discount if it exceeds subtotal
        if ($discount_amount > $subtotal) {
            $discount_amount = $subtotal;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng thành công!',
            'discount_amount' => $discount_amount,
            'coupon_code' => $coupon['code']
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>