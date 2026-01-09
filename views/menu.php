<?php
$categories = [];
if (isset($conn)) {
    try {
        $stmt_cat = $conn->prepare("SELECT * FROM categories WHERE is_active = 1");
        $stmt_cat->execute();
        $categories = $stmt_cat->fetchAll();
    } catch (PDOException $e) {
        $categories = [];
    }
}

// Fetch Products
// 1. Capture Filters
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$price_filters = isset($_GET['price']) ? $_GET['price'] : []; // Array of price ranges
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// 2. Resolve Category Slug to ID
$category_id = null;
if ($category_slug && !empty($categories)) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $category_slug) {
            $category_id = $cat['id'];
            break;
        }
    }
}

// 3. Build Query
$where_clauses = ["is_active = 1"];
$params = [];

// Keyword
if ($keyword) {
    $where_clauses[] = "name LIKE :keyword";
    $params['keyword'] = "%$keyword%";
}

// Category
if ($category_id) {
    $where_clauses[] = "category_id = :cat_id";
    $params['cat_id'] = $category_id;
}

// Price Ranges
if (!empty($price_filters)) {
    $price_conditions = [];
    foreach ($price_filters as $range) {
        if ($range == 'under100')
            $price_conditions[] = "base_price < 100000";
        if ($range == '100-300')
            $price_conditions[] = "base_price BETWEEN 100000 AND 300000";
        if ($range == '300-500')
            $price_conditions[] = "base_price BETWEEN 300000 AND 500000";
        if ($range == 'above500')
            $price_conditions[] = "base_price > 500000";
    }
    if (!empty($price_conditions)) {
        $where_clauses[] = "(" . implode(" OR ", $price_conditions) . ")";
    }
}

// Sorting
$order_by = "created_at DESC";
if ($sort == 'price_asc')
    $order_by = "base_price ASC";
if ($sort == 'price_desc')
    $order_by = "base_price DESC";
if ($sort == 'name_asc')
    $order_by = "name ASC";
// Note: 'rating' sort requires joining reviews, skipping for simplicity or can add later.

$sql_where = implode(" AND ", $where_clauses);

// 4. Fetch Products
$products = [];
$total_products = 0;

if (isset($conn)) {
    try {
        // Count Total
        $stmt_count = $conn->prepare("SELECT COUNT(*) FROM products WHERE $sql_where");
        $stmt_count->execute($params);
        $total_products = $stmt_count->fetchColumn();

        // Fetch Data
        $sql = "SELECT * FROM products WHERE $sql_where ORDER BY $order_by LIMIT $limit OFFSET $offset";
        $stmt_prod = $conn->prepare($sql);
        // Bind limit/offset manually or simply execute params only (PDO limitation with named params in LIMIT)
        // Actually, better to bind all params. LIMIT/OFFSET are integers.
        foreach ($params as $key => $val) {
            $stmt_prod->bindValue(":$key", $val);
        }
        // $stmt_prod->bindValue(':limit', $limit, PDO::PARAM_INT);
        // $stmt_prod->bindValue(':offset', $offset, PDO::PARAM_INT);
        // Direct execute with params works, but limit/offset must be injected or bindValue used separately.
        // Let's use string interpolation for LIMIT/OFFSET since they are sanitized ints.
        // Re-preparing with safe ints
        $sql = "SELECT * FROM products WHERE $sql_where ORDER BY $order_by LIMIT $limit OFFSET $offset";
        $stmt_prod = $conn->prepare($sql);
        $stmt_prod->execute($params);

        $products = $stmt_prod->fetchAll();
    } catch (PDOException $e) {
        $products = [];
        // debug: echo $e->getMessage();
    }
}

$total_pages = ceil($total_products / $limit);
?>

