<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mâu Bakery</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Main Admin Style -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <i class="fas fa-birthday-cake"></i> Mâu Bakery Admin
        </div>

        <nav class="menu">
            <?php
            // Helper function to check permission
            function has_menu_permission($page) {
                if ($_SESSION['role'] === 'admin') return true;
                if ($_SESSION['role'] !== 'staff') return false;
                
                // Pages allowed for all staff
                $always_allowed = ['index.php', 'reset_password.php', 'support.php'];
                if (in_array($page, $always_allowed)) return true;
                
                // Check DB for other pages
                global $conn;
                if (!isset($conn)) {
                    require_once __DIR__ . '/../../config/db.php';
                }
                $stmt = $conn->prepare("SELECT COUNT(*) FROM staff_permissions WHERE user_id = ? AND permission_key = ? AND is_granted = 1");
                $stmt->execute([$_SESSION['user_id'], $page]);
                return $stmt->fetchColumn() > 0;
            }
            ?>

            <?php if (has_menu_permission('index.php')): ?>
            <a href="index.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('statistics.php')): ?>
            <a href="statistics.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'statistics.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i> Thống Kê
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('orders.php')): ?>
            <a href="orders.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Đơn Hàng
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('vouchers.php')): ?>
            <a href="vouchers.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'vouchers.php') ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i> Mã Giảm Giá
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('support.php')): ?>
            <a href="support.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'support.php') ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i> Hỗ Trợ <span id="chat-badge-admin" class="badge bg-danger" style="display:none; margin-left: 5px;">0</span>
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('products.php')): ?>
            <a href="products.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i> Sản Phẩm
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('categories.php')): ?>
            <a href="categories.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Danh Mục
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('tags.php')): ?>
            <a href="tags.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'tags.php') ? 'active' : ''; ?>">
                <i class="fas fa-lightbulb"></i> Gợi ý (Tags)
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('shipping_zones.php')): ?>
            <a href="shipping_zones.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'shipping_zones.php') ? 'active' : ''; ?>">
                <i class="fas fa-truck"></i> Khu vực Ship
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('users.php')): ?>
            <a href="users.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Khách Hàng
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('media.php')): ?>
            <a href="media.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'media.php') ? 'active' : ''; ?>">
                <i class="fas fa-images"></i> Thư Viện Ảnh
            </a>
            <?php endif; ?>

            <?php if (has_menu_permission('settings.php')): ?>
            <a href="settings.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> Cài Đặt
            </a>
            <?php endif; ?>
        </nav>

        <div class="user-panel">
            <div class="user-name-display">
                <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin'; ?>
            </div>
            <a href="../auth/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </aside>

    <!-- Main Wrapper Start -->
    <main class="main-content">
        <!-- Header could go here -->