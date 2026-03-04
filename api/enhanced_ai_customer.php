<?php
/**
 * Enhanced API for Customer AI Chat with structured responses
 */
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/AIHelper.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = isset($input['message']) ? trim($input['message']) : '';
    $user_state = isset($input['state']) ? $input['state'] : 'initial'; // Track conversation state

    if (empty($message)) {
        echo json_encode(['error' => 'Tin nhắn không được để trống']);
        exit;
    }

    $ai = new AIHelper($conn);
    
    // Enhanced response that can include structured data
    $response_data = $ai->handleEnhancedCustomerChat($message, $user_state);

    if (!$response_data) {
        echo json_encode(['error' => 'AI không trả về kết quả']);
    } else {
        echo json_encode($response_data);
    }
} catch (Exception $e) {
    error_log("Enhanced AI Customer Error: " . $e->getMessage());
    echo json_encode(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
}
?>