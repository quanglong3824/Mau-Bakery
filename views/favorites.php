<?php
require_once 'controllers/FavoritesController.php';
?>

<div class="container mt-2 mb-2">
    <h1 class="section-title">Sản Phẩm Yêu Thích</h1>

    <?php if (empty($favorites)): ?>
        <div class="text-center glass-panel" style="padding: 50px;">
            <i class="far fa-heart" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
            <p style="font-size: 1.1rem; color: #666;">Bạn chưa có sản phẩm yêu thích nào.</p>
            <a href="index.php?page=menu" class="btn-glass btn-primary" style="margin-top: 20px;">Khám phá ngay</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($favorites as $product): ?>
                <div class="product-card glass-panel" style="padding: 0; overflow: hidden; position: relative;">

                    <!-- Remove Button/Icon -->
                    <span class="btn-remove-fav" data-id="<?php echo $product['id']; ?>"
                        style="position: absolute; top: 10px; right: 10px; background: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; color: #e74c3c; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 10;">
                        <i class="fas fa-heart"></i>
                    </span>

                    <div class="product-image"
                        style="height: 200px; background: #f9f9f9; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-image fa-3x" style="color: #ccc;"></i>
                        <?php endif; ?>
                    </div>

                    <div class="product-info" style="padding: 15px;">
                        <!-- Rating Mock for now, or dynamic if review table exists -->
                        <div style="color: #FFD700; font-size: 0.8rem; margin-bottom: 5px;">
                            <?php
                            $rating = 5; // Default or fetch
                            for ($i = 0; $i < floor($rating); $i++)
                                echo '<i class="fas fa-star"></i>';
                            ?>
                        </div>

                        <h3
                            style="font-size: 1.1rem; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <a
                                href="index.php?page=product_detail&id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                        </h3>

                        <p style="color: var(--accent-color); font-weight: 700; margin-bottom: 15px;">
                            <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                        </p>

                        <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>" class="btn-glass btn-primary"
                            style="width: 100%; text-align: center; display: block; border-radius: 10px;">Xem chi tiết</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/favorites.js"></script>