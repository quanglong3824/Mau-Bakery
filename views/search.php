<?php
require_once 'controllers/SearchController.php';
?>

<div class="container mt-2 mb-2">
    <div class="glass-panel" style="margin-bottom: 30px; text-align: center;">
        <h1 style="font-family: 'Quicksand', sans-serif; margin-bottom: 20px;">Tìm kiếm</h1>
        <form action="index.php" method="GET" style="max-width: 500px; margin: 0 auto; position: relative;">
            <input type="hidden" name="page" value="search">
            <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>"
                placeholder="Bạn muốn tìm bánh gì hôm nay?..."
                style="width: 100%; padding: 15px 20px 15px 50px; border-radius: 30px; border: 1px solid rgba(0,0,0,0.1); font-size: 1rem; outline: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
            <button type="submit"
                style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.2rem; color: #888; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <?php if ($keyword): ?>
        <?php if (isset($error_msg))
            echo "<p class='text-danger'>Lỗi: $error_msg</p>"; ?>


        <p style="margin-bottom: 20px; color: #666;">Kết quả tìm kiếm cho:
            <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong> (<?php echo $total_items; ?> sản phẩm)
        </p>

        <?php if (count($results) > 0): ?>
            <div class="product-grid">
                <?php foreach ($results as $product): ?>
                    <div class="glass-panel product-card"
                        onclick="window.location.href='index.php?page=product_detail&id=<?php echo $product['id']; ?>'"
                        style="cursor: pointer;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                        <h3 class="product-name">
                            <?php echo htmlspecialchars($product['name']); ?>
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

            <!-- Pagination Controls -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-container" style="display: flex; justify-content: center; gap: 10px; margin-top: 30px;">
                    <?php if ($current_page > 1): ?>
                        <a href="index.php?page=search&q=<?php echo urlencode($keyword); ?>&p=<?php echo $current_page - 1; ?>"
                            class="btn-glass" style="padding: 10px 20px;">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="index.php?page=search&q=<?php echo urlencode($keyword); ?>&p=<?php echo $i; ?>" class="btn-glass"
                            style="padding: 10px 20px; <?php echo ($i === $current_page) ? 'background: var(--accent-color); color: white;' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="index.php?page=search&q=<?php echo urlencode($keyword); ?>&p=<?php echo $current_page + 1; ?>"
                            class="btn-glass" style="padding: 10px 20px;">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center" style="padding: 50px; color: #888;">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                <p>Hãy thử từ khóa khác như "Kem", "Dâu", "Sinh nhật"...</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>