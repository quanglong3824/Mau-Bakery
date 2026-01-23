<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mâu Bakery - Tiệm Bánh Ngọt Ngào</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hero_enhanced.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/cart_badge.css">
</head>

<body>
    <!-- Mini Top Status Bar -->
    <div class="top-bar">
        <div class="container top-bar-content">
            <div class="top-bar-left">
                <span><i class="fas fa-phone-alt"></i> 090 123 4567</span>
                <span><i class="fas fa-envelope"></i> hello@maubakery.com</span>
            </div>
            <div class="top-bar-right">
                <a href="#">Trợ giúp</a>
                <a href="#">Tin tức</a>
                <span style="display: flex; gap: 10px;">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </span>
            </div>
        </div>
    </div>

    <header>
        <div class="container">
            <nav class="glass-panel" style="padding: 15px 30px; border-radius: 50px;">
                <div class="logo">
                    <i class="fas fa-birthday-cake"></i> Mâu Bakery
                </div>

                <?php
                // Fetch Categories for Header Menu
                $header_categories = [];
                if (isset($conn)) {
                    try {
                        $stmt_cat_header = $conn->prepare("SELECT * FROM categories WHERE is_active = 1 LIMIT 5");
                        $stmt_cat_header->execute();
                        $header_categories = $stmt_cat_header->fetchAll();
                    } catch (PDOException $e) {
                        // Silent fail
                    }
                }
                ?>
                <ul class="nav-links">
                    <li><a href="index.php">Trang chủ</a></li>

                    <!-- Menu Dropdown -->
                    <li>
                        <a href="index.php?page=menu">Thực đơn <i class="fas fa-chevron-down"
                                style="font-size: 0.7rem; margin-left: 5px;"></i></a>
                        <ul class="dropdown-menu">
                            <?php if (!empty($header_categories)): ?>
                                <?php foreach ($header_categories as $cat): ?>
                                    <li>
                                        <a href="index.php?page=menu&category=<?php echo $cat['slug']; ?>">
                                            <i class="fas fa-birthday-cake"></i> <?php echo $cat['name']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li>
                                <a href="index.php?page=menu">
                                    <i class="fas fa-utensils"></i> Xem tất cả
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Blog & Promo Dropdown -->
                    <li>
                        <a href="#">Góc Bánh Ngọt <i class="fas fa-chevron-down"
                                style="font-size: 0.7rem; margin-left: 5px;"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?page=vouchers"><i class="fas fa-tags"></i> Khuyến mãi</a></li>
                            <li><a href="index.php?page=blog"><i class="fas fa-newspaper"></i> Blog chia sẻ</a></li>
                        </ul>
                    </li>

                    <!-- About Dropdown -->
                    <li>
                        <a href="#">Về Chúng Tôi <i class="fas fa-chevron-down"
                                style="font-size: 0.7rem; margin-left: 5px;"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?page=about"><i class="fas fa-store"></i> Câu chuyện</a></li>
                            <li><a href="index.php?page=contact"><i class="fas fa-envelope"></i> Liên hệ</a></li>
                            <!-- <li><a href="index.php?page=faq"><i class="fas fa-question-circle"></i> FAQ</a></li> -->
                        </ul>
                    </li>
                </ul>

                <div class="nav-icons">
                    <a href="index.php?page=favorites" title="Yêu thích"><i class="far fa-heart"></i></a>
                    <a href="index.php?page=search"><i class="fas fa-search"></i></a>
                    <div class="user-actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <?php
                                $cart_count = 0;
                                if (isset($_SESSION['cart'])) {
                                    foreach ($_SESSION['cart'] as $item) {
                                        $cart_count += $item['quantity'];
                                    }
                                }
                                ?>
                                <a href="index.php?page=cart" class="icon-link shopping-cart-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                                </a>

                                <!-- Authenticated User Dropdown -->
                                <div class="user-dropdown-group" style="position: relative;">
                                    <a href="#"
                                        style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-color);">
                                        <div
                                            style="width: 35px; height: 35px; background: var(--accent-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span style="font-size: 0.9rem; font-weight: 600;" class="hide-on-mobile">
                                            <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
                                        </span>
                                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; color: #aaa;"></i>
                                    </a>

                                    <ul class="dropdown-menu user-menu">
                                        <li>
                                            <a href="index.php?page=profile">
                                                <i class="far fa-user-circle"></i> Tài khoản của tôi
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.php?page=profile&tab=orders">
                                                <i class="fas fa-shopping-bag"></i> Đơn mua
                                            </a>
                                        </li>
                                        <li
                                            style="border-top: 1px solid rgba(0,0,0,0.05); margin-top: 5px; padding-top: 5px;">
                                            <a href="auth/logout.php" style="color: #e74c3c;">
                                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php
                            $cart_count = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cart_count += $item['quantity'];
                                }
                            }
                            ?>
                            <a href="index.php?page=cart" class="icon-link shopping-cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge"><?php echo $cart_count; ?></span>
                            </a>
                            <a href="auth/login.php" class="btn-login"
                                style="margin-left: 15px; padding: 5px 15px; color: white; background: var(--accent-color); border-radius: 15px; text-decoration: none; font-size: 0.9rem;">
                                Đăng nhập
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-btn glass-panel">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </nav>
        </div>

        <!-- Mobile Menu Overlay (FAB Stack Container) -->
        <div class="mobile-menu-overlay">
            <div class="mobile-menu-items">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logout -->
                    <a href="auth/logout.php" class="fab-item">
                        <span class="fab-label">Đăng xuất</span>
                        <div class="fab-icon-circle" style="background: #e74c3c;"><i class="fas fa-sign-out-alt"></i></div>
                    </a>
                    <!-- Profile -->
                    <a href="index.php?page=profile" class="fab-item">
                        <span class="fab-label">Tài khoản</span>
                        <div class="fab-icon-circle"><i class="fas fa-user-circle"></i></div>
                    </a>
                <?php else: ?>
                    <!-- Login -->
                    <a href="auth/login.php" class="fab-item">
                        <span class="fab-label">Đăng nhập</span>
                        <div class="fab-icon-circle"><i class="fas fa-sign-in-alt"></i></div>
                    </a>
                <?php endif; ?>

                <!-- Contact -->
                <a href="index.php?page=contact" class="fab-item">
                    <span class="fab-label">Liên hệ</span>
                    <div class="fab-icon-circle"><i class="fas fa-phone-alt"></i></div>
                </a>

                <!-- About -->
                <a href="index.php?page=about" class="fab-item">
                    <span class="fab-label">Về chúng tôi</span>
                    <div class="fab-icon-circle"><i class="fas fa-store"></i></div>
                </a>

                <!-- Blog -->
                <a href="index.php?page=blog" class="fab-item">
                    <span class="fab-label">Blog</span>
                    <div class="fab-icon-circle"><i class="fas fa-newspaper"></i></div>
                </a>

                <!-- Vouchers -->
                <a href="index.php?page=vouchers" class="fab-item">
                    <span class="fab-label">Khuyến mãi</span>
                    <div class="fab-icon-circle"><i class="fas fa-tags"></i></div>
                </a>

                <!-- Menu -->
                <a href="index.php?page=menu" class="fab-item">
                    <span class="fab-label">Thực đơn</span>
                    <div class="fab-icon-circle"><i class="fas fa-birthday-cake"></i></div>
                </a>

                <!-- Home -->
                <a href="index.php" class="fab-item">
                    <span class="fab-label">Trang chủ</span>
                    <div class="fab-icon-circle"><i class="fas fa-home"></i></div>
                </a>
            </div>
        </div>
    </header>

    <!-- Script to handle sticky header and mobile menu -->
    <!-- Script to handle sticky header and mobile menu -->
    <script>
        window.addEventListener('scroll', function () {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Mobile Menu Logic
        const mobileBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu-overlay');

        function toggleMenu() {
            const isActive = mobileMenu.classList.toggle('active');
            mobileBtn.classList.toggle('active');

            // Optional: Toggle body scroll
            if (isActive) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Toggle button click
        mobileBtn.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent document click from firing immediately
            toggleMenu();
        });

        // Close when clicking overlay (backdrop)
        mobileMenu.addEventListener('click', function (event) {
            if (event.target === mobileMenu) {
                toggleMenu();
            }
        });

        // Also close when pressing Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && mobileMenu.classList.contains('active')) {
                toggleMenu();
            }
        });
    </script>