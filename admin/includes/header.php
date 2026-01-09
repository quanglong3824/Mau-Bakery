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

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-bg: #f3f4f6;
            --white: #ffffff;
            --text-dark: #1f2937;
            --accent-color: #b19cd9;
            --accent-hover: #9b85c1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }

        body {
            background-color: var(--primary-bg);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--white);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .brand {
            padding: 24px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .menu {
            padding: 20px;
            flex: 1;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #4b5563;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.2s;
            font-weight: 600;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: var(--primary-bg);
            color: var(--accent-color);
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        .user-panel {
            padding: 20px;
            border-top: 1px solid #f3f4f6;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
        }

        .logout-btn:hover {
            background: #fee2e2;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 30px;
        }

        .header-bar {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .btn-glass {
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-glass:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .table th {
            text-align: left;
            padding: 15px 20px;
            color: #6b7280;
            font-weight: 600;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            color: #374151;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Buttons & Badges */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-info {
            background: #3b82f6;
            color: white;
        }

        .btn-secondary {
            background: #9ca3af;
            color: white;
        }

        .btn-light {
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
        }

        .bg-success {
            background: #d1fae5;
            color: #065f46;
        }

        .bg-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .bg-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .bg-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .bg-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        /* Form Controls */
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.2);
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #4b5563;
        }

        /* Alerts */
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        /* Flex Utilities */
        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 8px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .text-end {
            text-align: right;
        }

        .text-white {
            color: white !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .overflow-hidden {
            overflow: hidden;
        }
    </style>
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
            <div style="margin-bottom: 10px; font-weight: 600; color: #374151;">
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