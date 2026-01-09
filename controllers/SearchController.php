<?php
// controllers/SearchController.php

$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];

// Search Logic with Pagination
$items_per_page = 8;
$current_page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
if ($current_page < 1)
    $current_page = 1;
$offset = ($current_page - 1) * $items_per_page;
$total_items = 0;
$total_pages = 0;

if ($keyword && isset($conn)) {
    try {
        // Get Total Count
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name LIKE :keyword AND is_active = 1");
        $countStmt->execute(['keyword' => "%$keyword%"]);
        $total_items = $countStmt->fetchColumn();
        $total_pages = ceil($total_items / $items_per_page);

        // Get Paginated Results
        $searchStmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :keyword AND is_active = 1 LIMIT :limit OFFSET :offset");
        $searchStmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        $searchStmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
        $searchStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $searchStmt->execute();
        $results = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_msg = $e->getMessage();
    }
}
?>