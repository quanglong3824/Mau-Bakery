<?php
require_once 'controllers/FavoritesController.php';
?>

<div class="container mt-2 mb-2">
    <div class="text-center mb-5">
        <h1 class="section-title">Sản Phẩm Yêu Thích</h1>
        <p class="mb-1">Danh sách những món bánh bạn đã lưu lại.</p>
    </div>

    <!-- Product Grid (Reusing Menu Styles) -->
    <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px;">
        <?php if (empty($products)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <i class="far fa-heart" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                <p style="color: #666; font-size: 1.1rem;">Bạn chưa có sản phẩm yêu thích nào.</p>
                <a href="index.php?page=menu" class="btn-glass btn-primary"
                    style="margin-top: 20px; display: inline-block;">Khám phá Menu ngay</a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="glass-panel product-card"
                    onclick="window.location.href='index.php?page=product_detail&id=<?php echo $product['id']; ?>'"
                    style="cursor: pointer;">
                    <div style="position: relative;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="product-img" loading="lazy"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <!-- Heart Icon (Always Red here initially) -->
                        <div class="favorite-btn" onclick="toggleFavorite(event, <?php echo $product['id']; ?>)"
                            style="position: absolute; top: 10px; right: 10px; width: 35px; height: 35px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s;">
                            <i class="fas fa-heart" style="color: #e74c3c; font-size: 1.1rem;"></i>
                        </div>
                    </div>
                    <h3 class="product-name" style="font-size: 1.1rem;">
                        <?php echo $product['name']; ?>
                    </h3>

                    <div style="color: #FFD700; font-size: 0.9rem; margin-bottom: 5px;">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>

                    <p class="product-price">
                        <?php echo number_format($product['base_price'], 0, ',', '.'); ?>đ
                    </p>

                    <div class="product-actions" style="margin-top: 15px;">
                        <button
                            onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['base_price']; ?>); event.stopPropagation();"
                            class="btn-glass"
                            style="padding: 8px 15px; width: 100%; font-size: 0.9rem; border-radius: 50px; display: flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer;">
                            Thêm vào giỏ <i class="fas fa-plus" style="font-size: 0.8rem; margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/favorites.js"></script>