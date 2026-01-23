<?php
// controllers/MenuController.php

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
// Fetch Active Tags
$tags = [];
if (isset($conn)) {
    try {
        $stmt_tags = $conn->prepare("SELECT * FROM featured_tags WHERE is_active = 1 ORDER BY sort_order ASC");
        $stmt_tags->execute();
        $tags = $stmt_tags->fetchAll();
    } catch (PDOException $e) {
        $tags = [];
    }
}

// Fetch User Favorites (for heart status)
$user_favorites = [];
if (isset($_SESSION['user_id']) && isset($conn)) {
    $stmt_fav = $conn->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
    $stmt_fav->execute([$_SESSION['user_id']]);
    $user_favorites = $stmt_fav->fetchAll(PDO::FETCH_COLUMN);
}


// Fetch Products
// 1. Capture Filters
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$price_filters = isset($_GET['price']) ? $_GET['price'] : []; // Array of price ranges
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Capture specific IDs filter (for collections)
$ids_param = isset($_GET['ids']) ? $_GET['ids'] : '';
$ids_filter = [];
if (!empty($ids_param)) {
    $temp_ids = explode(',', $ids_param);
    foreach ($temp_ids as $tid) {
        $tid = intval(trim($tid));
        if ($tid > 0)
            $ids_filter[] = $tid;
    }
}

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

// IDs Filter
if (!empty($ids_filter)) {
    $where_clauses[] = "id IN (" . implode(',', $ids_filter) . ")";
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