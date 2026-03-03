<?php
/**
 * API for Customer AI Chat (Public/Customer only)
 */
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/AIHelper.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim($input['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Tin nhắn không được để trống']);
    exit;
}

$ai = new AIHelper($conn);
// Luồng này CHỈ dành cho khách hàng
$response = $ai->handleCustomerChat($message);

echo json_encode(['response' => $response]);
?>