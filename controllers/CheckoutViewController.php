<?php
// controllers/CheckoutViewController.php

// Ensure cart exists and is not empty
if (empty($_SESSION['cart'])) {
    echo "<script>window.location.href='index.php?page=cart';</script>";
    exit;
}

$cart_items = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$default_shipping = 0;
$total = $subtotal + $default_shipping;

// User Data for Pre-filling
$user_name = '';
$user_phone = '';
$user_email = '';
$user_address = '';

if (isset($_SESSION['user_id'])) {
    $user_name = $_SESSION['full_name'] ?? '';
    // Fetch more details if needed from DB, but session is faster if available
    if (isset($conn)) {
        $stmt = $conn->prepare("SELECT email, phone FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $u = $stmt->fetch();
        if ($u) {
            $user_email = $u['email'];
            $user_phone = $u['phone'];
        }

        // Fetch default address
        $stmt_addr = $conn->prepare("SELECT recipient_name, phone, address FROM user_addresses WHERE user_id = :id AND is_default = 1");
        $stmt_addr->execute(['id' => $_SESSION['user_id']]);
        $addr = $stmt_addr->fetch();
        if ($addr) {
            $user_name = $addr['recipient_name'];
            $user_phone = $addr['phone'];
            $user_address = $addr['address'];
        }
    }
}
?>