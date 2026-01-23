<?php
// api/toggle_favorite.php
header('Content-Type: application/json');
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện chức năng này.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = :uid AND product_id = :pid");
    $stmt->execute(['uid' => $user_id, 'pid' => $product_id]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        // Remove
        $stmt_del = $conn->prepare("DELETE FROM favorites WHERE id = :id");
        $stmt_del->execute(['id' => $existing]);
        $action = 'removed';
    } else {
        // Add
        $stmt_add = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (:uid, :pid)");
        $stmt_add->execute(['uid' => $user_id, 'pid' => $product_id]);
        $action = 'added';
    }

    echo json_encode(['success' => true, 'action' => $action]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
