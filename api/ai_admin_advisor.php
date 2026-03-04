<?php
/**
 * API for Admin AI Advisor
 */
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/AIHelper.php';

header('Content-Type: application/json');

try {
    // Check if user is admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $message = isset($input['message']) ? trim($input['message']) : '';

    if (empty($message)) {
        echo json_encode(['error' => 'Tin nhắn không được để trống']);
        exit;
    }

    $ai = new AIHelper($conn);
    // Use the admin-specific chat handler
    $response = $ai->handleAdminChat($message);

    if (!$response) {
        echo json_encode(['error' => 'AI không trả về kết quả']);
    } else {
        echo json_encode(['response' => $response]);
    }
} catch (Exception $e) {
    error_log("AI Admin Error: " . $e->getMessage());
    echo json_encode(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
}
?>