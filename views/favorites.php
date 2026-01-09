<?php
// views/favorites.php

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-2 text-center'><div class='glass-panel' style='padding:50px;'><h3>Vui lòng đăng nhập để xem danh sách yêu thích</h3><a href='auth/login.php' class='btn-glass mt-1'>Đăng nhập</a></div></div>";
    return;
}

$user_id = $_SESSION['user_id'];
$favorites = [];

if (isset($conn)) {
    try {
        // Fetch Favorites Joined with Products
        $sql = "SELECT p.*, f.created_at as liked_at 
                FROM favorites f 
                JOIN products p ON f.product_id = p.id 
                WHERE f.user_id = :uid 
                ORDER BY f.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $user_id]);
        $favorites = $stmt->fetchAll();
    } catch (PDOException $e) {
        $favorites = [];
    }
}
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

                    <div class="product-image" style="height: 200px; background: #f9f9f9; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if(!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-image fa-3x" style="color: #ccc;"></i>
                        <?php endif; ?>
                    </div>

                    <div class="product-info" style="padding: 15px;">
                        <!-- Rating Mock for now, or dynamic if review table exists -->
                        <div style="color: #FFD700; font-size: 0.8rem; margin-bottom: 5px;">
                            <?php 
                            $rating = 5; // Default or fetch
                            for ($i = 0; $i < floor($rating); $i++) echo '<i class="fas fa-star"></i>'; 
                            ?>
                        </div>
                        
                        <h3 style="font-size: 1.1rem; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
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

<script>
    // Simple Remove Logic
    document.querySelectorAll('.btn-remove-fav').forEach(btn => {
        btn.addEventListener('click', function() {
            if(!confirm('Bỏ sản phẩm này khỏi danh sách yêu thích?')) return;
            
            const productId = this.getAttribute('data-id');
            const card = this.closest('.product-card');
            
            // Call API to remove
            fetch('api/toggle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'removed') {
                    card.remove();
                    // Reload if empty
                    if(document.querySelectorAll('.product-card').length === 0) location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                // Fallback for demo if API not exists yet
                card.style.display = 'none';
                alert('Đã xóa (Demo)');
            });
        });
    });
</script>