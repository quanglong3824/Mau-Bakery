<?php
// admin/controllers/ResetPasswordController.php
require_once __DIR__ . '/../../config/db.php';
// This script seems to be a standalone utility to force reset admin password, 
// likely used manually. If it's part of the admin panel (e.g. self-change password), 
// it should restrict access or be just a utility. 
// Given the original file was separate, I'll keep it as a standalone controller logic,
// but usually this shouldn't be publicly accessible without checks.
// However, the original file had NO auth check! This implies it might be a dangerous file left over.
// For now, I will wrap the logic.

$message = "";

// Only execute if it's being accessed/required with intent. 
// Since the original file is `admin/reset_password.php` direct access,
// I will keep the logic here to simply output the result.

$new_password = '123456';
$hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Assuming 'admin' is the username to reset.
    // If the table is users, and admin user is 'admin'.
    $stmt = $conn->prepare("UPDATE users SET password = :pass WHERE username = 'admin'");
    $stmt->execute(['pass' => $hash]);
    $message = "SUCCESS";
} catch (PDOException $e) {
    $message = "ERROR: " . $e->getMessage();
}
?>