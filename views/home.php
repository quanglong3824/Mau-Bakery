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
                <a href="#menu" class="btn-glass"
                    style="background: var(--accent-color); color: white; border: none;">Đặt bánh ngay</a>
                <a href="#menu" class="btn-glass" style="margin-left: 15px;">Xem Menu</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="glass-bg"
                style="position: absolute; width: 400px; height: 400px; background: rgba(255,255,255,0.2); border-radius: 50%; filter: blur(40px); z-index: -1;">
            </div>
            <img src="uploads/banh-kem-dau-tay.jpg" alt="Premium Berry Cake" class="hero-main-img">
        </div>
    </section>

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
            <a href="#" class="btn-glass" style="padding: 15px 40px;">Xem tất cả các loại bánh</a>
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