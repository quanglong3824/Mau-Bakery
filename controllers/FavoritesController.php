<?php
// controllers/FavoritesController.php

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&redirect=favorites");
    exit;
}

$user_id = $_SESSION['user_id'];
$products = [];

if (isset($conn)) {
    try {
        $sql = "
            SELECT p.* 
            FROM products p
            JOIN favorites f ON p.id = f.product_id
            WHERE f.user_id = :uid AND p.is_active = 1
            ORDER BY f.created_at DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $user_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Since we are on favorites page, all these are favorited by definition
        $user_favorites = array_column($products, 'id');

    } catch (PDOException $e) {
        $products = [];
    }
}