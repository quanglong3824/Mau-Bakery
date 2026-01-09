<?php
// controllers/CartController.php

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Helper function to get cart total
function get_cart_total()
{
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Helper function to get cart count
function get_cart_count()
{
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Handle Add to Cart
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $image = $_POST['image'];
    $quantity = intval($_POST['quantity']);
    $size = isset($_POST['size']) ? $_POST['size'] : null; // Handle size if applicable

    // Check if product already exists in cart (with same size)
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $product_id && $item['size'] === $size) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'quantity' => $quantity,
            'size' => $size
        ];
    }

    // Return JSON response
    echo json_encode([
        'status' => 'success',
        'message' => 'Đã thêm vào giỏ hàng!',
        'cart_count' => get_cart_count()
    ]);
    exit;
}

// Handle Update Cart Quantity
if (isset($_POST['action']) && $_POST['action'] === 'update_cart') {
    $index = intval($_POST['index']);
    $quantity = intval($_POST['quantity']);

    if (isset($_SESSION['cart'][$index])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
        } else {
            // If quantity is 0 or less, maybe remove or keep at 1? Let's remove if 0.
            array_splice($_SESSION['cart'], $index, 1);
        }
    }

    echo json_encode([
        'status' => 'success',
        'cart_total' => number_format(get_cart_total(), 0, ',', '.') . 'đ',
        'item_total' => isset($_SESSION['cart'][$index]) ? number_format($_SESSION['cart'][$index]['price'] * $_SESSION['cart'][$index]['quantity'], 0, ',', '.') . 'đ' : 0
    ]);
    exit;
}

// Handle Remove Item
if (isset($_POST['action']) && $_POST['action'] === 'remove_from_cart') {
    $index = intval($_POST['index']);

    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
    ]);
    exit;
}
?>