<div class="container mt-2 mb-2">
    <!-- Page Header & Suggestions (Carousel-like) -->
    <div class="text-center mb-2">
        <h1 class="section-title">Thực Đơn Của Chúng Tôi</h1>
        <p class="mb-1">Khám phá thế giới ngọt ngào với hơn 10+ loại bánh được làm mới mỗi ngày.</p>

        <!-- Suggestions Tags -->
        <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-bottom: 30px;">
            <span style="font-size: 0.9rem; color: #666; display: flex; align-items: center;"><i
                    class="fas fa-lightbulb" style="color: var(--accent-color); margin-right: 5px;"></i> Gợi ý hôm
                nay:</span>
            <a href="#" class="btn-glass" style="padding: 5px 15px; font-size: 0.85rem; border-radius: 15px;">Ít
                ngọt</a>
            <a href="#" class="btn-glass"
                style="padding: 5px 15px; font-size: 0.85rem; border-radius: 15px;">Healthy</a>
            <a href="#" class="btn-glass" style="padding: 5px 15px; font-size: 0.85rem; border-radius: 15px;">Best
                Seller</a>
            <a href="#" class="btn-glass" style="padding: 5px 15px; font-size: 0.85rem; border-radius: 15px;">Mùa dâu
                tây</a>
        </div>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 30px;">
        <!-- Sidebar Filters Form -->
        <form method="GET" action="index.php" id="filterForm">
            <input type="hidden" name="page" value="menu">
            <!-- Keep category if set -->
            <?php if ($category_slug): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_slug); ?>">
            <?php endif; ?>

            <div class="glass-panel" style="padding: 25px; position: sticky; top: 100px;">
                <!-- Search -->
                <div style="margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; font-size: 1.2rem;">Tìm kiếm</h3>
                    <div style="position: relative;">
                        <input type="text" name="keyword" placeholder="Tìm tên bánh..."
                            value="<?php echo htmlspecialchars($keyword); ?>"
                            style="width: 100%; padding: 10px 10px 10px 40px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.6); background: rgba(255,255,255,0.5); outline: none;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #888; font-size: 0.9rem;"></i>
                    </div>
                </div>

                <!-- Categories -->
                <div style="margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; font-size: 1.2rem;">Danh Mục</h3>
                    <ul style="display: flex; flex-direction: column; gap: 10px;">
                        <!-- All Categories -->
                        <li>
                            <a href="index.php?page=menu"
                                style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; transition: all 0.2s; background: <?php echo empty($category_slug) ? 'rgba(255,255,255,0.6)' : 'transparent'; ?>">
                                <span>Tất cả bánh</span>
                            </a>
                        </li>
                        <!-- DB Categories -->
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="index.php?page=menu&category=<?php echo $cat['slug']; ?>"
                                    style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; transition: all 0.2s; background: <?php echo ($category_slug == $cat['slug']) ? 'rgba(255,255,255,0.6)' : 'transparent'; ?>">
                                    <span><?php echo $cat['name']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Price Filter -->
                <div style="margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; font-size: 1.2rem;">Khoảng Giá</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="price[]" value="under100" style="margin-right: 10px;" <?php echo in_array('under100', $price_filters) ? 'checked' : ''; ?>> Dưới 100.000đ
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="price[]" value="100-300" style="margin-right: 10px;" <?php echo in_array('100-300', $price_filters) ? 'checked' : ''; ?>> 100.000đ - 300.000đ
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="price[]" value="300-500" style="margin-right: 10px;" <?php echo in_array('300-500', $price_filters) ? 'checked' : ''; ?>> 300.000đ - 500.000đ
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="price[]" value="above500" style="margin-right: 10px;" <?php echo in_array('above500', $price_filters) ? 'checked' : ''; ?>> Trên 500.000đ
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-glass btn-primary"
                    style="width: 100%; border: none; cursor: pointer;">Áp dụng bộ lọc</button>
            </div>
        </form>
        </aside>

        <!-- Main Content -->
        <div style="flex: 3; min-width: 300px;">
            <!-- Sort & Results Count -->
            <div class="glass-panel"
                style="padding: 15px 25px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; border-radius: 15px;">
                <span>Hiển thị <strong><?php echo count($products); ?></strong> trong
                    <strong><?php echo $total_products; ?></strong> sản phẩm</span>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span>Sắp xếp:</span>
                    <!-- Sort triggers form submit via JS or just simple GET link handling but better inside the status -->
                    <!-- Simpler: Use form outside or JS onchange to redirect/submit -->
                    <!-- We will use JS to update a hidden input in the form or redirect -->
                    <select onchange="window.location.href=updateQueryString('sort', this.value)"
                        style="padding: 8px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.6); background: rgba(255,255,255,0.5); outline: none;">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao
                        </option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá: Cao đến
                            Thấp</option>
                        <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Tên: A-Z</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
                <?php if (empty($products)): ?>
                    <p style="grid-column: 1/-1; text-align: center; color: #888; padding: 40px;">Không tìm thấy sản phẩm
                        nào phù hợp.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="glass-panel product-card"
                            onclick="window.location.href='index.php?page=product_detail&id=<?php echo $product['id']; ?>'"
                            style="cursor: pointer;">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="product-img" loading="lazy"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="javascript:void(0)" onclick="window.location.href=updateQueryString('p', <?php echo $i; ?>)"
                            class="btn-glass <?php echo $i == $page ? 'btn-primary' : ''; ?>"
                            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0; text-decoration: none; <?php echo $i == $page ? 'border: none;' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Helper to update URL params
    function updateQueryString(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value);
        if (key !== 'p') url.searchParams.set('p', 1); // Reset to page 1 on filter change
        return url.toString();
    }
</script>