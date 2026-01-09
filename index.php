<?php
session_start();

// Include Database Config (Will implement later)
require_once 'config/db.php';

// Routing Logic
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Lists of pages that do not need Header/Footer (Actions/Controllers)
$no_layout_pages = ['cart_action', 'process_checkout'];

// Include Header only if not an action page
if (!in_array($page, $no_layout_pages)) {
    include 'includes/header.php';
}

switch ($page) {
    case 'home':
        include 'views/home.php';
        break;
    case 'product_detail':
        include 'views/product_detail.php';
        break;
    case 'checkout':
        include 'views/checkout.php';
        break;
    case 'contact':
        include 'views/contact.php';
        break;
    case 'cart':
        include 'views/cart.php';
        break;
    case 'menu':
        include 'views/menu.php';
        break;
    case 'about':
        include 'views/about.php';
        break;
    case 'search':
        include 'views/search.php';
        break;
    case 'favorites':
        include 'views/favorites.php';
        break;
    case 'order_success':
        include 'views/order_success.php';
        break;
    case 'profile':
        include 'views/profile.php';
        break;
    case 'order_detail':
        include 'views/order_detail.php';
        break;
    case 'faq':
        include 'views/faq.php';
        break;
    case 'blog':
        include 'views/blog.php';
        break;
    case 'blog_detail':
        include 'views/blog_detail.php';
        break;
    case 'vouchers':
        include 'views/vouchers.php';
        break;
    case 'search':
        include 'views/search.php';
        break;
    case 'favorites':
        include 'views/favorites.php';
        break;

    // --- Controller Actions ---
    case 'cart_action':
        include 'controllers/CartController.php';
        break;
    case 'process_checkout':
        include 'controllers/OrderController.php';
        break;

    default:
        include 'views/home.php';
        break;
}

// Include Footer
// Include Footer only if not an action page
if (!in_array($page, $no_layout_pages)) {
    include 'includes/footer.php';
}
?>