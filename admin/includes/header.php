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
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <i class="fas fa-birthday-cake"></i> Mâu Bakery Admin
        </div>

        <nav class="menu">
            <a href="index.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="orders.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Đơn Hàng
            </a>
            <a href="products.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i> Sản Phẩm
            </a>
            <a href="categories.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Danh Mục
            </a>
            <a href="tags.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'tags.php') ? 'active' : ''; ?>">
                <i class="fas fa-lightbulb"></i> Gợi ý (Tags)
            </a>
            <a href="users.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Khách Hàng
            </a>
            <a href="media.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'media.php') ? 'active' : ''; ?>">
                <i class="fas fa-images"></i> Thư Viện Ảnh
            </a>
            <a href="settings.php"
                class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> Cài Đặt
            </a>
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