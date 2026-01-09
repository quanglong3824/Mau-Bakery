<?php
// controllers/ProductDetailController.php

// Get Product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$images = [];
$variants = [];
$related_products = [];
$reviews = [];
$review_count = 0;

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
?>