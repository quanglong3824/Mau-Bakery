<?php
require_once 'controllers/HomeController.php';
?>

<main>
    <!-- Hero Banner -->
    <section class="hero container">
        <div class="hero-content">
            <h1 class="hero-title">Vị Ngọt <br> <span>Hạnh Phúc</span></h1>
            <p class="hero-desc">Chào mừng đến với Mâu Bakery. Chúng tôi tạo ra những chiếc bánh kem thủ công
                tuyệt hảo, đánh thức mọi giác quan của bạn.</p>
            <div class="hero-btns">
                <a href="index.php?page=menu" class="btn-glass"
                    style="background: var(--accent-color); color: white; border: none;">Đặt bánh ngay</a>
                <a href="index.php?page=menu" class="btn-glass" style="margin-left: 15px;">Xem Menu</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="glass-bg"
                style="position: absolute; width: 400px; height: 400px; background: rgba(255,255,255,0.2); border-radius: 50%; filter: blur(40px); z-index: -1;">
            </div>
            <img src="uploads/banh-kem-dau-tay.jpg" alt="Premium Berry Cake" class="hero-main-img">
        </div>
    </section>

    <!-- Daily Suggestions Bar -->
    <?php
    $tags = [];
    if (isset($conn)) {
        try {
            $stmt_tags = $conn->prepare("SELECT * FROM featured_tags WHERE is_active = 1 ORDER BY sort_order ASC");
            $stmt_tags->execute();
            $tags = $stmt_tags->fetchAll();
        } catch (PDOException $e) {
            // Ignore error
        }
    }
    ?>
    <?php if (!empty($tags)): ?>
        <section class="container mt-2">
            <div class="glass-panel"
                style="padding: 15px 25px; display: flex; align-items: center; justify-content: center; background: rgba(255, 245, 238, 0.6); border: 1px solid rgba(255, 183, 197, 0.3);">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: center;">
                    <span style="color: #666; font-weight: 500; margin-right: 5px;">
                        <i class="fas fa-lightbulb" style="color: var(--accent-color);"></i> Gợi ý hôm nay:
                    </span>

                    <?php foreach ($tags as $tag): ?>
                        <a href="<?php echo $tag['url']; ?>" class="tag-pill">
                            <?php if ($tag['icon']): ?>
                                <i class="<?php echo $tag['icon']; ?>" style="margin-right: 5px;"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <style>
            .tag-pill {
                display: inline-block;
                padding: 8px 20px;
                background: #fff;
                border-radius: 50px;
                text-decoration: none;
                color: #555;
                font-size: 0.95rem;
                font-weight: 600;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid transparent;
            }

            .tag-pill:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                color: var(--accent-color);
                border-color: var(--accent-color);
            }
        </style>
    <?php endif; ?>

    <!-- Featured Products -->
    <section id="menu" class="container mt-2">
        <h2 class="section-title">Bánh Mới Ra Lò</h2>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="glass-panel product-card"
                    onclick="window.location.href='index.php?page=product_detail&id=<?php echo $product['id']; ?>'"
                    style="cursor: pointer;">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-img">
                    <h3 class="product-name">
                        <?php echo $product['name']; ?>
                    </h3>
                    <?php echo number_format($product['base_price'], 0, ',', '.'); ?>đ
                    <div class="product-actions">
                        <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>" class="btn-glass"
                            style="padding: 10px 20px; font-size: 0.9rem;">
                            <i class="fas fa-shopping-cart"></i> Mua ngay
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mb-2">
            <a href="index.php?page=menu" class="btn-glass" style="padding: 15px 40px;">Xem tất cả các loại bánh</a>
        </div>
    </section>

    <!-- Promotion Section -->
    <section class="container mt-2 mb-2">
        <div class="glass-panel"
            style="padding: 50px; display: flex; align-items: center; justify-content: space-between; overflow: hidden; position: relative;">
            <div style="flex: 1; z-index: 1;">
                <h2 style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 20px;">Ưu Đãi Đặc Biệt</h2>
                <p style="font-size: 1.1rem; margin-bottom: 30px;">Giảm giá 20% cho đơn hàng đầu tiên khi bạn đăng ký
                    thành viên ngay hôm nay.</p>
                <a href="auth/register.php" class="btn-glass btn-primary">Đăng Ký Ngay</a>
            </div>
            <div style="flex: 1; text-align: right; z-index: 1;">
                <img src="uploads/banh-kem-socola-kem-bong-lan.jpg"
                    style="width: 300px; border-radius: 20px; transform: rotate(10deg);" alt="Promotion">
            </div>

            <!-- Decor circle -->
            <div
                style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: var(--primary-color); border-radius: 50%; opacity: 0.5; filter: blur(30px);">
            </div>
        </div>
    </section>
</main>