<?php
/**
 * API for AI Chat (Separate Customer vs Admin Flow)
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
    echo json_encode(['error' => 'Tin nhắn không được để trống']);
    exit;
}

$ai = new AIHelper($conn);

// Determine which flow to use
if ($role === 'admin' || $role === 'staff') {
    $response = $ai->handleAdminChat($message);
} else {
    $response = $ai->handleCustomerChat($message);
}

echo json_encode(['response' => $response]);
?>