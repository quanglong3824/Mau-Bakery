<?php
/**
 * API for AI Chat (Alibaba DashScope / OpenAI Protocol)
 */
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/AIHelper.php';

header('Content-Type: application/json');

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim($input['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Message is empty']);
    exit;
}

$ai = new AIHelper($conn);

// Determine context based on role
if ($role === 'admin' || $role === 'staff') {
    $context = $ai->getAdminContext();
} else {
    $context = $ai->getCustomerContext($message);
}

// Generate AI response
$response = $ai->generateContent($message, $context);

echo json_encode(['response' => $response]);
?>