<?php
// Get Product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$images = [];
$variants = [];
$related_products = [];

if (isset($conn) && $id > 0) {
    // 1. Fetch Product Info
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    if ($product) {
        // 2. Fetch Images
        $stmt_img = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = :id");
        $stmt_img->execute(['id' => $id]);
        $images = $stmt_img->fetchAll(PDO::FETCH_COLUMN);
        // Add main image to start of array if not empty, or just use as is. 
        // Logic: if images table empty, use main image. If not, use images table.
        if (empty($images) && !empty($product['image'])) {
            $images[] = $product['image'];
        } elseif (!empty($product['image'])) {
            array_unshift($images, $product['image']);
        }

        // 3. Fetch Variants
        $stmt_var = $conn->prepare("SELECT * FROM product_variants WHERE product_id = :id ORDER BY price ASC");
        $stmt_var->execute(['id' => $id]);
        $variants = $stmt_var->fetchAll();

        // 4. Fetch Related Products (same category)
        $stmt_rel = $conn->prepare("SELECT * FROM products WHERE category_id = :cat_id AND id != :id AND is_active = 1 LIMIT 4");
        $stmt_rel->execute(['cat_id' => $product['category_id'], 'id' => $id]);
        $related_products = $stmt_rel->fetchAll();

        // 5. Fetch Reviews
        $stmt_reviews = $conn->prepare("
            SELECT r.*, u.full_name, u.avatar 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = :pid AND r.is_active = 1 
            ORDER BY r.created_at DESC
        ");
        $stmt_reviews->execute(['pid' => $id]);
        $reviews = $stmt_reviews->fetchAll();
        $review_count = count($reviews);
    }
}

// Fallback if not found
if (!$product) {
    echo "<div class='container mt-2'><h3>Sản phẩm không tồn tại!</h3><a href='index.php'>Về trang chủ</a></div>";
    return;
}
?>

<!-- Load Specific Assets -->
<link rel="stylesheet" href="assets/css/product_detail.css">

<div class="container mt-2 mb-2">

    <!-- Breadcrumb -->
    <div style="margin-bottom: 20px; font-size: 0.9rem; color: #666;">
        <a href="index.php" style="color: #888;">Trang chủ</a> <i class="fas fa-chevron-right"
            style="font-size: 0.7rem; margin: 0 5px;"></i>
        <a href="index.php?page=menu" style="color: #888;">Thực đơn</a> <i class="fas fa-chevron-right"
            style="font-size: 0.7rem; margin: 0 5px;"></i>
        <span style="color: var(--accent-color); font-weight: 600;">
            <?php echo $product['name']; ?>
        </span>
    </div>

    <!-- Main Content Grid -->
    <!-- Main Content Grid -->
    <div class="glass-panel" style="padding: 40px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;" class="product-detail-layout">

            <!-- Left Column: Gallery -->
            <div class="product-gallery">
                <div class="main-image-container">
                    <img src="<?php echo !empty($images) ? $images[0] : 'assets/images/default-cake.jpg'; ?>"
                        id="main-image" class="main-image" alt="Main Product">

                    <!-- Wishlist absolute button -->
                    <button class="btn-glass"
                        style="position: absolute; top: 20px; right: 20px; border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; background: white; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <i class="far fa-heart" style="color: #ff6b6b; font-size: 1.2rem;"></i>
                    </button>
                </div>

                <div class="thumbnail-list">
                    <?php foreach ($images as $index => $img): ?>
                        <img src="<?php echo $img; ?>" class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                            alt="Thumb">
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Column: Info & Actions -->
            <div class="product-info">
                <input type="hidden" id="product-id" value="<?php echo $product['id']; ?>">
                <input type="hidden" id="product-name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" id="product-image"
                    value="<?php echo !empty($images) ? $images[0] : 'assets/images/default-cake.jpg'; ?>">

                <h1>
                    <?php echo $product['name']; ?>
                </h1>

                <div class="rating">
                    <?php for ($i = 0; $i < 5; $i++)
                        echo '<i class="fas fa-star"></i>'; ?>
                    <span style="color: #666; font-size: 0.9rem; margin-left: 10px;">(
                        <?php echo $product['views']; ?> lượt xem)
                    </span>
                </div>

                <div class="product-price">
                    <span id="price-display" data-base-price="<?php echo $product['base_price']; ?>">
                        <?php echo number_format($product['base_price'], 0, ',', '.'); ?>đ
                    </span>
                    <!-- <span class="original-price">400.000đ</span> -->
                </div>

                <p style="margin-bottom: 30px; line-height: 1.8; color: #555;">
                    <?php echo $product['description']; ?>
                </p>

                <!-- Configuration Options -->
                <?php if (!empty($variants)): ?>
                    <div class="option-group">
                        <span class="option-label">Kích thước bánh:</span>
                        <div class="size-options">
                            <?php foreach ($variants as $idx => $var): ?>
                                <button class="size-btn <?php echo $idx === 0 ? 'active' : ''; ?>"
                                    data-price="<?php echo $var['price']; ?>">
                                    <?php echo $var['size_name']; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="option-group">
                    <span class="option-label">Số lượng:</span>
                    <div class="quantity-wrapper">
                        <button class="qty-btn btn-minus"><i class="fas fa-minus"></i></button>
                        <input type="text" value="1" class="qty-input" id="qty-input" readonly>
                        <button class="qty-btn btn-plus"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <!-- Note Input -->
                <div class="option-group">
                    <span class="option-label">Lời nhắn trên bánh (Miễn phí):</span>
                    <textarea class="glass-panel"
                        style="width: 100%; padding: 15px; border-radius: 15px; border: 1px solid rgba(0,0,0,0.1); outline: none; font-family: inherit; font-size: 0.9rem;"
                        rows="2" placeholder="VD: Happy Birthday Annie..."></textarea>
                </div>

                <div class="action-buttons">
                    <button class="btn-add-cart">
                        <i class="fas fa-shopping-bag"></i> Thêm Vào Giỏ
                    </button>
                    <a href="index.php?page=checkout" class="btn-buy-now">
                        Mua Ngay
                    </a>
                </div>

                <!-- Policy Trust -->
                <div style="margin-top: 30px; display: flex; gap: 20px; font-size: 0.85rem; color: #666;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-shipping-fast" style="color: var(--accent-color);"></i> Giao hàng 2h
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-shield-alt" style="color: var(--accent-color);"></i> Bảo đảm tươi ngon
                    </div>
                </div>

            </div>
        </div>

        <!-- Description Tabs -->
        <div class="tabs-container">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="desc">Chi Tiết Sản Phẩm</button>
                <button class="tab-btn" data-tab="ingredients">Thành Phần</button>
                <button class="tab-btn" data-tab="reviews">Đánh Giá (
                    <?php echo isset($review_count) ? $review_count : 0; ?>)
                </button>
            </div>

            <div class="tab-content active" id="desc">
                <?php echo $product['description']; ?>
            </div>

            <div class="tab-content" id="ingredients">
                <p>Thông tin nguyên liệu hiện chưa cập nhật.</p>
            </div>

            <div class="tab-content" id="reviews">
                <?php if (empty($reviews)): ?>
                    <div style="padding: 30px; text-align: center; color: #888;">
                        <i class="far fa-comment-dots" style="font-size: 2rem; margin-bottom: 10px; color: #ddd;"></i>
                        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                    </div>
                <?php else: ?>
                    <div class="review-list">
                        <?php foreach ($reviews as $rv): ?>
                            <div class="review-item"
                                style="border-bottom: 1px solid #eee; padding: 20px 0; display: flex; gap: 20px;">
                                <img src="<?php echo !empty($rv['avatar']) ? $rv['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($rv['full_name']); ?>"
                                    alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">

                                <div class="review-content" style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <h4 style="font-size: 1rem; font-weight: 700; color: #444;">
                                            <?php echo htmlspecialchars($rv['full_name']); ?></h4>
                                        <span
                                            style="font-size: 0.85rem; color: #999;"><?php echo date('d/m/Y', strtotime($rv['created_at'])); ?></span>
                                    </div>

                                    <div class="stars" style="color: #FFD700; font-size: 0.9rem; margin-bottom: 8px;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?php echo $i <= $rv['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>

                                    <p style="color: #666; line-height: 1.5; font-size: 0.95rem;">
                                        <?php echo nl2br(htmlspecialchars($rv['comment'])); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <h2 class="section-title" style="margin-top: 60px;">Có Thể Bạn Thích</h2>
    <div class="product-grid">
        <?php foreach ($related_products as $prod): ?>
            <div class="glass-panel product-card"
                onclick="window.location.href='index.php?page=product_detail&id=<?php echo $prod['id']; ?>'"
                style="cursor: pointer;">
                <img src="<?php echo $prod['image']; ?>" alt="<?php echo $prod['name']; ?>" class="product-img">
                <h3 class="product-name">
                    <?php echo $prod['name']; ?>
                </h3>
                <p class="product-price">
                    <?php echo number_format($prod['base_price'], 0, ',', '.'); ?>đ
                </p>
                <div class="product-actions">
                    <a href="index.php?page=product_detail&id=<?php echo $prod['id']; ?>" class="btn-glass"
                        style="padding: 10px 20px; font-size: 0.9rem;">
                        <i class="fas fa-shopping-cart"></i> Mua ngay
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Load Specific JS -->
<script src="assets/js/product_detail.js"></script>