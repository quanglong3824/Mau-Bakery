<?php
// controllers/ContactController.php

$message = '';
$error = '';

// Pre-fill data if user is logged in
$contact_name = '';
$contact_email = '';
$is_readonly = '';

if (isset($_SESSION['user_id'])) {
    // User is logged in, use session data (or fetch user if needed, but session usually has name/email)
    $contact_name = $_SESSION['full_name'] ?? $_SESSION['username'];
    // Email might not be in session depending on login logic, but usually is. 
    // If not, we might need to fetch it.
    // Let's assume it IS in session or fetch it if missing.
    // Ideally we check if we have it. If not, fetch.

    if (isset($_SESSION['email'])) {
        $contact_email = $_SESSION['email'];
    } else {
        // Fetch from DB if conn exists
        if (isset($conn)) {
            $stmt = $conn->prepare("SELECT email FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $u = $stmt->fetch();
            if ($u)
                $contact_email = $u['email'];
        }
    }
    $is_readonly = 'readonly style="background-color: #f9f9f9; cursor: not-allowed;"';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject'] ?? '');
    $raw_msg = trim($_POST['message']);

    // Combine subject and message
    $msg = "Tiêu đề: $subject\n\nNội dung: $raw_msg";

    // Basic Validation
    if (empty($name) || empty($email) || empty($raw_msg)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ.";
    } else {
        // Insert into DB
        if (isset($conn)) {
            try {
                $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (:name, :email, :msg)");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'msg' => $msg
                ]);
                $message = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";

                // Clear form if guest (logged in user fields stay populated)
                if (!isset($_SESSION['user_id'])) {
                    $contact_name = '';
                    $contact_email = '';
                }
            } catch (PDOException $e) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        } else {
            $error = "Lỗi kết nối cơ sở dữ liệu.";
        }
    }
}
?>