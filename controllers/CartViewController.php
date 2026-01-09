<?php
// controllers/CartViewController.php

// Ensure cart session exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart_items as $item) {
    if (isset($item['price']) && isset($item['quantity'])) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
$shipping = 30000;
$total = $subtotal + $shipping;

$is_logged_in = isset($_SESSION['user_id']);
?>