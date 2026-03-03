<?php
/**
 * API for Admin AI Chat (Admin/Staff only)
 */
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/AIHelper.php';

header('Content-Type: application/json');

// Check strictly for admin/staff role
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if ($role !== 'admin' && $role !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Bạn không có quyền truy cập AI Quản Trị']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim($input['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Tin nhắn không được để trống']);
    exit;
}

$ai = new AIHelper($conn);
$response = $ai->handleAdminChat($message);

echo json_encode(['response' => $response]);
?>